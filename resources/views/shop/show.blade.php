@extends('layouts.app')

@section('title', $product['name'] ?? 'Product Details')

@section('content')
<div class="container py-5">
    <div class="row" id="product-container">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadProductDetails();
});

function loadProductDetails() {
    const slug = '{{ $slug }}';
    const token = getToken();
    const url = `/api/v1/products/${slug}`;

    $.ajax({
        url: url,
        method: 'GET',
        headers: token ? { 'Authorization': 'Bearer ' + token } : {},
        success: function(response) {
            displayProduct(response.data);
        },
        error: function() {
            $('#product-container').html(`
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Failed to load product details.
                    </div>
                </div>
            `);
        }
    });
}

function displayProduct(product) {
    const container = $('#product-container');
    container.empty();

    const html = `
        <div class="col-lg-8 mx-auto">
            <div class="card product-card">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="display-6 fw-bold">${product.name}</h1>
                            <p class="text-muted">SKU: ${product.sku || 'N/A'}</p>
                        </div>
                        <span class="stock-badge ${product.stock > 0 ? 'stock-instock' : 'stock-outofstock'}">
                            ${product.stock > 0 ? 'In Stock (' + product.stock + ')' : 'Out of Stock'}
                        </span>
                    </div>

                    <!-- Categories -->
                    <div class="mb-4">
                        ${product.categories.map(cat => `
                            <span class="product-category-badge ${cat.visibility}">
                                ${cat.name}
                                ${cat.visibility === 'protected' ? '<i class="fas fa-lock ms-1"></i>' : ''}
                            </span>
                        `).join('')}
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        <h2 class="product-price">$${parseFloat(product.price).toFixed(2)}</h2>
                        ${getPriceNote(product)}
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p class="text-muted">${product.description || 'No description available.'}</p>
                    </div>

                    <!-- Variations -->
                    ${product.is_variable ? renderVariations(product.variations) : ''}

                    <!-- Actions -->
                    <div class="d-flex gap-3">
                        <button class="btn btn-primary btn-lg flex-grow-1" ${product.stock <= 0 ? 'disabled' : ''}>
                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                        <button class="btn btn-outline-primary btn-lg">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.html(html);
}

function getPriceNote(product) {
    const token = getToken();
    if (!token) {
        return `
            <p class="text-muted small mt-2">
                <i class="fas fa-info-circle me-1"></i>
                <a href="/login" class="text-decoration-none">Login</a> for member pricing
            </p>
        `;
    }

    const userRole = '{{ Auth::user() ? Auth::user()->role : '' }}';
    if (userRole === 'gold') {
        return `
            <p class="text-success small mt-2">
                <i class="fas fa-crown me-1"></i>
                Gold member price applied
            </p>
        `;
    } else if (userRole === 'silver') {
        return `
            <p class="text-info small mt-2">
                <i class="fas fa-star me-1"></i>
                Silver member price applied
            </p>
        `;
    }
    return '';
}

function renderVariations(variations) {
    if (!variations || variations.length === 0) return '';

    return `
        <div class="mb-4">
            <h5>Available Options</h5>
            <div class="row g-3">
                ${variations.map(variation => `
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="card-title">${Object.entries(variation.attributes).map(([key, value]) =>
                                    `${key}: ${value}`
                                ).join(' - ')}</h6>
                                <p class="card-text">
                                    <span class="fw-bold text-primary">$${parseFloat(variation.price).toFixed(2)}</span>
                                    <span class="stock-badge ${variation.stock > 0 ? 'stock-instock' : 'stock-outofstock'} ms-2">
                                        ${variation.stock > 0 ? variation.stock + ' in stock' : 'Out of stock'}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}
</script>
@endpush
