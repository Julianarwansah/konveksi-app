@extends('layoutspublic.app')

@section('content')
<style>
    .product__item {
        transition: all 0.3s ease-in-out;
        cursor: pointer;
        /* biar smooth transform */
    }

    /* Efek saat hover */
    .product__item:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        transform: scale(1.05);
        transition: all 0.3s ease-in-out;
    }

    /* Efek saat diklik */
    .product__item.clicked {
        animation: clickEffect 0.2s forwards;
    }

    @keyframes clickEffect {
        0% {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        50% {
            transform: scale(0.95);
            box-shadow: 0 0 15px rgba(255, 20, 147, 0.7); /* pink glow */
        }
        100% {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
    }
</style>
<style>
    /* Tambahkan di bagian style */
    select option:checked {
        font-weight: bold;
        color: #e83e8c;
    }

    select {
        padding: 5px 10px;
        border-radius: 4px;
        border: 1px solid #ddd;
        cursor: pointer;
    }
</style>

</style>
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Shop Produk Jadi</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ url('/') }}">Home</a>
                            <span>Shop Produk Jadi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Shop Section Begin -->
    <section class="shop spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="shop__product__option">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="shop__product__option__left">
                                    <p>Showing 1–{{ $products->count() }} of {{ $products->count() }} results</p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="shop__product__option__right">
                                    <p>Category Baju:</p>
                                    <select onchange="filterByCategory(this.value)">
                                        <option value="" {{ $activeCategory == '' ? 'selected' : '' }}>Semua Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ $activeCategory == $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="product__item {{ $product->label == 'Sale' ? 'sale' : '' }}" 
                                onclick="handleClick(this, '{{ route('produkshopdetail.detail', ['id' => $product->id]) }}');">
                                
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

                                <div class="product__item__text">
                                    <h6>{{ $product->nama }}</h6>
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

                <!-- pagination dll... -->
            </div>
        </div>
    </div>
</section>

<script>
    function handleClick(element, url) {
        // Tambah class clicked untuk animasi klik
        element.classList.add('clicked');

        // Setelah 200ms (durasi animasi), pindah halaman
        setTimeout(() => {
            window.location.href = url;
        }, 200);
    }
</script>
<script>
    function filterByCategory(category) {
        // Buat URL dengan parameter category
        let url = new URL(window.location.href);
        
        if (category) {
            url.searchParams.set('category', category);
        } else {
            url.searchParams.delete('category');
        }
        
        window.location.href = url.toString();
    }
    
    // Fungsi handleClick yang sudah ada
    function handleClick(element, url) {
        element.classList.add('clicked');
        setTimeout(() => {
            window.location.href = url;
        }, 200);
    }
</script>
@endsection
