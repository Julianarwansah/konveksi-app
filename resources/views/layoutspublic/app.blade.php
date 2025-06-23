<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Male_Fashion Template">
    <meta name="keywords" content="Male_Fashion, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Konveksi Jul</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
    rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ asset('assetspublic/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assetspublic/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assetspublic/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assetspublic/css/magnific-popup.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assetspublic/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assetspublic/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assetspublic/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assetspublic/css/style.css') }}" type="text/css">
    <style>
        /* Atur menu hanya untuk desktop (lg ke atas) */
        @media (min-width: 992px) {
            /* Menu utama (hanya ul langsung di dalam .header__menu) */
            .header__menu > ul {
                display: flex;
                justify-content: flex-end;
                padding-left: 0;
                margin-bottom: 0;
                list-style: none;
            }

            .header__menu > ul > li {
                margin-left: 20px;
                position: relative; /* penting untuk dropdown */
            }

            /* Dropdown harus diatur khusus */
            .header__menu ul.dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                display: none;
                background: white; /* sesuaikan warna */
                padding: 10px 0;
                list-style: none;
                min-width: 180px; /* bisa disesuaikan */
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                z-index: 9999;
            }

            .header__menu ul.dropdown li {
                margin: 0;
                padding: 0;
            }

            .header__menu ul.dropdown li a {
                display: block;
                padding: 10px 20px;
                color: #333;
                white-space: nowrap;
            }

            .header__menu ul.dropdown li a:hover {
                background-color: #f2f2f2;
            }

            /* Tampilkan dropdown saat hover */
            .header__menu > ul > li:hover > ul.dropdown {
                display: block;
            }
        }
    </style>
    <style>
        .offcanvas-menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh; /* penuh tinggi viewport */
    background: rgba(0, 0, 0, 0.5);
    z-index: 9998;
    display: none; /* default */
}

.offcanvas-menu-wrapper {
    position: fixed;
    top: 0;           /* mulai dari atas */
    left: 0;
    width: 300px;     /* lebar menu sidebar, sesuaikan */
    height: 100vh;    /* penuh tinggi viewport */
    background: #fff;
    z-index: 9999;    /* di atas overlay */
    overflow-y: auto; /* scroll jika konten panjang */
    display: none;    /* default */
}

/* ketika menu aktif biasanya ada class khusus, contoh: */
.offcanvas-menu-wrapper.active,
.offcanvas-menu-overlay.active {
    display: block;
}

/* pastikan tidak ada padding/margin di body yang mendorong turun */
body, html {
    margin: 0;
    padding: 0;
    height: 100%;
}

    </style>
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    @include('layoutspublic.navbar')

    <!-- Offcanvas Menu Begin -->
    <div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div id="mobile-menu-wrap"></div>
        <div class="offcanvas__text">
            <p>Free shipping, 30-day return or refund guarantee.</p>
        </div>
    </div>
    <!-- Offcanvas Menu End -->

    <main>
        @yield('content')
    </main>

    @include('layoutspublic.footer')

    <!-- Search Begin -->
    <div class="search-model">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="search-close-switch">+</div>
            <form class="search-model-form">
                <input type="text" id="search-input" placeholder="Search here.....">
            </form>
        </div>
    </div>
    <!-- Search End -->

    <!-- Js Plugins -->
    <script src="{{ asset('assetspublic/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assetspublic/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assetspublic/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('assetspublic/js/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('assetspublic/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assetspublic/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('assetspublic/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('assetspublic/js/mixitup.min.js') }}"></script>
    <script src="{{ asset('assetspublic/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assetspublic/js/main.js') }}"></script>
</body>
</html>