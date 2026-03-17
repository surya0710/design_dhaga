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
        <div class="row align-items-center" style="min-height: calc(100vh - 100px);">
            
            <!-- Brand Section -->
            <div class="col-lg-6 brand-section">
                <img class="" src="{{ asset('frontend_assets/images/logo/landscape-logo.svg') }}" alt="Design Dhaga Logo">
                <p class="brand-tagline">Explore limited edition handcrafted pieces you won’t find anywhere else.</p>

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
                    <h2 class="login-title">Verify Email</h2>
                    <p class="login-subtitle">Verify your email address</p>
                    <p class="text-muted mb-4">
                        We’ve sent a verification link to your email.  
                        Please verify your email to continue.
                    </p>

                    {{-- Success Message --}}
                    @if (session('resent'))
                        <div class="alert alert-success ">
                            A fresh verification link has been sent to your email address.
                        </div>
                    @endif

                    {{-- Resend --}}
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <p class="text-muted mb-3 text-center">
                            Didn’t receive the email?
                        </p>

                        <button type="submit" class="btn btn-login">
                            Resend Verification Email
                        </button>
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

