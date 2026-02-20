<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Assessment Shop') - {{ config('app.name') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .08);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--secondary-color);
            padding: 0.5rem 1rem;
            transition: all 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }

        .navbar-nav .nav-link.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }

        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .role-customer {
            background-color: #e3f2fd;
            color: #0d47a1;
        }

        .role-silver {
            background-color: #f5f5f5;
            color: #616161;
            border: 1px solid #bdbdbd;
        }

        .role-gold {
            background-color: #fff8e1;
            color: #b76e1b;
        }

        .role-admin {
            background-color: #fbe9e7;
            color: #bf360c;
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
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .product-category-badge {
            background-color: #e3f2fd;
            color: #0d47a1;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .product-category-badge.protected {
            background-color: #fff3e0;
            color: #b76e1b;
        }

        .stock-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
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

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }

        .pagination {
            justify-content: center;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .page-link {
            color: var(--primary-color);
        }

        .page-link:hover {
            color: #2e59d9;
        }

        .footer {
            background-color: white;
            border-top: 1px solid #e3e6f0;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .alert {
            border: none;
            border-radius: 10px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-store me-2"></i>
                Assessment Shop
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}"
                            href="{{ route('shop.index') }}">
                            <i class="fas fa-shopping-bag me-1"></i> Shop
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @auth
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                {{ Auth::user()->display_name }}
                                <span class="role-badge role-{{ Auth::user()->role }} ms-2">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>


                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-store me-2"></i>Assessment Shop</h5>
                    <p class="text-muted">Your premier destination for quality products with exclusive member pricing.
                    </p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">Shop</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">About Us</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Member Benefits</h6>
                    <ul class="list-unstyled">
                        <li class="text-muted"><i class="fas fa-check-circle text-success me-2"></i>Silver: 10% off</li>
                        <li class="text-muted"><i class="fas fa-check-circle text-warning me-2"></i>Gold: 20% off</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center text-muted">
                &copy; {{ date('Y') }} Assessment Shop. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
          // Store JWT token after login
    function storeToken(token) {
        localStorage.setItem('jwt_token', token);
    }

    function getToken() {
        return localStorage.getItem('jwt_token');
    }

    function removeToken() {
        localStorage.removeItem('jwt_token');
    }

    // Auto remove expired token
    function safeFetch(url, options = {}) {
        const token = getToken();

        if (token) {
            options.headers = {
                ...(options.headers || {}),
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            };
        }

        return fetch(url, options).then(response => {
            if (response.status === 401) {
                removeToken();
                console.log('Token expired - removed');
            }
            return response;
        });
    }

        // Handle logout
        document.getElementById('logout-form')?.addEventListener('submit', function(e) {
            // Remove JWT token first
            removeToken();
            // Let the form submit naturally to the web logout route
        });

        // Helper function for programatic logout if needed
        function logoutUser() {
            removeToken();
            document.getElementById('logout-form')?.submit();
        }
    </script>

    @stack('scripts')
</body>

</html>
