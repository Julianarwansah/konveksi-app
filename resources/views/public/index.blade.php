@extends('layoutspublic.app')

@section('content')
    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="hero__slider owl-carousel">
            <!-- Slide Produk Jadi -->
            <div class="hero__items set-bg" data-setbg="{{ asset('assetspublic/img/hero/background1.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Koleksi Eksklusif</h6>
                                <h2>Koleksi Siap Pakai 2025</h2>
                                <p>Temukan gaya sempurna dengan koleksi produk jadi kami. Dibuat dengan bahan premium dan detail craftsmanship terbaik untuk penampilan yang memukau.</p>
                                <a href="#" class="primary-btn">Belanja Sekarang <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide Produk Custom -->
            <div class="hero__items set-bg" data-setbg="{{ asset('assetspublic/img/hero/background2.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Kreasi Personal</h6>
                                <h2>Kustomisasi Desainmu</h2>
                                <p>Wujudkan imajinasi Anda dengan layanan custom made kami. Setiap detail dibuat khusus sesuai keinginan Anda, untuk gaya yang benar-benar unik dan personal.</p>
                                <a href="#" class="primary-btn">Pesan Sekarang <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Banner Section Begin -->
    <section class="banner spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 offset-lg-4">
                    <div class="banner__item">
                        <div class="banner__item__pic">
                            <img src="{{ asset('assetspublic/img/banner/banner-1.jpg') }}" alt="">
                        </div>
                        <div class="banner__item__text">
                            <h2>Clothing Collections 2025</h2>
                            <a href="#">Shop now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="banner__item banner__item--middle">
                        <div class="banner__item__pic">
                            <img src="{{ asset('assetspublic/img/banner/banner-2.jpg') }}" alt="">
                        </div>
                        <div class="banner__item__text">
                            <h2>Accessories</h2>
                            <a href="#">Shop now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="banner__item banner__item--last">
                        <div class="banner__item__pic">
                            <img src="{{ asset('assetspublic/img/banner/banner-3.jpg') }}" alt="">
                        </div>
                        <div class="banner__item__text">
                            <h2>Shoes Spring 2030</h2>
                            <a href="#">Shop now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner Section End -->

    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="filter__controls">
                        <li class="active" data-filter="*">Best Sellers</li>
                        @foreach($products->pluck('kategori')->unique() as $kategori)
                            @php
                                $filterClass = strtolower(str_replace(' ', '-', $kategori));
                            @endphp
                            <li data-filter=".{{ $filterClass }}">{{ ucwords($kategori) }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="row product__filter">
                @foreach ($products as $product)
                    @php
                        $filterClass = strtolower(str_replace(' ', '-', $product->kategori));
                    @endphp
                    <div class="col-lg-3 col-md-6 col-sm-6 mix {{ $filterClass }}">
                        <div class="product__item {{ $product->label == 'Sale' ? 'sale' : '' }}">
                            {{-- Gambar produk --}}
                            <div class="product__item__pic set-bg" data-setbg="{{ asset('storage/' . $product->img) }}">
                                @if($product->label)
                                    <span class="label">{{ $product->label }}</span>
                                @endif
                                <ul class="product__hover">
                                    <li>
                                            <a href="{{ route('produkshopdetail.detail', ['id' => $product->id]) }}">
                                            <img src="{{ asset('assetspublic/img/icon/search.png') }}" alt=""><span>Detail</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            {{-- Informasi produk --}}
                            <div class="product__item__text">
                                <h6>{{ $product->nama }}</h6>

                                {{-- Rating --}}
                                <div class="rating">
                                    @for($i = 0; $i < 5; $i++)
                                        @if($i < $product->rating)
                                            <i class="fa fa-star"></i>
                                        @else
                                            <i class="fa fa-star-o"></i>
                                        @endif
                                    @endfor
                                </div>

                                <h5>Rp{{ number_format($product->harga, 0, ',', '.') }}</h5>

                                {{-- Warna produk dari database --}}
                                <div class="product__color__select">
                                    @foreach($product->warna as $warna)
                                        <label style="background-color: {{ $warna->warna }};" for="pc-{{ $product->id }}-{{ $loop->index }}">
                                            <input type="radio" name="warna_{{ $product->id }}" id="pc-{{ $product->id }}-{{ $loop->index }}">
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Product Section End -->

    <!-- Instagram Section Begin -->
    <section class="instagram spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="instagram__pic">
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('assetspublic/img/instagram/instagram-1.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('assetspublic/img/instagram/instagram-2.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('assetspublic/img/instagram/instagram-3.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('assetspublic/img/instagram/instagram-4.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('assetspublic/img/instagram/instagram-5.jpg') }}"></div>
                        <div class="instagram__pic__item set-bg" data-setbg="{{ asset('assetspublic/img/instagram/instagram-6.jpg') }}"></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="instagram__text">
                        <h2>Instagram</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                        labore et dolore magna aliqua.</p>
                        <h3>#Male_Fashion</h3>
                    </div>
                </div>
            </div>
        </div>
    </section><br><br><br>
    <!-- Instagram Section End -->
@endsection