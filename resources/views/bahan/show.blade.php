@extends('layouts.app')

@section('title', 'Detail Bahan Baku')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">Detail Bahan Baku</h4>
            <div>
                <a href="{{ route('bahan.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
                @if(auth()->user()->role->nama == 'Admin Gudang')
                    <a href="{{ route('bahan.edit', $bahan->id) }}" class="btn btn-primary ms-2">
                        <i class="bx bx-edit me-1"></i> Edit
                    </a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">ID Bahan Baku</label>
                            <p class="fs-5">{{ $bahan->id }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nama Bahan Baku</label>
                            <p class="fs-5">{{ $bahan->nama }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Satuan</label>
                            <p class="fs-5">{{ $bahan->satuan }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Stok</label>
                            <p class="fs-5">
                                {{ number_format($bahan->stok, 2) }} 
                                <span class="badge bg-{{ $bahan->stok <= $bahan->min_stok ? 'danger' : 'success' }} ms-2">
                                    {{ $bahan->stok <= $bahan->min_stok ? 'Stok Rendah' : 'Stok Aman' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Stok Minimum</label>
                            <p class="fs-5">{{ number_format($bahan->min_stok, 2) }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Harga per Satuan</label>
                            <p class="fs-5">Rp {{ number_format($bahan->harga, 0, ',', '.') }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Gambar</label>
                            <div>
                                @if($bahan->img)
                                    <img src="{{ asset('storage/' . $bahan->img) }}" 
                                         alt="{{ $bahan->nama }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px;">
                                @else
                                    <div class="text-center py-4 bg-light rounded">
                                        <i class="bx bx-image-alt text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2 mb-0">Tidak ada gambar</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mt-4 pt-4 border-top">
                    <h5 class="fw-bold mb-3">Informasi Tambahan</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Dibuat Pada</label>
                            <p>{{ $bahan->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Diperbarui Pada</label>
                            <p>{{ $bahan->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection