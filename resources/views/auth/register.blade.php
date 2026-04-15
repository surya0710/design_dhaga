@extends('layouts.app')

@section('content')


<!-- page-title -->
        <div class="page-title" style="background-image: url(images/section/page-title.jpg);">
            <div class="container-full">
                <div class="row">
                    <div class="col-12">
                        <h3 class="heading text-center">Create An Account</h3>
                        <ul class="breadcrumbs d-flex align-items-center justify-content-center">
                            <li>
                                <a class="link" href="{{route('home.index')}}">Homepage</a>
                            </li>
                            <li>
                                <i class="icon-arrRight"></i>
                            </li>
                            <li>
                                Register
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /page-title -->

        <!-- login -->
        <section class="flat-spacing">
            <div class="container">
                <div class="login-wrap">
                    <div class="left">
                        <div class="heading d-flex align-items-center justify-content-between mb_20">
                            <h4>Register</h4>
                            <button type="button" class="login-with-google-btn" >
                                <a href="{{ route('google.login') }}" >Sign in with Google</a>
                            </button>
                        </div>
                        <form method="POST" action="{{route('register')}}" class="form-login form-has-password">
                          @csrf
                            <div class="wrap">
                                <fieldset class="">
                                    <input class="" type="text" placeholder="Name*" name="name" tabindex="2" value="{{old('name')}}" aria-required="true" required="">
                                    @error('name')
                                      <div class="text-danger">{{$message}}</div>
                                    @enderror
                                </fieldset>
                                <fieldset class="">
                                    <input class="" type="email" placeholder="Username or email address*" name="email" tabindex="2" value="{{old('email')}}" aria-required="true" required="">
                                    @error('email')
                                      <div class="text-danger">{{$message}}</div>
                                    @enderror

                                </fieldset>
                                <fieldset class="">
                                    <input class="" type="text" pattern="[789][0-9]{9}" title="Phone number with 7-9 and remaing 9 digit with 0-9" placeholder="Mobile no.*" name="mobile" tabindex="2" value="{{old('mobile')}}" aria-required="true" required="">
                                    @error('mobile')
                                      <div class="text-danger">{{$message}}</div>
                                    @enderror
                                </fieldset>
                                <fieldset class="position-relative password-item">
                                    <input class="input-password" type="password" placeholder="Password*" name="password" tabindex="2" value="" aria-required="true" required="">
                                    <span class="toggle-password unshow">
                                        <i class="icon-eye-hide-line"></i>
                                    </span>
                                    @error('password')
                                      <div class="text-danger">{{$message}}</div>
                                    @enderror

                                </fieldset>
                                <fieldset class="position-relative password-item">
                                    <input class="input-password" type="password" placeholder="Confirm Password*" name="password_confirmation" tabindex="2" value="" aria-required="true" required="">
                                    <span class="toggle-password unshow">
                                        <i class="icon-eye-hide-line"></i>
                                    </span>
                                    @error('password_confirmation')
                                      <div class="text-danger">{{$message}}</div>
                                    @enderror
                                </fieldset>
                                <!-- <div class="d-flex align-items-center">
                                    <div class="tf-cart-checkbox">
                                        <div class="tf-checkbox-wrapp">
                                            <input checked="" class="" type="checkbox" id="login-form_agree" name="agree_checkbox">
                                            <div>
                                                <i class="icon-check"></i>
                                            </div>
                                        </div>
                                        <label class="text-secondary-2" for="login-form_agree">
                                            I agree to the&nbsp;
                                        </label>
                                    </div>
                                    <a href="term-of-use.html" title="Terms of Service"> Terms of User</a>
                                </div> -->
                            </div>
                            <div class="button-submit">
                                <button class="tf-btn btn-fill" type="submit">
                                    <span class="text text-button">Register</span>
                                </button>
                                
                            </div>
                        </form>
                    </div>
                    <div class="right">
                        <h4 class="mb_8">Already have an account?</h4>
                        <p class="text-secondary">Welcome back. Sign in to access your personalized experience, saved preferences, and more. We're thrilled to have you with us again!</p>
                        <a class="tf-btn btn-fill" data-bs-toggle="modal" data-bs-target="#loginModal"><span class="text text-button">Login</span></a>
                    </div>
                </div>
            </div>
        </section>
        <!-- /login -->










<!-- <main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">
      <ul class="nav nav-tabs mb-5" id="login_register" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore active" id="register-tab" data-bs-toggle="tab"
            href="#tab-item-register" role="tab" aria-controls="tab-item-register" aria-selected="true">Register</a>
        </li>
      </ul>
      <div class="tab-content pt-2" id="login_register_tab_content">
        <div class="tab-pane fade show active" id="tab-item-register" role="tabpanel" aria-labelledby="register-tab">
          <div class="register-form">
            <form method="POST" action="{{route('register')}}" name="register-form" class="needs-validation" novalidate="">
                @csrf
              <div class="form-floating mb-3">
                <input class="form-control form-control_gray @error('name') is-invalid @enderror" name="name" value="{{old('name')}}" required="" autocomplete="name" autofocus="">
                <label for="name">Name</label>
                @error('name')
                    <span class="invalid-feedback"><strong>{{$message}}</strong></span>
                @enderror
              </div>
              <div class="pb-3"></div>
              <div class="form-floating mb-3">
                <input id="email" type="email" class="form-control form-control_gray @error('email') is-invalid @enderror" name="email" value="{{old('email')}}" required=""
                  autocomplete="email">
                <label for="email">Email address *</label>
                @error('email')
                    <span class="invalid-feedback"><strong>{{$message}}</strong></span>
                @enderror
              </div>

              <div class="pb-3"></div>

              <div class="form-floating mb-3">
                <input id="mobile" type="text" class="form-control form-control_gray @error('mobile') is-invalid @enderror" name="mobile" value="{{old('mobile')}}" required="" autocomplete="mobile">
                <label for="mobile">Mobile *</label>
                @error('mobile')
                    <span class="invalid-feedback"><strong>{{$message}}</strong></span>
                @enderror
              </div>

              <div class="pb-3"></div>

              <div class="form-floating mb-3">
                <input id="password" type="password" class="form-control form-control_gray @error('password') is-invalid @enderror" name="password" value="{{old('password')}}" required="" autocomplete="new-password">
                <label for="password">Password *</label>
                @error('password')
                    <span class="invalid-feedback"><strong>{{$message}}</strong></span>
                @enderror
              </div>

              <div class="form-floating mb-3">
                <input id="password-confirm" type="password" value="{{old('password_confirmation')}}" class="form-control form-control_gray @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required="" autocomplete="new-password">
                <label for="password">Confirm Password *</label>
                @error('password_confirmation')
                    <span class="invalid-feedback"><strong>{{$strong}}</strong></span>
                @enderror
              </div>

              <div class="d-flex align-items-center mb-3 pb-2">
                <p class="m-0">Your personal data will be used to support your experience throughout this website, to
                  manage access to your account, and for other purposes described in our privacy policy.</p>
              </div>

              <button class="btn btn-primary w-100 text-uppercase" type="submit">Register</button>

              <div class="customer-option mt-4 text-center">
                <span class="text-secondary">Have an account?</span>
                <a href="login.html" class="btn-text js-show-register">Login to your Account</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main> -->






<!-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->
@endsection
