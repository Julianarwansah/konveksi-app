@extends('layoutspublic.app')

@section('content')
<!-- Checkout Section Begin -->
<section class="checkout spad">
    <div class="container">
        <div class="checkout__form">
            <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Billing Details (Left Column) -->
                    <div class="col-lg-12 col-md-12">
                        <h6 class="checkout__title">Billing Details</h6>

                        <div class="checkout__input">
                            <p><strong>Full Name</strong><span>*</span></p>
                            <input type="text" name="nama" value="{{ old('nama', auth()->user()->nama ?? '') }}" readonly>
                        </div>

                        <div class="checkout__input">
                            <p><strong>Address</strong><span>*</span></p>
                            <input type="text" name="alamat" placeholder="Street Address" class="checkout__input__add" value="{{ old('alamat', auth()->user()->alamat ?? '') }}" readonly>
                        </div>

                        <div class="checkout__input">
                            <p><strong>Phone</strong><span>*</span></p>
                            <input type="text" name="no_telp" value="{{ old('no_telp', auth()->user()->no_telp ?? '') }}" readonly>
                        </div>

                        <div class="checkout__input">
                            <p><strong>Email</strong><span>*</span></p>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" readonly>
                        </div>

                        <!-- Hidden Inputs -->
                        <input type="hidden" name="customer_id" value="{{ auth()->user()->id }}">
                        <input type="hidden" name="total_harga" value="{{ $totalHarga }}">
                        <input type="hidden" name="status" value="Menunggu Konfirmasi">
                    </div>

                    <!-- Order Summary (Right Column) -->
                    <div class="col-lg-12 col-md-12">
                        <div class="checkout__order">
                            <h4 class="order__title">Your order</h4>
                            <div class="checkout__order__products">Product <span>Total</span></div>
                            <ul class="checkout__total__products">
                                @foreach($keranjang as $item)
                                    <li>
                                        {{ $loop->iteration < 10 ? '0'.$loop->iteration : $loop->iteration }}. 
                                        {{ $item->produk->nama ?? 'Custom Produk' }} x{{ $item->jumlah }} 
                                        <span>Rp {{ number_format(($item->harga ?? $item->produk->harga) * $item->jumlah, 0, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <ul class="checkout__total__all">
                                <li>Total <span>Rp {{ number_format($totalHarga, 0, ',', '.') }}</span></li>
                            </ul>

                            <!-- Pembayaran Section - Diperbarui -->
                            <h6 class="checkout__title">Payment Method</h6>
                            <div class="checkout__input__checkbox">
                                <label for="payment-transfer">
                                    Transfer
                                    <input type="radio" name="pembayaran[0][metode]" value="Transfer" checked>
                                    <span class="checkmark"></span>
                                </label>
                            </div>

                            <div class="checkout__input">
                                <p>Jumlah Bayar<span>*</span></p>
                                <input type="number" name="pembayaran[0][jumlah]" value="{{ $totalHarga }}" required>
                            </div>
                            
                            <div class="checkout__input">
                                <p>Tanggal Bayar</p>
                                <input type="date" name="pembayaran[0][tanggal_bayar]" value="{{ now()->toDateString() }}" readonly style="background-color:#f0f0f0;">
                            </div>
                            
                            <div class="checkout__input">
                                <p>Status Pembayaran</p>
                                <input type="text" name="pembayaran[0][status]" value="Menunggu Konfirmasi" readonly>
                            </div>
                            
                            <div class="checkout__input">
                                <p>Catatan Pembayaran</p>
                                <input type="text" name="pembayaran[0][catatan]" placeholder="Contoh: DP 50% atau Pelunasan">
                            </div>
                            
                            <div class="checkout__input__checkbox">
                                <label for="is_dp">
                                    Pembayaran DP?
                                    <input type="checkbox" name="pembayaran[0][is_dp]" value="1" id="is_dp">
                                    <span class="checkmark"></span>
                                </label>
                            </div>

                            <div class="checkout__input">
                                <p>Bukti Pembayaran (Jika Transfer)</p>
                                <input type="file" name="pembayaran[0][bukti_bayar]">
                            </div>

                            <!-- Detail Produk - Diperbarui -->
                            <div id="detail-produk-list">
                                @foreach($keranjang as $item)
                                    <input type="hidden" name="detail_pesanan[{{ $loop->index }}][produk_id]" value="{{ $item->produk_id }}">
                                    <input type="hidden" name="detail_pesanan[{{ $loop->index }}][custom_id]" value="{{ $item->custom_id }}">
                                    <input type="hidden" name="detail_pesanan[{{ $loop->index }}][jumlah]" value="{{ $item->jumlah }}">
                                    <input type="hidden" name="detail_pesanan[{{ $loop->index }}][ukuran]" value="{{ $item->ukuran }}">
                                    <input type="hidden" name="detail_pesanan[{{ $loop->index }}][warna]" value="{{ $item->warna }}">
                                    <input type="hidden" name="detail_pesanan[{{ $loop->index }}][harga]" value="{{ $item->harga ?? $item->produk->harga }}">
                                @endforeach
                            </div>

                            <button type="submit" class="site-btn">PLACE ORDER</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- Checkout Section End -->
@endsection