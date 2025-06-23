@extends('layoutspublic.app')

@section('title', 'Checkout Pesanan')

@section('content')
<!-- Checkout Section Begin -->
<section class="checkout spad">
    <div class="container">
        <div class="checkout__form">
            <form action="{{ route('public.pesanan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8 col-md-6">
                        <h6 class="checkout__title">Informasi Pelanggan</h6>
                        
                        <!-- User Information Section -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="checkout__input mb-3">
                                            <p class="mb-1"><strong>Nama Lengkap</strong></p>
                                            <div class="form-control-plaintext">{{ auth()->user()->nama ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="checkout__input mb-3">
                                            <p class="mb-1"><strong>Email</strong></p>
                                            <div class="form-control-plaintext">{{ auth()->user()->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="checkout__input mb-3">
                                            <p class="mb-1"><strong>Nomor Telepon</strong></p>
                                            <div class="form-control-plaintext">{{ auth()->user()->no_telp ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="checkout__input mb-3">
                                            <p class="mb-1"><strong>Alamat</strong></p>
                                            <div class="form-control-plaintext">{{ auth()->user()->alamat ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('customer.profile') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-edit"></i> Update Profil
                                    </a>
                                </div>
                            </div>
                        </div>

                        <h6 class="checkout__title mt-4">Ringkasan Pesanan</h6>
                        
                        @if($cartItems->isEmpty())
                            <div class="alert alert-warning">Keranjang belanja Anda kosong.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Produk</th>
                                            <th>Warna</th>
                                            <th>Ukuran</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cartItems as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->produk->gambar)
                                                        <img src="{{ asset('storage/' . $item->produk->gambar) }}" 
                                                             alt="{{ $item->produk->nama }}" 
                                                             width="60" class="mr-3">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $item->produk->nama }}</h6>
                                                        <small class="text-muted">Kode: {{ $item->produk->kode }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item->warna->warna ?? '-' }}</td>
                                            <td>{{ $item->ukuran->ukuran ?? '-' }}</td>
                                            <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                            <td>{{ $item->jumlah }}</td>
                                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="checkout__order">
                            <h4 class="order__title mb-4">Pembayaran</h4>
                            
                            <!-- Ringkasan Total -->
                            <div class="checkout__total mb-4 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold">Total Pembayaran:</h5>
                                    <h4 class="mb-0 text-primary fw-bold">Rp {{ number_format($totalHarga, 0, ',', '.') }}</h4>
                                </div>
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="checkout__payment-method mb-4">
                                <h6 class="mb-3 fw-bold">Metode Pembayaran <span class="text-danger">*</span></h6>
                                <select class="form-select form-select-lg @error('metode_pembayaran') is-invalid @enderror" 
                                        id="metode_pembayaran" name="metode_pembayaran" required>
                                    <option value="">Pilih Metode Pembayaran</option>
                                    <option value="Transfer Bank" {{ old('metode_pembayaran') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="E-Wallet" {{ old('metode_pembayaran') == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                                    <option value="COD" {{ old('metode_pembayaran') == 'COD' ? 'selected' : '' }}>Cash On Delivery (COD)</option>
                                </select>
                                @error('metode_pembayaran')
                                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload Bukti Pembayaran -->
                            <div class="checkout__upload mb-4">
                                <h6 class="mb-3 fw-bold">Upload Bukti Pembayaran <span class="text-danger">*</span></h6>
                                <div class="file-upload-wrapper">
                                    <div class="input-group">
                                        <input type="file" class="form-control form-control-lg @error('bukti_bayar') is-invalid @enderror" 
                                            id="bukti_bayar" name="bukti_bayar" accept="image/*">
                                    </div>
                                    <small class="text-muted d-block mt-2">Format: JPG, PNG (Maksimal 2MB)</small>
                                    @error('bukti_bayar')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div class="checkout__notes mb-4">
                                <h6 class="mb-3 fw-bold">Catatan (Opsional)</h6>
                                <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                        placeholder="Catatan tambahan untuk pesanan Anda">{{ old('catatan') }}</textarea>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="checkout__actions">
                                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 mb-3" {{ $cartItems->isEmpty() ? 'disabled' : '' }}>
                                    <i class="fas fa-shopping-bag me-2"></i> Buat Pesanan
                                </button>
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-lg w-100 py-3">
                                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Keranjang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- Checkout Section End -->
@endsection