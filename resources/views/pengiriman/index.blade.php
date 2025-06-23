@extends('layouts.app')

@section('title', 'Manajemen Pengiriman')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Pengiriman</h4>

        <div class="row g-3 align-items-center mb-3">
            @if(auth()->user()->role->nama == 'Admin Ecommerce')
            <div class="col-12 col-md-auto">
                <a href="{{ route('pengiriman.create') }}" class="btn btn-primary w-100 w-md-auto">Tambah Pengiriman</a>
            </div>
            @endif

            <div class="col-12 col-md">
                <form action="{{ route('pengiriman.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-6">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari pengiriman..." value="{{ request('search') }}">
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
                                <th style="width: 20%;">Pesanan</th>
                                <th style="width: 15%;">Kurir</th>
                                <th style="width: 15%;">Nomor Resi</th>
                                <th style="width: 15%;">Biaya</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengirimans as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->pesanan->id ?? '-' }}</td>
                                    <td>{{ $item->kurir }}</td>
                                    <td>{{ $item->resi ?? '-' }}</td>
                                    <td>Rp {{ number_format($item->biaya, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->status == 'terkirim' ? 'success' : ($item->status == 'diproses' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            {{-- Tombol Edit dan Hapus - Hanya untuk Admin Ecommerce --}}
                                            @if(auth()->user()->role->nama == 'Admin Ecommerce')
                                                <a href="{{ route('pengiriman.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="{{ route('pengiriman.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengiriman ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('pengiriman.show', $item->id) }}" class="btn btn-success btn-sm">Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($pengirimans->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row">
                    <div class="text-muted small">
                        Menampilkan {{ $pengirimans->firstItem() }} - {{ $pengirimans->lastItem() }} dari total {{ $pengirimans->total() }} pengiriman
                    </div>
                    <div>
                        {{ $pengirimans->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
