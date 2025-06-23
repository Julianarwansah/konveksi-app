@extends('layouts.app')

@section('title', 'Detail Pengeluaran')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">Detail Pengeluaran</h4>
            <div>
                <a href="{{ route('pengeluaran.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
                <a href="{{ route('pengeluaran.edit', $pengeluaran->id) }}" class="btn btn-primary ms-2">
                    <i class="bx bx-edit me-1"></i> Edit
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">ID Pengeluaran</label>
                            <p class="fs-5">{{ $pengeluaran->id }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Kategori Pengeluaran</label>
                            <p class="fs-5">{{ $pengeluaran->kategori }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Jumlah</label>
                            <p class="fs-5">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tanggal</label>
                            <<p class="fs-5">{{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d M Y') }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Ditambahkan Oleh</label>
                            <p class="fs-5">{{ $pengeluaran->user->name }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Keterangan</label>
                            <p class="fs-5">{{ $pengeluaran->keterangan ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="mt-4 pt-4 border-top">
                    <h5 class="fw-bold mb-3">Informasi Tambahan</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Dibuat Pada</label>
                            <p>{{ $pengeluaran->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Diperbarui Pada</label>
                            <p>{{ $pengeluaran->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection