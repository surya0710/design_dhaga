@extends('frontend.layouts.app')
@section('title', 'Login - Design Dhaga')

@section('meta_description', 'Login to your Design Dhaga account to access exclusive features and manage your orders.')
@section('meta_keywords', 'login, design dhaga, fashion brand, handmade clothing, made in india')
@section('og_title', 'Login - Design Dhaga')
@section('og_description', 'Login to your Design Dhaga account to access exclusive features and manage your orders.')
@section('og_image', asset('frontend_assets/images/og-login.jpg'))

@section('content')

<div class="login-page">
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>

    <div class="container login-content">
        <div class="row align-items-center login-logo-section">
            
            <!-- Brand Section -->
            <div class="col-lg-6 brand-section">
                <img style="max-width: 35%" class="mb-2" src="{{ asset('frontend_assets/images/logo/square-logo.png') }}" alt="Design Dhaga Logo">
                <p class="brand-tagline d-none d-md-block">Explore limited edition handcrafted pieces you won’t find anywhere else.</p>

                <ul class="feature-list d-none d-lg-block">
                    <li class="feature-item">
                        <div class="feature-icon">✦</div>
                        <span class="feature-text">Access exclusive handmade collections</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon">♡</div>
                        <span class="feature-text">Save your favorite pieces to wishlist</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon">⚡</div>
                        <span class="feature-text">Track orders and manage your account</span>
                    </li>
                </ul>
            </div>

            <!-- Login Card -->
            <div class="col-lg-6">
                <div class="card login-card mx-auto">
                    <h2 class="login-title">Welcome back</h2>
                    <p class="login-subtitle">Sign in to continue your journey</p>

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <input type="hidden" name="redirect_to" value="{{ url()->previous() }}">

                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                placeholder="Enter your password"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-options">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    name="remember" 
                                    id="remember"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <a href="{{ route('password.forgot') }}" class="forgot-link">
                                Forgot password?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-login">
                            Sign In
                        </button>

                        <!-- <div class="divider">
                            <span>or continue with</span>
                        </div>

                        <div class="social-login">
                            <a href="#" class="btn-social">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                                Google
                            </a>
                            <a href="#" class="btn-social">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#1877F2">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </a>
                        </div> -->

                        <p class="register-link">
                            Don't have an account? <a href="{{ route('register') }}">Create one</a>
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add subtle parallax effect on mouse move
    document.addEventListener('mousemove', (e) => {
        const decorations = document.querySelectorAll('.bg-decoration');
        const mouseX = e.clientX / window.innerWidth;
        const mouseY = e.clientY / window.innerHeight;
        
        decorations.forEach((decoration, index) => {
            const speed = (index + 1) * 10;
            const x = mouseX * speed;
            const y = mouseY * speed;
            decoration.style.transform = `translate(${x}px, ${y}px)`;
        });
    });
</script>
@endpush

@endsection