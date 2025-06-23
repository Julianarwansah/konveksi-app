@extends('layouts.app')

@section('title', 'Manajemen Customer')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Customer</h4>

        <div class="row g-3 align-items-center mb-3">
            @if(auth()->user()->role->nama == 'Admin Ecommerce')
            <div class="col-12 col-md-auto">
                <a href="{{ route('customers.create') }}" class="btn btn-primary w-100 w-md-auto">Tambah Customer</a>
            </div>
            @endif

            <div class="col-12 col-md">
                <form action="{{ route('customers.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-6">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari customer Berdasarkan Nama dan email" value="{{ request('search') }}">
                    </div>

                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 20%;">Nama</th>
                                <th style="width: 20%;">Email</th>
                                <th style="width: 20%;">Alamat</th>
                                <th style="width: 15%;">No. Telp</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->nama }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->alamat }}</td>
                                    <td>{{ $customer->no_telp }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            {{-- Tombol Edit dan Hapus - Hanya untuk Admin Ecommerce --}}
                                            @if(auth()->user()->role->nama == 'Admin Ecommerce')
                                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus customer ini?')">Hapus</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-success btn-sm">Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row">
                    <div class="text-muted small">
                        Menampilkan {{ $customers->firstItem() }} - {{ $customers->lastItem() }} dari total {{ $customers->total() }} customer
                    </div>
                    <div>
                        {{ $customers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
