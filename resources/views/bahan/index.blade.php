@extends('layouts.app')

@section('title', 'Manajemen Bahan Baku')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Bahan Baku</h4>

        <!-- Header dengan Tombol Tambah dan Form Search -->
        <div class="row g-3 align-items-center mb-3">
            <!-- Tombol Tambah Bahan Baku -->
            @if(auth()->user()->role->nama == 'Manager')
                <div class="col-12 col-md-auto">
                    <a href="{{ route('bahan.create') }}" class="btn btn-primary w-100 w-md-auto">Tambah Bahan Baku</a>
                </div>
            @endif

            <!-- Form untuk Search -->
            <div class="col-12 col-md">
                <form action="{{ route('bahan.index') }}" method="GET" class="row g-2 align-items-center">
                    <!-- Input Search -->
                    <div class="col-12 col-md-6">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari bahan baku..." value="{{ request('search') }}">
                    </div>

                    <div class="col-12 col-md-4">
                        <select name="satuan" class="form-select form-select-sm">
                            <option value="">Semua Satuan</option>
                            @foreach($satuans as $satuan)
                                <option value="{{ $satuan }}" {{ request('satuan') == $satuan ? 'selected' : '' }}>
                                    {{ $satuan }}
                                </option>
                            @endforeach
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

        <!-- Tabel Bahan Baku -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 20%;">Nama</th>
                                <th style="width: 15%;">Satuan</th>
                                <th style="width: 15%;">Stok</th>
                                <th style="width: 15%;">Harga</th>
                                <th style="width: 15%;">Gambar</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bahans as $bahan)
                                <tr>
                                    <td>{{ $bahan->id }}</td>
                                    <td>{{ $bahan->nama }}</td>
                                    <td>{{ $bahan->satuan }}</td>
                                    <td>{{ $bahan->stok }}</td>
                                    <td>Rp {{ number_format($bahan->harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($bahan->img)
                                            <img src="{{ asset('storage/' . $bahan->img) }}" alt="{{ $bahan->nama }}" class="img-thumbnail" width="100">
                                        @else
                                            <span class="text-muted">Tidak ada gambar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if(auth()->user()->role->nama == 'Manager')
                                                <a href="{{ route('bahan.edit', $bahan->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            @endif

                                            <a href="{{ route('bahan.show', $bahan->id) }}" class="btn btn-success btn-sm">Detail</a>

                                            @if(auth()->user()->role->nama == 'Manager')
                                                <form action="{{ route('bahan.destroy', $bahan->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus bahan baku ini?')">Hapus</button>
                                                </form>
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
                        Menampilkan {{ $bahans->firstItem() }} - {{ $bahans->lastItem() }} dari total {{ $bahans->total() }} bahan baku
                    </div>
                    <div>
                        {{ $bahans->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection