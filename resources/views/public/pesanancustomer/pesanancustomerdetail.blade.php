@extends('layoutspublic.app')

@section('content')
<div class="container mt-5" data-aos="fade-up">
    <div class="mb-4">
        <h2 class="fw-bold text-primary">Detail Pesanan #{{ $pesanan->id }}</h2>
        <a href="{{ route('pesanan.customer') }}" class="btn btn-outline-secondary mt-2">
            &larr; Kembali ke Daftar Pesanan
        </a>
    </div>

    <!-- INFORMASI UTAMA PESANAN -->
    <div class="card shadow-sm rounded-4 p-4 mb-4" data-aos="fade-up" data-aos-delay="100">
        <h5 class="mb-4 fw-bold">Informasi Pesanan</h5>
        <table class="table table-bordered">
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge 
                        @if($pesanan->status == 'Selesai') bg-success 
                        @elseif($pesanan->status == 'Diproses') bg-warning text-dark
                        @else bg-secondary @endif">
                        {{ ucfirst($pesanan->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Tanggal Selesai</th>
                <td>{{ $pesanan->tanggal_selesai ? \Carbon\Carbon::parse($pesanan->tanggal_selesai)->format('d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Total Harga</th>
                <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
            </tr>
            @php
                $totalDibayar = $pesanan->pembayaran->where('status', 'Berhasil')->sum('jumlah');
                $sisaPembayaran = $pesanan->total_harga - $totalDibayar;
            @endphp
            <tr>
                <th>Total Yang Sudah Dibayar</th>
                <td class="text-success fw-bold">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Sisa Pembayaran</th>
                <td class="text-danger fw-bold">
                    Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- ITEM PESANAN -->
    <div class="card shadow rounded-4 mb-4" data-aos="zoom-in" data-aos-delay="150">
        <div class="card-body table-responsive">
            <h5 class="fw-bold mb-3">Rincian Produk</h5>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Produk / Custom</th>
                        <th>Ukuran</th>
                        <th>Warna</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pesanan->detailPesanan as $detail)
                        <tr>
                            <td>
                                @if($detail->produk_id)
                                    <span class="fw-semibold">{{ $detail->produk->nama }}</span>
                                @elseif($detail->custom_id)
                                    <span class="text-muted">Custom: {{ $detail->custom->model ?? 'Tanpa Model' }}</span>
                                @else
                                    <em class="text-danger">Item Tidak Dikenali</em>
                                @endif
                            </td>
                            <td>{{ $detail->ukuran }}</td>
                            <td>{{ $detail->warna }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end">Total Harga</th>
                        <th class="text-success">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- DETAIL PEMBAYARAN -->
    @if($pesanan->pembayaran->count() > 0)
    <div class="card shadow-sm rounded-4 p-4" data-aos="fade-up" data-aos-delay="250">
        <h5 class="mb-3 fw-bold">Detail Pembayaran</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal Bayar</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Jenis</th>
                        <th>Bukti</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pesanan->pembayaran as $bayar)
                    <tr>
                        <td>{{ $bayar->tanggal_bayar ? $bayar->tanggal_bayar->format('d-m-Y H:i') : '-' }}</td>
                        <td>{{ ucfirst($bayar->metode) }}</td>
                        <td>Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge 
                                @if($bayar->status == 'Diterima') bg-success 
                                @elseif($bayar->status == 'Ditolak') bg-danger 
                                @else bg-warning text-dark @endif">
                                {{ $bayar->status }}
                            </span>
                        </td>
                        <td>{{ $bayar->is_dp ? 'Uang Muka' : 'Pelunasan' }}</td>
                        <td>
                            @if ($bayar->bukti_bayar)
                                <a href="{{ asset('storage/' . $bayar->bukti_bayar) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $bayar->catatan ?? '-' }}</td>
                        <td>
                            <!-- Tombol PDF -->
                            <a href="{{ route('pembayaran.pdf', $bayar->id) }}" class="btn btn-sm btn-danger">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div><br><br>
@endsection