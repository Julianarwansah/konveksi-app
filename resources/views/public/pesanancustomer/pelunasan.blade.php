@extends('layoutspublic.app')

@section('content')
<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Pelunasan Pembayaran</h4>
                    <div class="breadcrumb__links">
                        <a href="{{ url('/') }}">Home</a>
                        <span>Pesanan Saya</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->
<div class="container mt-4">
  <h3>Pelunasan Pesanan #{{ $pesanan->id }}</h3>
  <p>Sisa Pembayaran: <strong>Rp {{ number_format($sisaPembayaran,2,',','.') }}</strong></p>

  <form action="{{ route('public.pesanan.storePelunasan', $pesanan->id) }}" 
        method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label for="jumlah" class="form-label">Jumlah Pelunasan</label>
      <input type="number" name="jumlah" step="0.01"
             max="{{ $sisaPembayaran }}" 
             value="{{ old('jumlah', $sisaPembayaran) }}"
             class="form-control @error('jumlah') is-invalid @enderror" required>
      @error('jumlah')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="metode" class="form-label">Metode Pembayaran</label>
      <select name="metode" 
              class="form-select @error('metode') is-invalid @enderror" required>
        <option value="">Pilih metode</option>
        <option>Transfer Bank</option>
      </select>
      @error('metode')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="bukti_bayar" class="form-label">
        Upload Bukti Bayar (opsional)
      </label>
      <input type="file" name="bukti_bayar" 
             class="form-control @error('bukti_bayar') is-invalid @enderror">
      @error('bukti_bayar')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="catatan" class="form-label">Catatan (opsional)</label>
      <textarea name="catatan" class="form-control">{{ old('catatan') }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">Kirim Pelunasan</button>
    <a href="{{ route('pesanan.pesanancustomerdetail', $pesanan->id) }}" class="btn btn-secondary">Batal</a>
  </form>
</div><br><br>
@endsection
