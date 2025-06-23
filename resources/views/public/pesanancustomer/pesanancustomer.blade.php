@extends('layoutspublic.app')

@section('content')

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Pesanan Saya</h4>
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

<!-- Shop Section Begin -->
<section class="shop spad">
    <div class="container mt-4">
        <h2 class="mb-4">Daftar Pesanan Saya</h2>

        {{-- Pesan sukses --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Tabel daftar pesanan --}}
        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>ID Pesanan</th>
                    <th>Jenis Pesanan</th>
                    <th>Total Harga</th>
                    <th>Sisa Pembayaran</th>
                    <th>Status (Pesanan)</th>
                    <th>Status Pembayaran</th>
                    <th>Tanggal Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pesanan as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>{{ ucfirst($p->jenis_pesanan) }}</td> 
                        <td>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($p->sisa_pembayaran, 0, ',', '.') }}</td>
                        <td>{{ $p->status }}</td>
                        <td>{{ $p->status_pembayaran }}</td>
                        <td>
                            {{ $p->tanggal_selesai 
                                ? \Carbon\Carbon::parse($p->tanggal_selesai)->format('d-m-Y') 
                                : '-' 
                            }}
                        </td>
                        <td>
    {{-- Lihat detail --}}
    <a href="{{ route('pesanan.pesanancustomerdetail', $p->id) }}"
       class="btn btn-primary btn-sm">
        Lihat Detail
    </a>

    {{-- Tombol Bayar Ulang jika status pembayaran gagal --}}
    @if($p->status_pembayaran === 'Pembayaran Gagal')
        <a href="{{ route('pesanan.bayar-ulang', $p->id) }}"
           class="btn btn-danger btn-sm">
            Bayar Ulang
        </a>
    @endif

    {{-- Tombol pelunasan hanya jika status_pembayaran == 'DP' --}}
    @if($p->status_pembayaran === 'DP')
        <a href="{{ route('public.pesanan.pelunasanForm', $p->id) }}"
           class="btn btn-warning btn-sm">
            Bayar Pelunasan
        </a>
    @endif

    {{-- Tombol ubah status menjadi "Selesai" jika status = Dalam Pengiriman --}}
    @if($p->status === 'Dalam Pengiriman')
        <form action="{{ route('pesanan.selesaikan', $p->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success btn-sm"
                    onclick="return confirm('Apakah Anda yakin ingin menyelesaikan pesanan ini?')">
                Tandai Selesai
            </button>
        </form>
    @endif
</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada pesanan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
<!-- Shop Section End -->

@endsection
