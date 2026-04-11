<div class="sticky-top">
    <div class="container-fluid bg-body-primary">
        <div class="row">
            <div class="col-md-12">
                <div class="top-header rotating-text">
                    <p class="text-center active"></p>
                </div>
            </div>
        </div>
    </div>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom p-0">
        <div class="container-fluid d-flex justify-content-between align-items-center px-3 px-md-5">

            <!-- LEFT: MENU + SEARCH -->
            <div class="d-flex align-items-center gap-3">
                <!-- Menu Icon -->
                <button id="openMenu" class="btn p-0 bg-transparent border-0">
                    <svg width="26" height="26" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round">
                        <line x1="3" y1="7" x2="23" y2="7" />
                        <line x1="3" y1="13" x2="23" y2="13" />
                        <line x1="3" y1="19" x2="23" y2="19" />
                    </svg>
                </button>

                <!-- Search Icon -->
                <button class="btn p-0 bg-transparent border-0">
                    <svg width="24" height="24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </button>
            </div>

            <!-- CENTER: LOGO TEXT -->
            <div class="text-center">
                <a class="navbar-brand logo" href="{{ route('home') }}">
                    <img src="{{ asset('frontend_assets/images/logo/landscape-logo.svg') }}" alt="Logo" class="img-fluid">
                </a>
            </div>

            <!-- RIGHT: WISHLIST + CART -->
            <div class="d-flex align-items-center gap-3">

                <!-- Wishlist -->
                <div>
                    @php 
                        $url = (Auth::check() && Auth::user()->utype === 'USR') ? route('wishlist.index') : route('login');
                    @endphp
                    <a class="btn p-0 bg-transparent border-0" href="{{ $url }}">
                        <i class="fa-2x fa-regular fa-heart"></i>
                    </a>
                </div>

                <!-- Cart -->
                <div class="position-relative">
                    <button class="btn p-0 bg-transparent border-0" onclick="window.location.href = '{{ route('cart.index') }}'">
                        <svg width="26" height="26" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="10" cy="20" r="1.5"></circle>
                            <circle cx="18" cy="20" r="1.5"></circle>
                            <path d="M3 3h3l2.5 12h11.5l2-8H8"></path>
                        </svg>
                    </button>

                    <!-- Badge -->
                    <span class="badge bg-warning text-dark rounded-circle position-absolute top-0 start-100 translate-middle"
                        style="font-size: 12px;">
                        {{ getCartItemsCount() }}
                    </span>
                </div>

            </div>
        </div>
    </nav>
</div>
<div id="menuOverlay"></div>

<!-- SLIDING MENU -->
<aside id="sideMenu">

    <div class="menu-section">
        <div class="d-flex align-items-center justify-content-between border-1 px-3 py-2">
            <div class="sidebar-logo">
                <img src="{{ asset('frontend_assets/images/logo/logo.svg') }}" class="img-fluid" alt="">
            </div>
            <div class="d-flex justify-content-space-between gap-3">
                @if (Auth::check() && Auth::user()->utype === 'USR')
                    <a href="{{ route('account.index') }}" class="btn btn-outline-secondary border-rounded text-white bg-dark px-3 py-1 font-normal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-person" viewBox="0 0 18 20">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                            <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z" />
                        </svg>
                        {{ Auth::user()->name }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary border-rounded text-white bg-dark px-3 py-1 font-normal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-person" viewBox="0 0 18 20">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                            <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z" />
                        </svg>
                        Account
                    </a>
                @endif
            </div>
            <div class="close-menu">
                <button id="closeMenu" class="btn p-0 bg-transparent border-0">
                    <svg aria-hidden="true" focusable="false" fill="none" width="16" class="icon icon-close"
                        viewBox="0 0 16 16">
                        <path d="m1 1 14 14M1 15 15 1" stroke="currentColor" stroke-width="1.5"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="menu-section p-3">
        @foreach($categories as $category)
            <div class="menu-title" data-toggle="{{ $category->slug }}">
                {{ $category->name }}
                <span class="toggle-icon">{{ $loop->index == 0 ? '-' : '+' }}</span>
            </div>

            <div class="submenu" id="{{ $category->slug }}" style="{{ $loop->index == 0 ? 'display:block;' : 'display:none;' }}">
                @foreach($category->children as $subCategory)
                    <a href="{{ route('shop.subcategory', [$category->slug, $subCategory->slug]) }}" class="submenu-item">
                        <img src="{{ asset('uploads/categories/'.$subCategory->image) }}" />
                        {{ $subCategory->name }}
                    </a>
                @endforeach
            </div>
        @endforeach
        
        <div class="menu-title">
            <a href="{{ route('home') }}" class="submenu-item">HOME</a>
        </div>
        <div class="menu-title">
            <a href="{{ route('about-us') }}" class="submenu-item">ABOUT US</a>
        </div>
        <div class="menu-title">
            <a href="{{ route('portfolio') }}" class="submenu-item">PORTFOLIO</a>
        </div>
        <div class="menu-title">
            <a href="{{ route('blogs') }}" class="submenu-item">BLOGS</a>
        </div>
        <div class="menu-title">
            <a href="{{ route('collaborations') }}" class="submenu-item">COLLABORATIONS</a>
        </div>
        <div class="menu-title">
            <a href="{{ route('contact-us') }}" class="submenu-item">CONTACT US</a>
        </div>
    </div>
    
    <div class="position-absolute bottom-0 w-100 align-items-center">
        <div class="d-flex gap-3 justify-content-center social-media-icons mb-2">
            <a href="https://www.facebook.com/share/1A9mCmVNy2/" target="_blank">
                <i class="fa-brands fa-facebook"></i>
            </a>
            <a href="https://www.instagram.com/design.dhaga?igsh=MW5maXJraTgzbnYzOA==" target="_blank">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="https://youtube.com/@designdhaga?si=A5rYdj_bpGZB_D1b" target="_blank">
                <i class="fa-brands fa-youtube"></i>
            </a>
            <a href="https://pin.it/Y79Q6uD62" target="_blank">
                <i class="fa-brands fa-pinterest"></i>
            </a>
            <a href="https://wa.link/x3oxtd" target="_blank">
                <i class="fa-brands fa-whatsapp"></i>
            </a>
        </div>
    </div>
</aside>