<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="author" content="surfside media" />
    <link rel="stylesheet" type="text/css" href="{{ versionedAsset('css/animate.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ versionedAsset('css/animation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ versionedAsset('css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ versionedAsset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="{{ versionedAsset('css/style.css') }}">
    <link rel="stylesheet" href="{{ versionedAsset('icon/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.jpg') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('images/favicon.jpg') }}">
    <link rel="stylesheet" type="text/css" href="{{ versionedAsset('css/sweetalert.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ versionedAsset('css/custom.css') }}">

    @stack("styles")
</head>

<body class="body">
    <div id="wrapper">
        <div id="page" class="">
            <div class="layout-wrap">

                <!-- <div id="preload" class="preload-container">
                    <div class="preloading">
                        <span></span>
                    </div>
                </div> -->

                <div class="section-menu-left">
                    <div class="box-logo">
                        <a href="{{ route('admin.index') }}" id="site-logo-inner">
                            <h4 class="text-center">Design Dhaga</h4>
                        </a>
                        <div class="button-show-hide">
                            <i class="icon-menu-left"></i>
                        </div>
                    </div>
                    <div class="center">
                        <div class="center-item">
                            <div class="center-heading">Main Home</div>
                            <ul class="menu-list">
                                <li class="menu-item">
                                    <a href="{{ route('admin.index') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Dashboard</div>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="{{ route('admin.analytics') }}" class="">
                                        <div class="icon"><i class="icon-bar-chart"></i></div>
                                        <div class="text">Analytics</div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="center-item">
                            <ul class="menu-list">
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-shopping-cart"></i></div>
                                        <div class="text">Products</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.products.add')}}" class="">
                                                <div class="text">Add Product</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.products') }}" class="">
                                                <div class="text">Products</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-layers"></i></div>
                                        <div class="text">Category</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{route('admin.category.add')}}" class="">
                                                <div class="text">New Category</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{route('admin.categories')}}" class="">
                                                <div class="text">Categories</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-file-plus"></i></div>
                                        <div class="text">Order</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{route('admin.orders')}}" class="">
                                                <div class="text">Orders</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Coupons</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{route('admin.coupon.add')}}" class="">
                                                <div class="text">New Coupon</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{route('admin.coupons')}}" class="">
                                                <div class="text">Coupons</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Blogs</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{route('admin.blog.add')}}" class="">
                                                <div class="text">New Blog</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{route('admin.blogs')}}" class="">
                                                <div class="text">Blogs</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Testimonials</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{route('admin.testimonial.add')}}" class="">
                                                <div class="text">New Testimonial</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{route('admin.testimonials')}}" class="">
                                                <div class="text">Testimonials</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item">
                                    <a href="{{ route('admin.reviews') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Reviews</div>
                                    </a>
                                </li>
                                <li class="menu-item has-children">
                                    <a href="{{route('admin.contact.view')}}">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Queries</div>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="{{route('admin.sliders')}}" class="">
                                        <div class="icon"><i class="icon-image"></i></div>
                                        <div class="text">Slider</div>
                                    </a>
                                </li>

                                <li class="menu-item">
                                    <a href="{{ route('admin.menus.index') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Menu</div>
                                    </a>
                                </li>

                                <li class="menu-item">
                                    <a href="{{ route('footer.widgets') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Footer Menu</div>
                                    </a>
                                </li>

                                <li class="menu-item">
                                    <a href="{{ route('admin.stories') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Story</div>
                                    </a>
                                </li>

                                <li class="menu-item has-children">
                                    <a href="javascript:void(0);" class="menu-item-button">
                                        <div class="icon"><i class="icon-image"></i></div>
                                        <div class="text">Portfolio</div>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.portfolio.gallery.index') }}" class="">
                                                <div class="text">Gallery</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.portfolio.categories.index') }}" class="">
                                                <div class="text">Categories</div>
                                            </a>
                                        </li>
                                        <li class="sub-menu-item">
                                            <a href="{{ route('admin.portfolio.subcategories.index') }}" class="">
                                                <div class="text">Subcategories</div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="menu-item">
                                    <a href="{{ route('admin.home-page.index') }}" class="">
                                        <div class="icon"><i class="icon-home"></i></div>
                                        <div class="text">Home Page</div>
                                    </a>
                                </li>

                                <li class="menu-item">
                                    <a href="{{ route('admin.about.section') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">About Us</div>
                                    </a>
                                </li>
                                
                                <li class="menu-item">
                                    <a href="{{ route('admin.homepage-highlights.index') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">Highlights</div>
                                    </a>
                                </li>

                                <li class="menu-item">
                                    <a href="{{ route('admin.pages') }}">
                                        <div class="icon"><i class="icon-file-text"></i></div>
                                        <div class="text">Pages</div>
                                    </a>
                                </li>
                                
                                <li class="menu-item">
                                    <a href="{{ route('admin.faqs') }}" class="">
                                        <div class="icon"><i class="icon-grid"></i></div>
                                        <div class="text">FAQs</div>
                                    </a>
                                </li>

                                <li class="menu-item">
                                    <a href="{{route('admin.users')}}" class="">
                                        <div class="icon"><i class="icon-user"></i></div>
                                        <div class="text">User</div>
                                    </a>
                                </li>

                                <li class="menu-item">
                                    <a href="{{ route('admin.settings') }}" >
                                        <div class="icon"><i class="icon-settings"></i></div>
                                        <div class="text">Settings</div>
                                    </a>
                                </li>

                                <li class="menu-item">
                                    <form method="post" action="{{route('logout')}}" id="logout-form">
                                        @csrf
                                        <a href="#" class="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <div class="icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15 16L20 12L15 8" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    
                                                    <path d="M20 12H9" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    
                                                    <path d="M9 20H5C4.44772 20 4 19.5523 4 19V5C4 4.44772 4.44772 4 5 4H9" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                            <div class="text">Logout</div>
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="section-content-right">

                    <div class="header-dashboard">
                        <div class="wrap">
                            <div class="header-left">
                                <a href="index-2.html">
                                    <img class="" id="logo_header_mobile" alt="" src="{{ asset('images/logo/landscape-logo.svg') }}"
                                        data-light="{{ asset('images/logo/landscape-logo.svg') }}" data-dark="{{ asset('images/logo/landscape-logo.svg') }}"
                                        data-width="154px" data-height="52px" data-retina="{{ asset('images/logo/landscape-logo.svg') }}">
                                </a>
                                <div class="button-show-hide">
                                    <i class="icon-menu-left"></i>
                                </div>

                            </div>
                            <div class="header-grid">
                                <div class="popup-wrap user type-header">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="header-user wg-user">
                                                <span class="image">
                                                    <img src="{{ asset('images/avatar/user-1.png') }}" alt="">
                                                </span>
                                                <span class="flex flex-column">
                                                    <span class="body-title mb-2">{{Auth::user()->name}}</span>
                                                    <span class="text-tiny">Admin</span>
                                                </span>
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end has-content" aria-labelledby="dropdownMenuButton3">
                                            <li>
                                                <a href="{{route('logout')}}" class="user-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <div class="icon">
                                                        <i class="icon-log-out"></i>
                                                    </div>
                                                    <div class="body-title-2">Log out</div>
                                                </a>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                    @csrf   
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="main-content">
                        @session('success')
                        <p class="alert alert-success">{{ session('success') }}</p>
                        @endsession
                        @if(Session::has('error'))
                        <p class="alert alert-danger">{{ Session::get('error') }}</p>
                        @endif
                        @if(Session::has('status'))
                        <p class="alert alert-success">{{ Session::get('status') }}</p>
                        @endif

                        @yield('content')


                        <div class="bottom-page">
                            <div class="body-text">Copyright © @php echo date('Y') @endphp Design Dhaga</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ versionedAsset('js/jquery.min.js') }}"></script>
    <script src="{{ versionedAsset('js/bootstrap.min.js') }}"></script>
    <script src="{{ versionedAsset('js/bootstrap-select.min.js') }}"></script>
    <script src="{{ versionedAsset('js/sweetalert.min.js') }}"></script>
    <script src="{{ versionedAsset('js/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ versionedAsset('js/main.js') }}"></script>
    <script>
        $(function () {
            const currentParams = new URLSearchParams(window.location.search);

            function prepareServerSearch(form) {
                const searchInput = form.find('input[type="text"], input[type="search"]').first();

                form.attr('method', 'GET');

                if (!form.attr('action')) {
                    form.attr('action', window.location.pathname);
                }

                if (searchInput.length) {
                    searchInput.attr('name', 'search');
                    searchInput.prop('required', false).removeAttr('aria-required');

                    if (currentParams.has('search')) {
                        searchInput.val(currentParams.get('search'));
                    }
                }

                form.on('submit', function () {
                    if (searchInput.length && !String(searchInput.val() || '').trim()) {
                        searchInput.prop('disabled', true);
                    }
                });
            }

            $('.wg-table table, .table-all-user table, .table-responsive table, .table-scroll table').each(function (index) {
                const table = $(this);
                const box = table.closest('.wg-box');
                const tableShell = table.closest('.wg-table, .table-all-user, .table-responsive, .table-scroll');
                const hasPagination = box.find('.wgp-pagination').length > 0
                    && (box.find('.form-search').length > 0 || box.find('.wgp-pagination a, .pagination a').length > 0);
                let searchInput = box.find('.form-search input[type="text"], .form-search input[type="search"]').first();

                table.attr('id', table.attr('id') || 'admin-table-' + index);
                tableShell.addClass('admin-table-scroll');

                if (hasPagination) {
                    box.find('.form-search').each(function () {
                        prepareServerSearch($(this));
                    });
                    return;
                }

                if (!searchInput.length) {
                    const filter = $(`
                        <div class="admin-table-filter">
                            <form class="form-search admin-generated-filter" autocomplete="off">
                                <fieldset class="name">
                                    <input type="search" placeholder="Search list..." aria-label="Search table">
                                </fieldset>
                                <div class="button-submit">
                                    <button type="submit"><i class="icon-search"></i></button>
                                </div>
                            </form>
                            <div class="admin-table-filter-count"></div>
                        </div>
                    `);

                    tableShell.before(filter);
                    searchInput = filter.find('input');
                }

                const countLabel = searchInput.closest('.wg-box').find('.admin-table-filter-count').first();
                const rows = table.find('tbody tr').filter(function () {
                    return !$(this).find('td[colspan]').length;
                });

                if (!rows.length) {
                    countLabel.text('');
                    return;
                }

                const columnCount = table.find('thead th').length || table.find('tr:first-child td').length || 1;
                const emptyRow = $('<tr class="admin-table-empty-row" style="display:none;"><td colspan="' + columnCount + '">No matching records found.</td></tr>');

                table.find('tbody').append(emptyRow);

                function filterRows() {
                    const query = String(searchInput.val() || '').toLowerCase().trim();
                    let visible = 0;

                    rows.each(function () {
                        const row = $(this);
                        const matches = !query || row.text().toLowerCase().indexOf(query) !== -1;
                        row.toggle(matches);

                        if (matches) {
                            visible++;
                        }
                    });

                    emptyRow.toggle(visible === 0);
                    countLabel.text(visible + ' of ' + rows.length + ' shown');
                }

                searchInput.on('input', filterRows);
                searchInput.closest('.admin-generated-filter').on('submit', function (event) {
                    event.preventDefault();
                    filterRows();
                });

                filterRows();
            });
        });
    </script>
    
    @stack("scripts")
</body>

</html>
