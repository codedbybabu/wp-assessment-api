    @extends('layouts.app')

    @section('title', 'Login')

    @section('content')
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header bg-white border-0 pt-4 pb-0">
                            <h3 class="text-center fw-bold text-primary">Welcome Back</h3>
                            <p class="text-center text-muted mb-0">Login to access your account</p>
                        </div>

                        <div class="card-body p-4">
                            <div id="alert-container"></div>

                            <form id="login-form">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="fas fa-envelope me-2 text-primary"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email"
                                        placeholder="Enter your email" value="" required>
                                    <div class="invalid-feedback" id="email-error"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">
                                        <i class="fas fa-lock me-2 text-primary"></i>Password
                                    </label>
                                    <input type="password" class="form-control form-control-lg" id="password"
                                        name="password" placeholder="Enter your password" value="" required>
                                    <div class="invalid-feedback" id="password-error"></div>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100" id="login-btn">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </form>

                            <hr class="my-4">

                            <div class="text-center">
                                <p class="text-muted mb-2">Demo Accounts:</p>
                                <div class="d-flex justify-content-center gap-2 mb-2">
                                    <span class="role-badge role-customer">john@example.com</span>
                                </div>
                                <div class="d-flex justify-content-center gap-2">
                                    <span class="role-badge role-silver">jane@example.com</span>
                                    <span class="role-badge role-gold">bob@example.com</span>
                                </div>
                                <p class="text-muted small mt-2">Password: 123456</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            function storeToken(token) {
                localStorage.setItem('jwt_token', token);
            }

            $(document).ready(function() {
                $('#login-form').on('submit', function(e) {
                    e.preventDefault();

                    const btn = $('#login-btn');
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Logging in...');

                    $.ajax({
                        url: '/web-login',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        data: {
                            email: $('#email').val(),
                            password: $('#password').val()
                        },
                        success: function(response) {
                            storeToken(response.data.token);

                            $('#alert-container').html(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>Login successful! Redirecting...
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);

                            setTimeout(() => {
                                window.location.href = '/dashboard';
                            }, 1000);
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html(
                                '<i class="fas fa-sign-in-alt me-2"></i>Login');

                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;

                                if (errors.email) {
                                    $('#email').addClass('is-invalid');
                                    $('#email-error').text(errors.email[0]);
                                }

                                $('#alert-container').html(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>${xhr.responseJSON.message || 'Invalid credentials'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                            } else {
                                $('#alert-container').html(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>Login failed. Please try again.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `);
                            }
                        }
                    });
                });

                // Remove error styling on input
                $('#email, #password').on('input', function() {
                    $(this).removeClass('is-invalid');
                });
            });
        </script>
    @endpush
