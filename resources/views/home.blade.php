@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Welcome to Assessment Shop</h1>
                    <p class="lead mb-4">Discover our exclusive collection of premium products with special pricing for
                        silver and gold members.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('shop.index') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-store me-2"></i>Shop Now
                        </a>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="{{ asset('images/hero1.avif') }}" alt="Hero Image" class="img-fluid rounded-3 shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Member Benefits -->
    <section class="benefits-section py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Member Benefits</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 p-3"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-user text-primary fa-3x mt-2"></i>
                            </div>
                            <h4 class="fw-bold">Customer</h4>
                            <p class="text-muted mb-3">Standard pricing on all products</p>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Access to public
                                    categories</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Standard pricing
                                </li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Order tracking</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-secondary bg-opacity-10 rounded-circle mx-auto mb-3 p-3"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-star text-secondary fa-3x mt-2"></i>
                            </div>
                            <h4 class="fw-bold">Silver Member</h4>
                            <p class="text-muted mb-3">10% off on all products</p>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>All Customer
                                    benefits</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Access to protected
                                    categories</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i><strong>10%
                                        discount</strong> on all products</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 p-3"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-crown text-warning fa-3x mt-2"></i>
                            </div>
                            <h4 class="fw-bold">Gold Member</h4>
                            <p class="text-muted mb-3">20% off on all products</p>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>All Silver benefits
                                </li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Priority support
                                </li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i><strong>20%
                                        discount</strong> on all products</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products-section py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Featured Products</h2>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-primary">
                    View All <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>

            <div class="row">
                @if ($featuredProducts && $featuredProducts->count() > 0)
                    @foreach ($featuredProducts as $product)
                        <div class="col-md-6 col-lg-3">
                            <div class="card product-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">{{ $product['name'] }}</h5>
                                    <div class="product-categories mb-2">
                                        @foreach ($product['categories'] as $category)
                                            <span class="product-category-badge {{ $category['visibility'] }}">
                                                {{ $category['name'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($product['excerpt'] ?? 'No description available', 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="product-price">${{ number_format($product['price'], 2) }}</span>
                                        <span
                                            class="stock-badge {{ $product['stock'] > 0 ? 'stock-instock' : 'stock-outofstock' }}">
                                            {{ $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' }}
                                        </span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('product.show', $product['slug']) }}"
                                            class="btn btn-primary w-100">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No featured products available at the moment.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    @if (isset($categories) && $categories->count() > 0)
        <section class="categories-section py-5">
            <div class="container">
                <h2 class="text-center fw-bold mb-5">Shop by Category</h2>
                <div class="row g-4">
                    @foreach ($categories as $category)
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('shop.index') }}?category={{ $category['slug'] }}"
                                class="text-decoration-none">
                                <div class="card border-0 shadow-sm text-center h-100 category-card">
                                    <div class="card-body p-4">
                                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                            style="width: 80px; height: 80px; background-color: {{ $category['visibility'] === 'protected' ? '#fff3e0' : '#e3f2fd' }};">
                                            <i
                                                class="fas {{ $category['visibility'] === 'protected' ? 'fa-lock text-warning' : 'fa-tag text-primary' }} fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">{{ $category['name'] }}</h5>
                                        <p class="text-muted small mb-2">{{ $category['description'] ?? '' }}</p>
                                        <span
                                            class="badge {{ $category['visibility'] === 'protected' ? 'bg-warning' : 'bg-primary' }}">
                                            {{ $category['visibility'] === 'protected' ? 'Protected' : 'Public' }}
                                        </span>
                                        @if ($category['product_count'] > 0)
                                            <span class="badge bg-secondary ms-2">{{ $category['product_count'] }}
                                                products</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Call to Action -->
    <section class="cta-section bg-primary text-white py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">Ready to start shopping?</h2>
            <p class="lead mb-4">Join our community and enjoy exclusive member benefits.</p>
            @guest
                <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Login Now
                </a>
            @else
                <a href="{{ route('shop.index') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-store me-2"></i>Start Shopping
                </a>
            @endguest
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .hero-section {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .category-card {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .benefits-section .card {
            transition: transform 0.3s;
        }

        .benefits-section .card:hover {
            transform: translateY(-5px);
        }

        .product-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #4e73df;
        }

        .product-category-badge {
            background-color: #e3f2fd;
            color: #0d47a1;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
            display: inline-block;
        }

        .product-category-badge.protected {
            background-color: #fff3e0;
            color: #b76e1b;
        }

        .stock-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .stock-instock {
            background-color: #d4edda;
            color: #155724;
        }

        .stock-outofstock {
            background-color: #f8d7da;
            color: #721c24;
        }

        .cta-section {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
    </style>
@endpush
