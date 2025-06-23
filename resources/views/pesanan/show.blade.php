@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="container">
        <h4 class="fw-bold py-3 mb-4">Detail Pesanan</h4>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title fw-bold">Informasi Pesanan</h5>
                        <p><strong>ID Pesanan:</strong> {{ $pesanan->id }}</p>
                        <p><strong>Customer:</strong> {{ $pesanan->customer->nama ?? '-' }}</p>
                        <p><strong>Tanggal Pesanan:</strong> {{ $pesanan->created_at->format('d-m-Y H:i:s') }}</p>
                        <p><strong>Total Harga:</strong> Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
                        <p><strong>Status Pesanan:</strong> 
                            <span class="badge bg-{{ $pesanan->status == 'selesai' ? 'success' : ($pesanan->status == 'dibatalkan' ? 'danger' : 'warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $pesanan->status)) }}
                            </span>
                        </p>
                        @if($pesanan->tanggal_selesai)
                            <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($pesanan->tanggal_selesai)->format('d-m-Y') }}</p>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="card-title fw-bold">Informasi Pembayaran</h5>
                        @if($pesanan->pembayarans->isNotEmpty())
                            @foreach($pesanan->pembayarans as $pembayaran)
                                <div class="mb-3 p-2 border rounded">
                                    <p><strong>Metode:</strong> {{ $pembayaran->metode }}</p>
                                    <p><strong>Jumlah:</strong> Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $pembayaran->status == 'dikonfirmasi' ? 'success' : 'warning' }}">
                                            {{ ucfirst($pembayaran->status) }}
                                        </span>
                                    </p>
                                    <p><strong>Tanggal Bayar:</strong> {{ $pembayaran->tanggal_bayar?->format('d-m-Y H:i:s') ?? '-' }}</p>
                                    @if($pembayaran->bukti_bayar)
                                        <p><strong>Bukti Bayar:</strong> 
                                            <a href="{{ asset('storage/bukti_bayar/' . $pembayaran->bukti_bayar) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                Lihat Bukti
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Belum ada data pembayaran</p>
                        @endif
                    </div>
                </div>

                <hr>

                <!-- Detail Pesanan -->
                <h5 class="fw-bold">Detail Item Pesanan</h5>
                <div class="table-responsive">
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Deskripsi</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesanan->detailPesanan as $detail)
                                <tr>
                                    <td>{{ $detail->nama_item }}</td>
                                    <td>{{ $detail->deskripsi_item }}</td>
                                    <td>{{ $detail->jumlah }}</td>
                                    <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Pakaian Custom -->
                @if($pesanan->custom->isNotEmpty())
                    <hr>
                    <h5 class="fw-bold">Detail Pakaian Custom</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Model</th>
                                    <th>Ukuran</th>
                                    <th>Warna</th>
                                    <th>Jumlah</th>
                                    <th>Harga Estimasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pesanan->custom as $custom)
                                    <tr>
                                        <td>{{ $custom->template->nama ?? 'Model tidak ditemukan' }}</td>
                                        <td>{{ $custom->ukuran }}</td>
                                        <td>{{ $custom->warna }}</td>
                                        <td>{{ $custom->jumlah }}</td>
                                        <td>Rp {{ number_format($custom->harga_estimasi, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $custom->getStatusColor() }}">
                                                {{ $custom->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Pengiriman -->
                @if($pesanan->pengiriman)
                    <hr>
                    <h5 class="fw-bold">Informasi Pengiriman</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Kurir:</strong> {{ $pesanan->pengiriman->kurir }}</p>
                            <p><strong>No. Resi:</strong> {{ $pesanan->pengiriman->no_resi ?? '-' }}</p>
                            <p><strong>Status:</strong> {{ $pesanan->pengiriman->status }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Alamat:</strong> {{ $pesanan->pengiriman->alamat }}</p>
                            <p><strong>Biaya:</strong> Rp {{ number_format($pesanan->pengiriman->biaya, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Bagian Biaya Tambahan -->
                @if($pesanan->custom->isNotEmpty() && $pesanan->custom->first()->customBiayas->isNotEmpty())
                    <hr>
                    <h5 class="fw-bold">Biaya Tambahan</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Biaya</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pesanan->custom->first()->customBiayas as $customBiaya)
                                    <tr>
                                        <td>{{ $customBiaya->biayaTambahan->nama ?? '-' }}</td>
                                        <td>{{ $customBiaya->jumlah }}</td>
                                        <td>Rp {{ number_format($customBiaya->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('pesanan.index') }}" class="btn btn-secondary">Kembali</a>
                    <div>
                        @if($pesanan->custom->isNotEmpty())
                            <a href="{{ route('pesanan-pakaian-custom.edit', $pesanan->id) }}" class="btn btn-warning">Edit Pesanan Custom</a>
                        @else
                            <a href="{{ route('pesanan-produk.edit', $pesanan->id) }}" class="btn btn-warning">Edit Pesanan Produk</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection