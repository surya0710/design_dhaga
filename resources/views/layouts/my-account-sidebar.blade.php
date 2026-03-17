<div class="wrap-sidebar-account">
    <div class="sidebar-account">
        <div class="account-avatar">
            <!-- <div class="image">
                <img src="images/avatar/user-account.jpg" alt="">
            </div> -->
            <h6 class="mb_4">{{isset($user->name) ? $user->name : 'Guest'}}</h6>
            <div class="body-text-1">{{isset($user->email) ? $user->email : ''}}</div>
        </div>
        <ul class="my-account-nav">
            <li>
                <a href="{{route('account.index')}}" class="my-account-nav-item {{ Route::is('account.index') ? 'active' : '' }}">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    Account Details
                </a>
            </li>
            <li>
                <a href="{{route('user.orders')}}" class="my-account-nav-item {{ Route::is(['user.orders','user.order.detail']) ? 'active' : '' }}">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.5078 10.8734V6.36686C16.5078 5.17166 16.033 4.02541 15.1879 3.18028C14.3428 2.33514 13.1965 1.86035 12.0013 1.86035C10.8061 1.86035 9.65985 2.33514 8.81472 3.18028C7.96958 4.02541 7.49479 5.17166 7.49479 6.36686V10.8734M4.11491 8.62012H19.8877L21.0143 22.1396H2.98828L4.11491 8.62012Z" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    Your Orders
                </a>
            </li>
            <!-- <li>
                <a href="my-account-address.html" class="my-account-nav-item ">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    My Address
                </a>
            </li> -->
            <li>
                <a href="{{route('logout')}}" class="my-account-nav-item">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M16 17L21 12L16 7" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M21 12H9" stroke="#181818" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</div>


<!-- sidebar account-->
    <div class="offcanvas offcanvas-start canvas-sidebar" id="mbAccount">
        <div class="canvas-wrapper">
            <header class="canvas-header">
                <span class="text-btn-uppercase">SIDEBAR ACCOUNT</span>
                <span class="icon-close icon-close-popup" data-bs-dismiss="offcanvas" aria-label="Close"></span>
            </header>
            <div class="canvas-body sidebar-mobile-append"></div>
        </div>       
    </div>
    <!-- End sidebar account -->