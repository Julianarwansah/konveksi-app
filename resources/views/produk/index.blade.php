@extends('layouts.app')

@section('title', 'Manajemen Produk')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Produk</h4>

        <!-- Header dengan Tombol Tambah dan Form Search -->
        <div class="row g-3 align-items-center mb-3">
            <!-- Tombol Tambah Produk - Hanya untuk Manager -->
            @if(auth()->user()->role->nama == 'Manager')
            <div class="col-12 col-md-auto">
                <a href="{{ route('produk.create') }}" class="btn btn-primary w-100 w-md-auto">
                    <i class="fas fa-plus me-2"></i>Tambah Produk
                </a>
            </div>
            @endif

            <!-- Form untuk Search -->
            <div class="col-12 col-md">
                <form action="{{ route('produk.index') }}" method="GET" class="row g-2 align-items-center">
                    <!-- Input Search -->
                    <div class="col-12 col-md-6">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari produk..." value="{{ request('search') }}">
                    </div>

                    <!-- Dropdown Kategori -->
                    <div class="col-12 col-md-4">
                        <select name="kategori" class="form-select form-select-sm">
                            <option value="">-- Semua Kategori --</option>
                            @foreach($kategoriList as $kategori)
                                <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                    {{ $kategori }}
                                </option>
                            @endforeach
                        </select>
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

        <!-- Tabel Produk -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 20%;">Nama</th>
                                <th style="width: 15%;">Kategori</th>
                                <th style="width: 15%;">Harga</th>
                                <th style="width: 10%;">Stok</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($produks as $index => $produk)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $produk->nama }}</td>
                                    <td>{{ $produk->kategori }}</td>
                                    <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                                    <td>{{ $produk->total_stok }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <!-- Tombol Detail - Bisa diakses semua role -->
                                            <a href="{{ route('produk.show', $produk->id) }}" class="btn btn-success btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- Tombol Edit dan Hapus - Hanya untuk Manager -->
                                            @if(auth()->user()->role->nama == 'Manager')
                                                <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('produk.destroy', $produk->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Hapus produk ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row">
                    <div class="text-muted small">
                        Menampilkan {{ $produks->firstItem() }} - {{ $produks->lastItem() }} dari total {{ $produks->total() }} produk
                    </div>
                    <div>
                        {{ $produks->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection