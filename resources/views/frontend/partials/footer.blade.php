    <section class="footer-section bg-body-primary py-2 border-top" id="footer">
      <div class="container w-100">
        <div class="row">
          <h5 class="fw-bold fw-xs-regular mt-3 mb-3 text-center">Join our community and stay updated with our latest designs.</h5>
        </div>
        <div class="row g-2 padding-sm-2">
          <div class="col-lg-4 col-md-6 col-sm-12">
            <h5 class="fw-bold text-uppercase mt-1 mb-1">Design Dhaga</h5>
            <ul class="list-unstyled">
              <li class="mb-2">
                <a href="{{ route('about-us') }}" class="text-decoration-none text-secondary link-dark">About Us</a>
              </li>
              <li class="mb-2">
                <a href="{{ route('privacy-policy') }}" class="text-decoration-none text-secondary link-dark">Privacy Policy</a>
              </li>
              <li class="mb-2">
                <a href="{{ route('terms-and-condition') }}" class="text-decoration-none text-secondary link-dark">Terms & Condition</a>
              </li>
              <li class="mb-2">
                <a href="{{ route('blogs') }}" class="text-decoration-none text-secondary link-dark">Blogs</a>
              </li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 col-sm-12">
            <h5 class="fw-bold text-uppercase mt-1 mb-1">Help & Support</h5>
            <ul class="list-unstyled">
              <li class="mb-2">
                <a href="{{ route('shipping-policy') }}" class="text-decoration-none text-secondary link-dark">Order & Shipping</a>
              </li>
              <li class="mb-2">
                <a href="{{ route('return-policy') }}" class="text-decoration-none text-secondary link-dark">Return, Exchange & Cancellation</a>
              </li>
              <li class="mb-2">
                <a href="{{ route('contact-us') }}" class="text-decoration-none text-secondary link-dark">Contact Us</a>
              </li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-12 col-sm-12">
            <h5 class="fw-bold text-uppercase mt-1 mb-1">Connect With Us</h5>
            <ul class="list-unstyled mb-1">
              <li><a href="{{ route('collaborations') }}" class="text-decoration-none text-secondary link-dark">Collaboration</a></li>
            </ul>
            <p class="text-muted small mb-1">Follow us for daily inspiration:</p>
            <div class="d-flex mt-2 social-media-icons">
              <a href="https://www.facebook.com/share/1A9mCmVNy2/" target="_blank" class="rounded-circle p-2 d-flex align-items-center justify-content-center"
                style="width: 38px; height: 38px; border-color: #cfcdcd; text-decoration:none">
                <i class="fa-brands fa-facebook" style="font-size:20px"></i>
              </a>
              <a href="https://www.instagram.com/design.dhaga?igsh=MW5maXJraTgzbnYzOA==" target="_blank" class="rounded-circle p-2 d-flex align-items-center justify-content-center"
                style="width: 38px; height: 38px; border-color: #cfcdcd; text-decoration:none">
                <i class="fa-brands fa-instagram" style="font-size:20px"></i>
              </a>
              <a href="https://youtube.com/@designdhaga?si=A5rYdj_bpGZB_D1b" target="_blank" class="rounded-circle p-2 d-flex align-items-center justify-content-center"
                style="width: 38px; height: 38px; border-color: #cfcdcd; text-decoration:none">
                <i class="fa-brands fa-youtube" style="font-size:20px"></i>
              </a>
              <a href="https://pin.it/Y79Q6uD62" target="_blank" class="p-2 d-flex align-items-center justify-content-center"
                style="width: 38px; height: 38px; border-color: #cfcdcd; text-decoration:none">
                <i class="fa-brands fa-pinterest" style="font-size:20px"></i>
              </a>
            </div>
          </div>
        </div>

        <hr class="opacity-10" />
        <div class="row align-items-center">
          <div class="col-md-6 text-center text-md-start">
            <span class="small text-muted">© 2026 <span class="fw-bold">Design Dhaga</span>. All rights reserved.</span>
          </div>
          <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
            <a href="{{ route('portfolio') }}" class="text-decoration-none small text-secondary fw-semibold">View Portfolio →</a>
          </div>
        </div>
      </div>
    </section>
    <a href="https://wa.me/919671941303?text=Hi%20!" class="floating-whatsapp">
      <img src="https://cdn.shopify.com/s/files/1/0450/3476/6485/files/56x56_1.png?v=1755694867" alt="WhatsApp Icon" width="56" height="56">
    </a>
    <button id="goToTop" aria-label="Go to top">↑</button>
    @push('scripts')
    @if(!Auth::check() || (Auth::check() && Auth::user()->utype != 'USR'))
    <!-- LOGIN MODAL -->
    <div class="modal fade" id="loginModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content border-0 rounded-4 overflow-hidden">

              <div class="row g-0">

                  <!-- LEFT SIDE (LOGO) -->
                  <div class="col-md-6 bg-dark d-flex align-items-center justify-content-center">
                      <div class="text-center p-4">
                          <img src="{{ asset('frontend_assets/images/logo/white-logo.svg') }}" class="img-fluid mb-2">
                          <p class="text-white small mb-0 opacity-75">
                              Welcome to Design Dhaga
                          </p>
                      </div>
                  </div>

                  <!-- RIGHT SIDE (FORM) -->
                  <div class="col-md-6 p-4 position-relative">

                      <!-- CLOSE -->
                      <button type="button" class="btn-close position-absolute top-0 end-0 m-3"  data-bs-dismiss="modal">
                      </button>

                      <h5 class="fw-semibold mb-3">Login / Signup</h5>

                      <form method="POST" action="{{ route('login.post') }}">
                          @csrf

                          <!-- STEP 1 -->
                          <div id="step1">

                              <label class="form-label text-uppercase small text-muted fw-semibold">
                                  Email
                              </label>

                              <input type="email" name="email" id="email" class="form-control form-control-lg rounded-3 mb-3" placeholder="Enter your email" required>

                              <button type="button" class="btn btn-dark w-100 rounded-3 py-2" onclick="nextStep()">
                                  Continue
                              </button>

                              <div class="text-center my-3 text-muted small">or</div>

                              <a href="{{ route('google.login') }}" 
                                class="btn btn-outline-secondary w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 py-2">
                                  <img src="https://developers.google.com/identity/images/g-logo.png" width="18">
                                  Continue with Google
                              </a>

                          </div>

                          <!-- STEP 2 -->
                          <div id="step2" class="d-none">

                              <label class="form-label text-uppercase small text-muted fw-semibold">
                                  Password
                              </label>

                              <input type="password" name="password" class="form-control form-control-lg rounded-3 mb-3" placeholder="Enter your password" required>

                              <button type="submit" class="btn btn-dark w-100 rounded-3 py-2">
                                  Login
                              </button>

                              <button type="button" class="btn btn-link w-100 mt-2 text-decoration-none text-muted" onclick="prevStep()">
                                  ← Back
                              </button>

                          </div>

                      </form>

                  </div>

              </div>
          </div>
      </div>
  </div>
    <script>
      let loginModal = $('#loginModal');

      function nextStep() {
          let email = document.getElementById('email').value;
          if (!email) return alert("Enter email");

          document.getElementById('step1').classList.add('d-none');
          document.getElementById('step2').classList.remove('d-none');
      }

      function prevStep() {
          document.getElementById('step2').classList.add('d-none');
          document.getElementById('step1').classList.remove('d-none');
      }
    </script>
    @endif
    @endpush