@extends('layouts.app')

@section('title', 'Manajemen Pakaian Custom')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Pakaian Custom</h4>

        <!-- Header dengan Tombol Tambah dan Form Search -->
        <div class="row g-3 align-items-center mb-3">

            <!-- Form untuk Search -->
            <div class="col-12 col-md">
                <form action="{{ route('pakaian_custom.index') }}" method="GET" class="row g-2 align-items-center">
                    <!-- Input Search -->
                    <div class="col-12 col-md-6">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari pakaian custom..." value="{{ request('search') }}">
                    </div>

                    <!-- Dropdown Status Produksi -->
                    <div class="col-12 col-md-4">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- Semua Status --</option>
                            @foreach($statusList as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
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

        <!-- Tabel Pakaian Custom -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 20%;">Nama Customer</th>
                                <th style="width: 15%;">Pesanan ID</th>
                                <th style="width: 15%;">Bahan Baku</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 20%;">Tanggal Dibuat</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pakaianCustoms as $pakaianCustom)
                                <tr>
                                    <td>{{ $pakaianCustom->id }}</td>
                                    <td>{{ $pakaianCustom->customer->nama }}</td>
                                    <td>{{ $pakaianCustom->pesanan->id }}</td>
                                    <td>
                                        @foreach($pakaianCustom->pakaianCustomDetail as $detail)
                                            {{ $detail->bahanBaku->nama }},
                                        @endforeach
                                    </td>
                                    <td>{{ $pakaianCustom->status_produksi }}</td>
                                    <td>{{ $pakaianCustom->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('pakaian_custom.show', $pakaianCustom->id) }}" class="btn btn-success btn-sm">Detail</a>
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
                        Menampilkan {{ $pakaianCustoms->firstItem() }} - {{ $pakaianCustoms->lastItem() }} dari total {{ $pakaianCustoms->total() }} data
                    </div>
                    <div>
                        {{ $pakaianCustoms->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection