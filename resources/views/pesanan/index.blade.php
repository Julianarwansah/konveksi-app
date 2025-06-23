@extends('layouts.app')

@section('title', 'Manajemen Pesanan')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Pesanan</h4>

        <!-- Header dengan Tombol Tambah dan Form Search -->
        <div class="row g-3 align-items-center mb-3">

            <!-- Form untuk Search -->
            <div class="col-12 col-md">
                <form action="{{ route('pesanan.index') }}" method="GET" class="row g-2 align-items-center">
                    <!-- Input Search -->
                    <div class="col-12 col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari pesanan Berdasarkan nama" value="{{ request('search') }}">
                    </div>
                    <!-- Dropdown Filter Status -->
                    <div class="col-12 col-md-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- Semua Status Pesanan --</option>
                            {{-- Mengambil status unik dari koleksi $pesanans --}}
                            @php
                                $uniqueStatuses = $pesanans->pluck('status')->unique()->sort()->toArray();
                            @endphp
                            @foreach($uniqueStatuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Dropdown Filter Jenis Pesanan -->
                    <div class="col-12 col-md-3">
                        <select name="jenis_pesanan" class="form-select form-select-sm">
                            <option value="">-- Semua Jenis Pesanan --</option>
                            <option value="produk" {{ request('jenis_pesanan') == 'produk' ? 'selected' : '' }}>Produk</option>
                            <option value="custom" {{ request('jenis_pesanan') == 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>

                    <!-- Tombol Cari -->
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notifikasi Sukses -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Notifikasi Error -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabel Pesanan -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Customer</th>
                            <th style="width: 10%;">Total Harga</th>
                            <th style="width: 10%;">Sisa Pembayaran</th>
                            <th style="width: 10%;">Status Pembayaran</th>
                            <th style="width: 10%;">Status Pesanan</th>
                            <th style="width: 10%;">Jenis Pesanan</th>
                            <th style="width: 10%;">Tanggal Selesai</th>
                            <th style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesanans as $pesanan)
                            <tr>
                                <td>{{ $pesanan->id }}</td>
                                <td>{{ $pesanan->customer->nama ?? '-' }}</td>
                                <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($pesanan->sisa_pembayaran, 0, ',', '.') }}</td>
                                <td>{{ $pesanan->status_pembayaran }}</td>
                                <td>{{ $pesanan->status }}</td>
                                <td>{{ ucfirst($pesanan->jenis_pesanan) }}</td>
                                <td>{{ $pesanan->tanggal_selesai ? \Carbon\Carbon::parse($pesanan->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('pesanan.show', $pesanan->id) }}" class="btn btn-info btn-sm">Detail</a>

                                        @if(auth()->user()->role->nama === 'Admin Ecommerce' && $pesanan->status != 'Selesai')
                                            @if(in_array($pesanan->status_pembayaran, ['DP', 'Lunas']))
                                                @if($pesanan->jenis_pesanan == 'produk')
                                                    <a href="{{ route('pesanan-produk.edit', $pesanan->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                @elseif($pesanan->jenis_pesanan == 'custom')
                                                    <a href="{{ route('pesanan-pakaian-custom.edit', $pesanan->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row">
                    <div class="text-muted small">
                        Menampilkan {{ $pesanans->firstItem() }} - {{ $pesanans->lastItem() }} dari total {{ $pesanans->total() }} pesanan
                    </div>
                    <div>
                        {{ $pesanans->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection