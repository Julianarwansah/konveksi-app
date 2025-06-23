@extends('layouts.app')

@section('title', 'Manajemen Antrian Produksi')

@section('content')

<div class="container-fluid">
    <h4 class="fw-bold py-3 mb-4">Manajemen Antrian Produksi</h4>

    <div class="row g-3 align-items-center mb-3">
        <div class="col-12 col-md-auto mb-3 mb-md-0">
            @if(auth()->user()->role->nama === 'Admin Ecommerce')
                <a href="{{ route('antrian.create') }}" class="btn btn-primary">Tambah Antrian</a>
            @endif
        </div>
        <div class="col-12 col-md">
            <form action="{{ route('antrian.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>

                <div class="col-12 col-md-4">
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>

                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Customer</th>
                            <th>Template</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($antrians as $index => $antrian)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $antrian->tanggal }}</td>
                                <td>{{ $antrian->custom->customer->nama ?? '-' }}</td>
                                <td>{{ $antrian->custom->template->model ?? '-' }}</td>
                                <td>{{ $antrian->jumlah ?? '-' }}</td>
                                <td>{{ $antrian->status }}</td>
                                <td>
                                    <a href="{{ route('antrian.show', $antrian->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                    {{-- Hanya tampilkan tombol edit jika user memiliki role 'Produksi' DAN status belum 'Selesai Produksi' --}}
                                    @if(auth()->check() && auth()->user()->role->nama == 'Produksi' && $antrian->status != 'Selesai Produksi')
                                        <a href="{{ route('antrian.edit', $antrian->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    @endif
                                    <form action="{{ route('antrian.destroy', $antrian->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus antrian ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data antrian</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection