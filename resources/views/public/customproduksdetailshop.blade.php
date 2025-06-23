@extends('layoutspublic.app')

@section('content')
    <!-- Shop Details Section Begin -->
    <section class="shop-details">
        <div class="product__details__pic">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__breadcrumb">
                            <a href="./index.html">Home</a>
                            <a href="./shop.html">Shop</a>
                            <span>Product Details</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3">
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($template->gambar as $key => $gambar)
                            <li class="nav-item">
                                <a class="nav-link {{ $key == 0 ? 'active' : '' }}" data-toggle="tab" href="#tabs-{{ $key+1 }}" role="tab">
                                    <div class="product__thumb__pic set-bg" data-setbg="{{ asset('storage/'.$gambar->gambar) }}">
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-lg-6 col-md-9">
                        <div class="tab-content">
                            @foreach($template->gambar as $key => $gambar)
                            <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="tabs-{{ $key+1 }}" role="tabpanel">
                                <div class="product__details__pic__item">
                                    <img src="{{ asset('storage/'.$gambar->gambar) }}" alt="{{ $template->model }}">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="product__details__content">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-8">
                        <div class="product__details__text">
                            <h4>{{ $template->model }}</h4>
                            <div class="rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-o"></i>
                                <span> - 5 Reviews</span>
                            </div>
                            <h3>Rp {{ number_format($template->harga_estimasi, 0, ',', '.') }}</h3>
                            
                            <form id="customOrderForm" action="{{ route('public.pesanan.custom.checkout') }}" method="GET">
                                @csrf
                                <input type="hidden" name="template_id" value="{{ $template->id }}">

                                @if($template->warna->count() > 0)
                                <div class="product__details__option__color">
                                    <span>Color:</span>
                                    @foreach($template->warna as $idx => $warna)
                                    <label class="color-label" 
                                        style="background-color: {{ $warna->warna }};" 
                                        title="{{ $warna->warna }}">
                                        <input type="radio" 
                                            name="warna_id" 
                                            value="{{ $warna->id }}"
                                            @if($idx === 0) checked @endif> {{-- default pilih yang pertama --}}
                                    </label>
                                    @endforeach
                                </div><br><br>
                                @endif

                                <button type="submit" class="primary-btn">Pesan Custom</button>
                            </form>

                            <div class="product__details__last__option">
                                <h5><span>Guaranteed Safe Checkout</span></h5>
                                <img src="{{ asset('assetspublic/img/shop-details/details-payment.png') }}" alt="Payment Methods">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="product__details__tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tabs-5" role="tab">Description</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tabs-7" role="tab">Detail Bahan</a>
                                </li>
                            </ul>
                            
                            <div class="tab-content">
                                <div class="tab-pane active" id="tabs-5" role="tabpanel">
                                    <div class="product__details__tab__content">
                                        <div class="note">{!! $template->deskripsi !!}</div>
                                    </div>
                                </div>
                                
                                <div class="tab-pane" id="tabs-7" role="tabpanel">
                                    <div class="product__details__tab__content">
                                        <div class="product__details__tab__content__item">
                                            <h5>Bahan Pembuatan</h5>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Bahan</th>
                                                        <th>Jumlah</th>
                                                        <th>Harga</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($template->details as $detail)
                                                    <tr>
                                                        <td>{{ $detail->bahan->nama }}</td>
                                                        <td>{{ $detail->jumlah }} {{ $detail->bahan->satuan }}</td>
                                                        <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shop Details Section End -->

@section('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi nilai default
        @if($template->warna->count() > 0)
        $('#selectedColor').val($('input[name="color"]:first').val());
        @endif

        // Tangkap perubahan warna yang dipilih
        $('input[name="color"]').change(function() {
            $('#selectedColor').val($(this).val());
            console.log('Warna dipilih:', $(this).val()); // Debugging
        });

        // Validasi sebelum submit
        $('#customOrderForm').on('submit', function(e) {
            @if($template->warna->count() > 0)
            if (!$('#selectedColor').val()) {
                alert('Silakan pilih warna terlebih dahulu');
                return false;
            }
            @endif
            console.log('Data yang akan dikirim:', {
                template_id: $('input[name="template_id"]').val(),
                warna_id: $('#selectedColor').val()
            }); // Debugging
            return true;
        });
    });
</script>
@endsection
@endsection