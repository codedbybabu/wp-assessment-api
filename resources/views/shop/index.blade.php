@extends('layouts.app')

@section('title', 'Shop')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold">Our Products</h1>
            <p class="text-muted">Discover our collection of quality products</p>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search products..." id="search-input">
                <button class="btn btn-primary" type="button" id="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Categories Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2" id="categories-container">
                <button class="btn btn-outline-primary active" data-category="all">All Products</button>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row" id="products-container">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-12">
            <nav aria-label="Product pagination">
                <ul class="pagination" id="pagination-container">
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Product Card Template (Hidden) -->
<template id="product-card-template">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card product-card h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold product-name"></h5>
                <div class="product-categories mb-2"></div>
                <p class="card-text text-muted small product-excerpt"></p>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="product-price"></span>
                    <span class="stock-badge product-stock"></span>
                </div>
                <div class="mt-3">
                    <a href="#" class="btn btn-primary w-100 view-product-btn">
                        <i class="fas fa-eye me-2"></i>View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let currentCategory = 'all';
let searchQuery = '';

$(document).ready(function() {
    loadCategories();
    loadProducts();

    // Search functionality
    $('#search-btn').click(function() {
        searchQuery = $('#search-input').val();
        currentPage = 1;
        loadProducts();
    });

    $('#search-input').keypress(function(e) {
        if (e.which === 13) {
            searchQuery = $(this).val();
            currentPage = 1;
            loadProducts();
        }
    });
});

function loadCategories() {
    const url = '/api/v1/categories'; // ✅ always same route

    safeFetch(url)
        .then(res => {
            if (!res.ok) throw res;
            return res.json();
        })
        .then(response => {

            const container = $('#categories-container');
            container.empty().append('<button class="btn btn-outline-primary active" data-category="all">All Products</button>');

            response.data.forEach(category => {
                container.append(`
                    <button class="btn btn-outline-${category.visibility === 'protected' ? 'warning' : 'primary'}"
                            data-category="${category.slug}"
                            data-visibility="${category.visibility}">
                        ${category.name}
                        ${category.visibility === 'protected' ? '<i class="fas fa-lock ms-1"></i>' : ''}
                    </button>
                `);
            });

        })
        .catch(() => {
            removeToken();
            loadCategories();
        });
}


function loadProducts() {
    let url = '/api/v1/shop'; 
    url += '?page=' + currentPage;

    if (searchQuery) {
        url += '&search=' + encodeURIComponent(searchQuery);
    }

    if (currentCategory !== 'all') {
        url += '&category=' + currentCategory;
    }

    safeFetch(url)
        .then(res => {
            if (!res.ok) throw res;
            return res.json();
        })
        .then(response => {
            displayProducts(response.data.products);
            setupPagination(response.data.pagination);
        })
        .catch(() => {
            removeToken();
            loadProducts();
        });
}

function displayProducts(products) {
    const container = $('#products-container');
    container.empty();

    if (products.length === 0) {
        container.html(`
            <div class="col-12 text-center py-5">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No products found.
                </div>
            </div>
        `);
        return;
    }

    const template = document.getElementById('product-card-template');

    products.forEach(product => {
        const card = template.content.cloneNode(true);
        const cardElement = $(card);

        cardElement.find('.product-name').text(product.name);
        cardElement.find('.product-excerpt').text(product.excerpt || 'No description available');
        cardElement.find('.product-price').text('$' + parseFloat(product.price).toFixed(2));

        // Stock status
        const stockBadge = cardElement.find('.product-stock');
        if (product.stock > 0) {
            stockBadge.addClass('stock-instock').text('In Stock (' + product.stock + ')');
        } else {
            stockBadge.addClass('stock-outofstock').text('Out of Stock');
        }

        // Categories
        const categoriesContainer = cardElement.find('.product-categories');
        product.categories.forEach(category => {
            categoriesContainer.append(`
                <span class="product-category-badge ${category.visibility}">
                    ${category.name}
                </span>
            `);
        });

        // View button
        cardElement.find('.view-product-btn').attr('href', '/product/' + product.slug);

        container.append(cardElement);
    });
}

function setupPagination(pagination) {
    const container = $('#pagination-container');
    container.empty();

    if (pagination.last_page <= 1) {
        return;
    }

    // Previous button
    container.append(`
        <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `);

    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        if (
            i === 1 ||
            i === pagination.last_page ||
            (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)
        ) {
            container.append(`
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
            container.append(`
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `);
        }
    }

    // Next button
    container.append(`
        <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `);

    // Pagination click handler
    $('.page-link[data-page]').click(function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && page !== currentPage) {
            currentPage = page;
            loadProducts();
        }
    });
}
</script>
@endpush
