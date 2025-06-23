@extends('layouts.app')

@section('title', 'Manajemen Supplier')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Supplier</h4>

        <!-- Header dengan Tombol Tambah dan Form Search -->
        <div class="row g-3 align-items-center mb-3">
            <!-- Tombol Tambah Supplier -->
            @if(auth()->user()->role->nama == 'Admin Purcashing')
                <div class="col-12 col-md-auto">
                    <a href="{{ route('supplier.create') }}" class="btn btn-primary w-100 w-md-auto">Tambah Supplier</a>
                </div>
            @endif

            <!-- Form untuk Search -->
            <div class="col-12 col-md">
                <form action="{{ route('supplier.index') }}" method="GET" class="row g-2 align-items-center">
                    <!-- Input Search -->
                    <div class="col-12 col-md-6">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari supplier..." value="{{ request('search') }}">
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

        <!-- Tabel Supplier -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-bordered table-sm table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 25%;">Nama <i class="bi bi-arrow-down-up"></i></th> <!-- Icon untuk sorting -->
                                <th style="width: 25%;">Kontak</th>
                                <th style="width: 25%;">Alamat</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->id }}</td>
                                    <td>{{ $supplier->nama }}</td>
                                    <td>{{ $supplier->kontak }}</td>
                                    <td>{{ $supplier->alamat }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if(auth()->user()->role->nama == 'Admin Purcashing')
                                                <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            @endif

                                            <a href="{{ route('supplier.show', $supplier->id) }}" class="btn btn-success btn-sm">Detail</a>

                                            @if(auth()->user()->role->nama == 'Admin Purcashing')
                                                <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus supplier ini?')">Hapus</button>
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
                        Menampilkan {{ $suppliers->firstItem() }} - {{ $suppliers->lastItem() }} dari total {{ $suppliers->total() }} supplier
                    </div>
                    <div>
                        {{ $suppliers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
