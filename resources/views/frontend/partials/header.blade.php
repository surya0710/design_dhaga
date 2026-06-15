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
        <div class="container-fluid d-flex justify-content-between align-items-center px-3 px-md-5 py-1">

            <!-- LEFT: MENU + SEARCH -->
            <div class="d-flex align-items-center gap-3">
                <button id="openMenu" class="btn p-0 bg-transparent border-0">
                    <svg width="26" height="26" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round">
                        <line x1="3" y1="7" x2="23" y2="7" />
                        <line x1="3" y1="13" x2="23" y2="13" />
                        <line x1="3" y1="19" x2="23" y2="19" />
                    </svg>
                </button>
                <button class="btn p-0 bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <svg width="24" height="24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </button>
            </div>

            <!-- CENTER: LOGO -->
            <div class="text-center">
                <a class="navbar-brand logo" href="{{ route('home') }}">
                    <img src="{{ asset('uploads/settings/' . $settings->logo) }}" alt="{{ $settings->store_name }}Logo" class="img-fluid">
                </a>
            </div>

            <!-- RIGHT: USER + WISHLIST + CART -->
            <div class="d-flex align-items-center gap-1">

                <!-- User Avatar -->
                @if (Auth::check() && Auth::user()->utype === 'USR')
                    <a href="{{ route('account.index') }}" class="btn p-0 bg-transparent border-0 me-1" title="{{ Auth::user()->name }}">
                        @if (Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="rounded-circle object-fit-cover border user-image"
                            style="border-color: #ddd !important;" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="rounded-circle bg-dark d-none align-items-center justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="white" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                    <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z"/>
                                </svg>
                            </span>
                        @else
                            <span class="rounded-circle bg-dark d-flex align-items-center justify-content-center user-image">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="white" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                    <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z"/>
                                </svg>
                            </span>
                        @endif
                    </a>
                @endif

                <!-- Wishlist -->
                <div>
                    <a class="btn p-0 bg-transparent border-0"
                        href="{{ (Auth::check() && Auth::user()->utype === 'USR') ? route('wishlist.index') : '#loginModal' }}"
                        data-bs-toggle="{{ (Auth::check() && Auth::user()->utype === 'USR') ? '' : 'modal' }}"
                        data-bs-target="{{ (Auth::check() && Auth::user()->utype === 'USR') ? '' : '#loginModal' }}">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </a>
                </div>

                <!-- Cart -->
                <div class="position-relative">
                    <a class="btn p-0 bg-transparent border-0" 
                    href="{{ (Auth::check() && Auth::user()->utype === 'USR') ? route('cart.index') : '#loginModal' }}"
                    data-bs-toggle="{{ (Auth::check() && Auth::user()->utype === 'USR') ? '' : 'modal' }}"
                    data-bs-target="{{ (Auth::check() && Auth::user()->utype === 'USR') ? '' : '#loginModal' }}">
                        <svg width="26" height="26" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="10" cy="22" r="2"></circle>
                            <circle cx="18" cy="22" r="2"></circle>
                            <path d="M3 3h3l2.5 12h11.5l2-8H8"></path>
                        </svg>
                    </a>
                    <span id="cartBadge" class="badge bg-warning text-dark rounded-circle position-absolute top-0 start-100 translate-middle" style="font-size: 9px;">
                        {{ getCartItemsCount() }}
                    </span>
                </div>
            </div>
        </div>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchModal = document.getElementById('searchModal');
        const searchInput = searchModal?.querySelector('input[name="q"]');
        const clearSearch = document.getElementById('clearSearchInput');
        const suggestionsBox = document.getElementById('searchSuggestions');
        const suggestionsUrl = @json(route('shop.search.suggestions'));
        let searchTimer;
        let activeController;

        searchModal?.addEventListener('shown.bs.modal', () => {
            searchInput?.focus();
        });

        function renderSuggestions(items) {
            if (!suggestionsBox) {
                return;
            }

            if (!items.length) {
                suggestionsBox.innerHTML = '<div class="search-suggestion-empty">No quick matches found. Press enter to view all results.</div>';
                suggestionsBox.classList.add('show');
                return;
            }

            suggestionsBox.innerHTML = items.map(item => `
                <a class="search-suggestion-item" href="${item.url}">
                    <img src="${item.image}" alt="">
                    <span class="search-suggestion-info">
                        <span class="search-suggestion-name"></span>
                        <span class="search-suggestion-price">${item.price}</span>
                    </span>
                </a>
            `).join('');

            suggestionsBox.querySelectorAll('.search-suggestion-name').forEach((nameEl, index) => {
                nameEl.textContent = items[index].name;
            });

            suggestionsBox.classList.add('show');
        }

        function clearSuggestions() {
            if (!suggestionsBox) {
                return;
            }

            suggestionsBox.innerHTML = '';
            suggestionsBox.classList.remove('show');
        }

        function toggleClearButton() {
            clearSearch?.classList.toggle('show', Boolean(searchInput?.value.trim()));
        }

        searchInput?.addEventListener('input', () => {
            const query = searchInput.value.trim();
            clearTimeout(searchTimer);
            toggleClearButton();

            if (query.length < 2) {
                activeController?.abort();
                clearSuggestions();
                return;
            }

            searchTimer = setTimeout(() => {
                activeController?.abort();
                activeController = new AbortController();

                fetch(`${suggestionsUrl}?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: activeController.signal,
                })
                    .then(response => response.json())
                    .then(renderSuggestions)
                    .catch(error => {
                        if (error.name !== 'AbortError') {
                            clearSuggestions();
                        }
                    });
            }, 250);
        });

        clearSearch?.addEventListener('click', () => {
            activeController?.abort();
            searchInput.value = '';
            searchInput.focus();
            toggleClearButton();
            clearSuggestions();
        });

        searchModal?.addEventListener('hidden.bs.modal', () => {
            clearSuggestions();
            toggleClearButton();
        });
    });
</script>
<div id="menuOverlay"></div>

<!-- SLIDING MENU -->
<aside id="sideMenu">

    <div class="menu-section">
        <div class="d-flex align-items-center justify-content-between border-1 px-3 py-2">
            <a class="sidebar-logo" href="{{ route('home') }}">
                <img src="{{ asset('frontend_assets/images/logo/logo.svg') }}"  class="img-fluid" alt="{{ $settings->store_name }} logo">
            </a>

            <div class="d-flex justify-content-space-between gap-3">
                @if (Auth::check())
                    <a href="{{ route('account.index') }}" class="btn btn-outline-secondary border-rounded text-white bg-dark px-3 py-1 font-normal d-flex align-items-center gap-2">
                        @if (Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" width="22" height="22" class="rounded-circle object-fit-cover"
                                onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 18 20" style="display:none;">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 18 20">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z" />
                            </svg>
                        @endif
                        {{ Auth::user()->name }}
                    </a>
                @else
                    <a class="btn btn-outline-secondary border-rounded text-white bg-dark px-3 py-1 font-normal" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 18 20">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                            <path fill-rule="evenodd" d="M14 14s-1-4-6-4-6 4-6 4 1 1 6 1 6-1 6-1z" />
                        </svg>
                        Account
                    </a>
                @endif
            </div>

            <div class="close-menu">
                <button id="closeMenu" class="btn p-0 bg-transparent border-0">
                    <svg aria-hidden="true" focusable="false" fill="none" width="16" class="icon icon-close" viewBox="0 0 16 16">
                        <path d="m1 1 14 14M1 15 15 1" stroke="currentColor" stroke-width="1.5"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="menu-section p-3">
        @foreach($categories as $item)
            <div class="menu-title" data-toggle="{{ $item->slug }}">
                {{ $item->name }}
                <span class="toggle-icon">{{ $loop->index == 0 ? '-' : '+' }}</span>
            </div>
            <div class="submenu" id="{{ $item->slug }}" style="{{ $loop->index == 0 ? 'display:block;' : 'display:none;' }}">
                @foreach($item->children as $subCategory)
                    <a href="{{ route('shop.subcategory', [$item->slug, $subCategory->slug]) }}" class="submenu-item">
                        <img src="{{ asset('uploads/categories/'.$subCategory->image) }}" alt="{{ $subCategory->name }} Category Icon" />
                        {{ $subCategory->name }}
                    </a>
                @endforeach
            </div>
        @endforeach
        @foreach($menu as $item)
            <div class="menu-title">
                @php
                    $slug = trim($item->slug, '/');
                    $url = $item->slug == '/' ? route('home') : url($slug);
                @endphp

                <a href="{{ $url }}" class="submenu-item">{{ $item->name }}</a>
            </div>
        @endforeach
        @if(Auth::check())
        <form method="post" action="{{ route('account.logout') }}" id="logoutForm">
        @csrf
        <div class="menu-title"><a onclick="document.getElementById('logoutForm').submit()" class="submenu-item">LOGOUT</a></div>
        </form>
        @endif
    </div>

    <!-- REMOVE position-absolute bottom-0, ADD class social-icons-wrapper -->
    <div class="w-100 d-flex align-items-center social-icons-wrapper">
        <div class="d-flex gap-3 justify-content-center w-100 social-media-icons mb-2">
            <a href="https://www.facebook.com/share/1A9mCmVNy2/" target="_blank"><i class="fa-brands fa-facebook"></i></a>
            <a href="https://www.instagram.com/design.dhaga?igsh=MW5maXJraTgzbnYzOA==" target="_blank"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://youtube.com/@designdhaga?si=A5rYdj_bpGZB_D1b" target="_blank"><i class="fa-brands fa-youtube"></i></a>
            <a href="https://pin.it/Y79Q6uD62" target="_blank"><i class="fa-brands fa-pinterest"></i></a>
            <a href="https://wa.link/x3oxtd" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
    </div>
</aside>

<style>
    .search-modal-content {
        border-radius: 28px;
        overflow: visible;
        background: linear-gradient(135deg, #fff8f5 0%, #ffffff 48%, #f8eee9 100%);
        box-shadow: 0 24px 80px rgba(36, 24, 18, 0.2);
    }

    .search-modal-hero {
        padding: 34px 34px 20px;
        border-bottom: 1px solid rgba(201, 107, 75, 0.12);
        position: relative;
        z-index: 2;
    }

    .search-modal-kicker {
        color: #c96b4b;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .search-modal-title {
        color: #1f1b18;
        font-size: clamp(26px, 4vw, 40px);
        font-weight: 700;
        line-height: 1.15;
        margin: 8px 0;
    }

    .search-modal-text {
        color: #6f625c;
        font-size: 15px;
        margin: 0;
    }

    .search-modal-form {
        position: relative;
        margin-top: 24px;
    }

    .search-input-wrap {
        position: relative;
    }

    .search-modal-input {
        height: 64px;
        border: 1px solid rgba(31, 27, 24, 0.1);
        border-radius: 999px;
        box-shadow: 0 12px 36px rgba(31, 27, 24, 0.08);
        font-size: 16px;
        padding: 0 190px 0 56px;
    }

    .search-modal-input::-webkit-search-cancel-button {
        display: none;
    }

    .search-modal-input:focus {
        border-color: #c96b4b;
        box-shadow: 0 14px 40px rgba(201, 107, 75, 0.18);
    }

    .search-modal-icon {
        position: absolute;
        left: 23px;
        top: 50%;
        transform: translateY(-50%);
        color: #c96b4b;
        z-index: 2;
    }

    .search-modal-submit {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        height: 48px;
        border: 0;
        border-radius: 999px;
        background: #1f1b18;
        color: #fff;
        font-weight: 700;
        padding: 0 26px;
        transition: 0.2s ease;
    }

    .search-modal-submit:hover {
        background: #c96b4b;
    }

    .search-modal-clear {
        display: none;
        position: absolute;
        right: 118px;
        top: 50%;
        transform: translateY(-50%);
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 50%;
        background: transparent;
        color: #6f625c;
        font-size: 22px;
        line-height: 1;
        z-index: 3;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .search-modal-clear.show {
        display: inline-flex;
    }

    .search-modal-clear:hover {
        background: #f4ebe6;
        color: #1f1b18;
    }

    .search-suggestions {
        display: none;
        margin-top: 10px;
        z-index: 5;
        max-height: min(330px, 42vh);
        overflow-y: auto;
        border: 1px solid rgba(31, 27, 24, 0.08);
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 18px 48px rgba(31, 27, 24, 0.14);
        padding: 8px;
    }

    .search-suggestions.show {
        display: block;
    }

    .search-suggestion-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 9px;
        border-radius: 16px;
        color: #1f1b18;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .search-suggestion-item:hover {
        background: #fff4ef;
        color: #1f1b18;
    }

    .search-suggestion-item img {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        object-fit: cover;
        background: #f7f2ef;
        flex: 0 0 auto;
    }

    .search-suggestion-info {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .search-suggestion-name {
        font-size: 14px;
        font-weight: 700;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .search-suggestion-price {
        color: #c96b4b;
        font-size: 13px;
        font-weight: 700;
    }

    .search-suggestion-empty {
        color: #6f625c;
        font-size: 14px;
        padding: 14px;
    }

    .search-modal-body {
        padding: 22px 34px 34px;
        position: relative;
        z-index: 1;
    }

    .search-chip-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
    }

    .search-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(31, 27, 24, 0.1);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.78);
        color: #1f1b18;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 14px;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .search-chip:hover {
        border-color: #c96b4b;
        background: #fff4ef;
        color: #c96b4b;
        transform: translateY(-1px);
    }

    .search-modal-close {
        position: absolute;
        right: 22px;
        top: 20px;
        z-index: 3;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.8);
        opacity: 1;
    }

    @media (max-width: 576px) {
        #searchModal .modal-dialog {
            margin: 18px;
            max-width: calc(100% - 36px);
        }

        .search-modal-content {
            border-radius: 22px;
            min-height: auto;
        }

        .search-modal-hero,
        .search-modal-body {
            padding-left: 20px;
            padding-right: 20px;
        }

        .search-modal-input {
            height: 58px;
            padding-left: 52px;
            padding-right: 104px;
        }

        .search-modal-submit {
            width: 46px;
            height: 46px;
            padding: 0;
            font-size: 0;
        }

        .search-modal-submit::after {
            content: '→';
            font-size: 20px;
        }

        .search-modal-clear {
            right: 56px;
            width: 32px;
            height: 32px;
        }
    }
</style>

<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 search-modal-content">
            <button type="button" class="btn-close search-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>

            <div class="search-modal-hero">
                <div class="search-modal-kicker">Find your favourite piece</div>
                <h5 class="search-modal-title" id="searchModalLabel">Search hand-painted fashion</h5>
                <p class="search-modal-text">Explore sarees, co-ord sets, custom art, twinning outfits and more.</p>

                <form action="{{ route('shop.search') }}" method="GET" class="search-modal-form">
                    <div class="search-input-wrap">
                        <span class="search-modal-icon" aria-hidden="true">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <circle cx="10" cy="10" r="7"></circle>
                                <line x1="16" y1="16" x2="21" y2="21"></line>
                            </svg>
                        </span>
                        <input
                            type="search"
                            name="q"
                            class="form-control search-modal-input"
                            placeholder="Search products, collections, designs..."
                            autocomplete="off"
                            required
                        >
                        <button type="button" class="search-modal-clear" id="clearSearchInput" aria-label="Clear search">&times;</button>
                        <button class="search-modal-submit" type="submit">Search</button>
                    </div>
                    <div class="search-suggestions" id="searchSuggestions" role="listbox"></div>
                </form>
            </div>

            <div class="search-modal-body">
                <div class="fw-semibold text-dark">Popular categories</div>
                <div class="search-chip-list">
                    @foreach($categories->take(6) as $category)
                        <a href="{{ route('shop.index', [$category->slug]) }}" class="search-chip">
                            <span>{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>