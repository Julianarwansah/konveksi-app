@extends('layouts.app')

@section('title', 'Edit Pesanan Produk')

<style>
    .readonly-field {
        background-color: #e9ecef;
        pointer-events: none;
    }
    .detail-item {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
    }
</style>

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Pesanan Produk</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('pesanan-produk.update', $pesanan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Input Customer -->
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <input type="text" class="form-control readonly-field" 
                               value="{{ $pesanan->customer->nama }}" readonly>
                    </div>

                    <!-- Input Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Menunggu Pembayaran" {{ $pesanan->status == 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="Menunggu Konfirmasi" {{ $pesanan->status == 'Menunggu Konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                            <option value="Pembayaran Diverifikasi" {{ $pesanan->status == 'Pembayaran Diverifikasi' ? 'selected' : '' }}>Pembayaran Diverifikasi</option>
                            <option value="Sedang Pengemasan" {{ $pesanan->status == 'Sedang Pengemasan' ? 'selected' : '' }}>Sedang Pengemasan</option>
                            <option value="Siap Dikirim" {{ $pesanan->status == 'Siap Dikirim' ? 'selected' : '' }}>Siap Dikirim</option>
                            <option value="Dalam Pengiriman" {{ $pesanan->status == 'Dalam Pengiriman' ? 'selected' : '' }}>Dalam Pengiriman</option>
                            <option value="Selesai Pengiriman" {{ $pesanan->status == 'Selesai Pengiriman' ? 'selected' : '' }}>Selesai Pengiriman</option>
                            <option value="Selesai" {{ $pesanan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    <!-- Input Metode Pembayaran -->
                    <div class="mb-3" hidden>
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <input type="text" class="form-control readonly-field" 
                               value="{{ $pesanan->metode_pembayaran }}" readonly>
                    </div>

                    <!-- Input Total Harga -->
                    <div class="mb-3" hidden> 
                        <label for="total_harga" class="form-label">Total Harga</label>
                        <input type="text" class="form-control readonly-field" 
                               value="Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}" readonly>
                    </div>

                    <!-- Input Pembayaran Manual -->
                    <div class="mb-3" hidden>
                        <label for="pembayaran_manual" class="form-label">Pembayaran Manual</label>
                        <input type="text" class="form-control readonly-field" 
                               value="Rp {{ number_format($pesanan->pembayaran_manual, 0, ',', '.') }}" readonly>
                    </div>

                    <!-- Input Sisa Pembayaran -->
                    <div class="mb-3" hidden>
                        <label for="sisa_pembayaran" class="form-label">Sisa Pembayaran</label>
                        <input type="text" class="form-control readonly-field" 
                               value="Rp {{ number_format($pesanan->sisa_pembayaran, 0, ',', '.') }}" readonly>
                    </div>

                    <!-- Input Bukti Pembayaran -->
                    <div class="mb-3" hidden>
                        <label class="form-label">Bukti Pembayaran</label>
                        @if($pesanan->bukti_pembayaran)
                            <div class="mt-2">
                                <img src="{{ asset('storage/bukti_pembayaran/' . $pesanan->bukti_pembayaran) }}" 
                                     alt="Bukti Pembayaran" style="max-width: 200px;">
                            </div>
                        @else
                            <p class="text-muted">Tidak ada bukti pembayaran</p>
                        @endif
                    </div>

                    <!-- Detail Produk -->
                    <h5 class="mt-4 mb-3">Detail Produk</h5>
                    @foreach($pesanan->pesananDetails as $detail)
                        <div class="detail-item">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Produk</label>
                                    <input type="text" class="form-control readonly-field" 
                                           value="{{ $detail->produk->nama }}" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Ukuran</label>
                                    <input type="text" class="form-control readonly-field" 
                                           value="{{ $detail->ukuran }}" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Warna</label>
                                    <input type="text" class="form-control readonly-field" 
                                           value="{{ $detail->warna }}" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Jumlah</label>
                                    <input type="text" class="form-control readonly-field" 
                                           value="{{ $detail->jumlah }}" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Harga Satuan</label>
                                    <input type="text" class="form-control readonly-field" 
                                           value="Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}" readonly>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Tombol Simpan dan Batal -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Update Status</button>
                        <a href="{{ route('pesanan.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection