@extends('layouts.app')

@section('title', 'Template')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Template</h4>

        <!-- Header dengan Tombol Tambah dan Form Search -->
        <div class="row g-3 align-items-center mb-3">
            <!-- Tombol Tambah Template -->
            @if(auth()->user()->role->nama === 'Manager')
                <div class="col-12 col-md-auto">
                    <a href="{{ route('template.create') }}" class="btn btn-primary w-100 w-md-auto">Tambah Template</a>
                </div>
            @endif

            <!-- Form untuk Search dan Filter -->
            <div class="col-12 col-md">
                <form action="{{ route('template.index') }}" method="GET" class="row g-2 align-items-center">
                    <!-- Input Search -->
                    <div class="col-12 col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari model..." value="{{ request('search') }}">
                    </div>

                    <!-- Filter Kategori -->
                    <div class="col-12 col-md-4">
                        <select name="kategori" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $kategori)
                                <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                    {{ $kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tombol Cari -->
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                    </div>

                    <!-- Tombol Reset -->
                    <div class="col-12 col-md-2">
                        <a href="{{ route('template.index') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
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

        <!-- Tabel Template -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 20%;">Model</th>
                                <th style="width: 15%;">Kategori</th>
                                <th style="width: 30%;">Bahan Baku</th>
                                <th style="width: 15%;">Harga Estimasi</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                                <tr>
                                    <td>{{ $template->id }}</td>
                                    <td>{{ $template->model }}</td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $template->kategori }}</span>
                                    </td>
                                    <td>
                                        <ul class="mb-0">
                                            @foreach($template->details as $detail)
                                                <li>{{ $detail->bahan->nama }} ({{ $detail->jumlah }})</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>Rp{{ number_format($template->harga_estimasi, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('template.show', $template->id) }}" class="btn btn-success btn-sm">Detail</a>
                                            @if(auth()->user()->role->nama === 'Manager')
                                                <a href="{{ route('template.edit', $template->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="{{ route('template.destroy', $template->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus template ini?')">Hapus</button>
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
                        Menampilkan {{ $templates->firstItem() }} - {{ $templates->lastItem() }} dari total {{ $templates->total() }} template
                    </div>
                    <div>
                        {{ $templates->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection