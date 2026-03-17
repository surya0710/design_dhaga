@extends('frontend.layouts.app')
@section('title', 'Register - Design Dhaga')

@section('meta_description', 'Create your Design Dhaga account to access exclusive handcrafted collections and manage your orders.')
@section('meta_keywords', 'register, sign up, design dhaga, fashion brand, handmade clothing, made in india')
@section('og_title', 'Register - Design Dhaga')
@section('og_description', 'Create your Design Dhaga account to access exclusive handcrafted collections.')
@section('og_image', asset('frontend_assets/images/og-register.jpg'))

@section('content')

<div class="register-page">
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>

    <div class="container register-content">
        <div class="row align-items-center" style="min-height: calc(100vh - 80px);">
            
            <!-- Brand Section -->
            <div class="col-lg-6 brand-section">
                <img class="" src="{{ asset('frontend_assets/images/logo/landscape-logo.svg') }}" alt="Design Dhaga Logo">
                <p class="brand-tagline">Explore limited edition handcrafted pieces you won’t find anywhere else.</p>

                <ul class="benefit-list d-none d-lg-block">
                    <li class="benefit-item">
                        <div class="benefit-icon">🎨</div>
                        <span class="benefit-text">Exclusive access to limited edition pieces</span>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">💝</div>
                        <span class="benefit-text">Personalized recommendations just for you</span>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">🚚</div>
                        <span class="benefit-text">Priority shipping and early sale access</span>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">🌟</div>
                        <span class="benefit-text">Earn rewards with every purchase</span>
                    </li>
                </ul>
            </div>

            <!-- Register Card -->
            <div class="col-lg-6">
                <div class="card register-card mx-auto" style="max-width: 520px;">
                    <h2 class="register-title">Create Account</h2>
                    <p class="register-subtitle">Start your artisan fashion journey today</p>

                    <form method="POST" action="{{ route('register.post') }}" id="registerForm">
                        @csrf
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="row g-3">

                            <!-- First Name -->
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="John" required autofocus>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="Doe" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-12">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@example.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-12">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+91 98765 43210">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Create a strong password" required>

                                <div class="password-strength mt-2" id="passwordStrength">
                                    <div class="d-flex gap-1 mb-1">
                                        <div class="strength-bar flex-fill"></div>
                                        <div class="strength-bar flex-fill"></div>
                                        <div class="strength-bar flex-fill"></div>
                                        <div class="strength-bar flex-fill"></div>
                                    </div>
                                    <small class="strength-text text-muted"></small>
                                </div>

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Re-enter your password" required>
                            </div>

                            <!-- Terms -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the 
                                        <a href="#">Terms & Conditions</a> and 
                                        <a href="#">Privacy Policy</a> of Design Dhaga
                                    </label>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-register"> Create Account </button>
                            </div>

                            <!-- Login Link -->
                            <div class="col-12 text-center">
                                <p class="mb-0">
                                    Already have an account?
                                    <a href="{{ route('login') }}">Sign In</a>
                                </p>
                            </div>

                        </div>
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

    // Password Strength Indicator
    const passwordInput = document.getElementById('password');
    const strengthIndicator = document.getElementById('passwordStrength');
    const strengthBars = strengthIndicator.querySelectorAll('.strength-bar');
    const strengthText = strengthIndicator.querySelector('.strength-text');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        // Reset bars
        strengthBars.forEach(bar => {
            bar.classList.remove('active', 'weak', 'medium', 'strong');
        });

        // Update based on strength
        if (password.length > 0) {
            for (let i = 0; i < strength.level; i++) {
                strengthBars[i].classList.add('active', strength.class);
            }
            strengthText.textContent = strength.text;
            strengthText.style.color = getStrengthColor(strength.class);
        } else {
            strengthText.textContent = '';
        }
    });

    function calculatePasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[^a-zA-Z\d]/.test(password)) score++;
        
        if (score <= 2) {
            return { level: 1, text: 'Weak', class: 'weak' };
        } else if (score <= 3) {
            return { level: 2, text: 'Fair', class: 'medium' };
        } else if (score <= 4) {
            return { level: 3, text: 'Good', class: 'strong' };
        } else {
            return { level: 4, text: 'Strong', class: 'strong' };
        }
    }

    function getStrengthColor(strengthClass) {
        switch(strengthClass) {
            case 'weak': return '#dc3545';
            case 'medium': return '#ffc107';
            case 'strong': return '#8A9A7B';
            default: return '#6B7560';
        }
    }

    // Password Confirmation Match Check
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    confirmPasswordInput.addEventListener('input', function() {
        if (this.value && this.value !== passwordInput.value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });

    passwordInput.addEventListener('input', function() {
        if (confirmPasswordInput.value && confirmPasswordInput.value !== this.value) {
            confirmPasswordInput.setCustomValidity('Passwords do not match');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    });
</script>
@endpush

@endsection