@extends('layouts.app')

@section('title', 'Manajemen Custom Produk')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Custom Produk</h4>

        <!-- Header dengan Tombol Tambah dan Form Search -->
        <div class="row g-3 align-items-center mb-3">
            <!-- Tombol Tambah Custom Produk -->
            <div class="col-12 col-md-auto">
                <a href="{{ route('custom.create') }}" class="btn btn-primary w-100 w-md-auto" hidden>
                    <i class="fas fa-plus me-2"></i>Tambah Custom Produk
                </a>
            </div>

            <!-- Form untuk Search -->
            <div class="col-12 col-md">
                <form action="{{ route('custom.index') }}" method="GET" class="row g-2 align-items-center">
                    <!-- Input Search -->
                    <div class="col-12 col-md-6">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari model/nama customer..." value="{{ request('search') }}">
                    </div>
                    <!-- Tombol Cari -->
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-search me-1"></i>Cari
                        </button>
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

        <!-- Tabel Custom Produk -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 15%;">Model</th>
                                <th style="width: 15%;">Customer</th>
                                <th style="width: 10%;">Jumlah</th>
                                <th style="width: 15%;">Harga Estimasi</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 15%;">Estimasi Selesai</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customs as $index => $custom)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $custom->model }}</td>
                                    <td>{{ $custom->customer->nama }}</td>
                                    <td>{{ $custom->jumlah }}</td>
                                    <td>Rp {{ number_format($custom->harga_estimasi, 0, ',', '.') }}</td>
                                    <td>{{ $custom->status }}</td>
                                    <td>{{ \Carbon\Carbon::parse($custom->estimasi_selesai)->format('d M Y') }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('custom.show', $custom->id) }}" class="btn btn-success btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data custom produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row">
                    <div class="text-muted small">
                        Menampilkan {{ $customs->firstItem() }} - {{ $customs->lastItem() }} dari total {{ $customs->total() }} custom produk
                    </div>
                    <div>
                        {{ $customs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection