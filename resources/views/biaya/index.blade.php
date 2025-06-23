@extends('layouts.app')

@section('title', 'Manajemen Biaya')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Biaya</h4>

        <!-- Header dengan Tombol Tambah dan Form Search -->
        <div class="row g-3 align-items-center mb-3">
            <!-- Tombol Tambah Biaya -->
            <div class="col-12 col-md-auto">
                <a href="{{ route('biaya.create') }}" class="btn btn-primary w-100 w-md-auto">
                    Tambah Biaya
                </a>
            </div>

            <!-- Form untuk Search -->
            <div class="col-12 col-md">
                <form action="{{ route('biaya.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-6">
                        <input
                            type="text"
                            name="search"
                            class="form-control form-control-sm"
                            placeholder="Cari nama atau deskripsi..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notifikasi Sukses / Error -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Tabel Biaya -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 30%;">Nama</th>
                                <th style="width: 20%;">Harga</th>
                                <th style="width: 20%;">Dibuat Pada</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($biayas as $biaya)
                                <tr>
                                    <td>{{ $biaya->id }}</td>
                                    <td>{{ $biaya->nama }}</td>
                                    <td>Rp {{ number_format($biaya->harga, 0, ',', '.') }}</td>
                                    <td>{{ $biaya->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a
                                                href="{{ route('biaya.edit', $biaya->id) }}"
                                                class="btn btn-warning btn-sm"
                                            >Edit</a>
                                            <a
                                                href="{{ route('biaya.show', $biaya->id) }}"
                                                class="btn btn-success btn-sm"
                                            >Detail</a>
                                            <form
                                                action="{{ route('biaya.destroy', $biaya->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Hapus biaya ini?')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data biaya.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row">
                    <div class="text-muted small">
                        Menampilkan {{ $biayas->firstItem() }} – {{ $biayas->lastItem() }}
                        dari total {{ $biayas->total() }} biaya
                    </div>
                    <div>
                        {{ $biayas->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
