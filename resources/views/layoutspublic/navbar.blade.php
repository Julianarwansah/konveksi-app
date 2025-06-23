<!-- Header Section Begin -->
<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <div class="header__logo">
                    <a href="{{ url('/') }}"><img src="{{ asset('assetspublic/img/logo.png') }}" alt=""></a>
                </div>
            </div>
            <div class="col-lg-9 col-md-9">
                <nav class="header__menu mobile-menu">
                    <ul class="text-lg-end">
                        <li class="{{ request()->is('/') ? 'active' : '' }}"><a href="{{ url('/') }}">Home</a></li>

                        <li><a href="#">Shop</a>
                            <ul class="dropdown">
                                <li><a href="{{ route('produkshop.index') }}">Produk</a></li>
                                <li><a href="{{ route('custom.produk') }}">Custom Produk</a></li>
                            </ul>
                        </li>
                        
                        @auth('customer')
                            <!-- Menu untuk customer yang sudah login -->
                            <li class="{{ request()->is('contact') ? 'active' : '' }}"><a href="{{ url('/contact') }}">Contacts</a></li>
                            <li><a href="{{ route('cart.index') }}">Cart</a></li>
                            <li><a href="#">Profile</a>
                                <ul class="dropdown">
                                    <li><a href="{{ route('customer.profile') }}">Profile</a></li>
                                    <li><a href="{{ route('pesanan.customer') }}">Pesanan</a></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <!-- Menu untuk guest -->
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('customer.register.form') }}">Register</a></li>
                        @endauth
                    </ul>
                </nav>
            </div>
        </div>
        <div class="canvas__open"><i class="fa fa-bars"></i></div>
    </div>
</header>
<!-- Header Section End -->