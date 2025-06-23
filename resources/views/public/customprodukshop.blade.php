@extends('layoutspublic.app')

@section('content')
<style>
    .product__item {
        transition: all 0.3s ease-in-out;
        cursor: pointer;
        margin-bottom: 30px;
    }

    .product__item:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        transform: scale(1.05);
        transition: all 0.3s ease-in-out;
    }

    .product__item.clicked {
        animation: clickEffect 0.2s forwards;
    }

    @keyframes clickEffect {
        0% { transform: scale(1.05); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
        50% { transform: scale(0.95); box-shadow: 0 0 15px rgba(255, 20, 147, 0.7); }
        100% { transform: scale(1.05); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
    }

    /* Style untuk kategori */
    select option:checked {
        font-weight: bold;
        color: #e83e8c;
    }

    select {
        padding: 5px 10px;
        border-radius: 4px;
        border: 1px solid #ddd;
        cursor: pointer;
        background-color: white;
    }

    .category-filter {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .category-filter p {
        margin-bottom: 0;
        font-weight: 600;
    }
    .shop__product__option__right {
        display: flex;
        justify-content: flex-end;
    }
</style>

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Custom Produk</h4>
                    <div class="breadcrumb__links">
                        <a href="{{ url('/') }}">Home</a>
                        <span>Custom Produk</span>
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
                                <p>Showing 1–{{ $templates->count() }} of {{ $templates->count() }} results</p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 text-end">
                            <div class="shop__product__option__right d-inline-block">
                                <div class="category-filter">
                                    <p>Kategori:</p>
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
                </div>

                <div class="row">
                    @foreach ($templates as $template)
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="product__item" 
                                 onclick="handleClick(this, '{{ route('custom.detail', $template->id) }}')">
                                <div class="product__item__pic set-bg" 
                                     data-setbg="{{ $template->gambar->isNotEmpty() ? asset('storage/' . $template->gambar->first()->gambar) : asset('assetspublic/img/placeholder.jpg') }}">
                                    <ul class="product__hover">
                                        <li>
                                            <a href="{{ route('custom.detail', $template->id) }}">
                                                <img src="{{ asset('assetspublic/img/icon/search.png') }}" alt="">
                                                <span>Detail</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="product__item__text">
                                    <h6>{{ $template->model }}</h6>
                                    <p class="small">{{ Str::limit($template->deskripsi, 50) }}</p>
                                    <h5>Rp{{ number_format($template->harga_estimasi, 0, ',', '.') }}</h5>
                                    <div class="product__color__select">
                                        @foreach($template->warna as $warna)
                                            <label style="background-color: {{ $warna->warna }};" 
                                                   for="pc-{{ $template->id }}-{{ $loop->index }}">
                                                <input type="radio" name="warna_{{ $template->id }}" 
                                                       id="pc-{{ $template->id }}-{{ $loop->index }}">
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Shop Section End -->
 <script>
    function filterByCategory(category) {
        let url = new URL(window.location.href);
        if (category) {
            url.searchParams.set('category', category);
        } else {
            url.searchParams.delete('category');
        }
        window.location.href = url.toString();
    }

    function handleClick(element, url) {
        element.classList.add('clicked');
        setTimeout(() => {
            window.location.href = url;
        }, 200);
    }
</script>
@endsection