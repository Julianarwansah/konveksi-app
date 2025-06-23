@extends('layouts.app')
@php
use App\Models\Antrian;
@endphp

@section('title', 'Detail Antrian Produksi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">Detail Antrian Produksi</h4>
        <div>
            <a href="{{ route('antrian.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label fw-bold">ID Antrian</label>
                        <p class="fs-5">{{ $antrian->id }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Tanggal Produksi</label>
                        <p class="fs-5">{{ \Carbon\Carbon::parse($antrian->tanggal)->format('d M Y') }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Status Produksi</label>
                        <p class="fs-5">
                            <span class="badge bg-{{ 
                                $antrian->status == Antrian::STATUS_DALAM_ANTRIAN_PRODUKSI ? 'warning' : 
                                ($antrian->status == Antrian::STATUS_DALAM_PRODUKSI ? 'info' : 
                                ($antrian->status == Antrian::STATUS_SELESAI_PRODUKSI ? 'success' : 'secondary')) 
                            }}">
                                {{ $statuses[$antrian->status] ?? 'Tidak Diketahui' }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Jumlah Produksi</label>
                        <p class="fs-5">{{ number_format($antrian->jumlah, 0) }} pcs</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">ID Custom</label>
                        <p class="fs-5">{{ $antrian->custom_id }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Status Pembayaran</label>
                        <p class="fs-5">
                            <span class="badge bg-{{ 
                                $antrian->custom->pesanan->status_pembayaran == 'Lunas' ? 'success' : 
                                ($antrian->custom->pesanan->status_pembayaran == 'DP' ? 'warning' : 'secondary')
                            }}">
                                {{ $antrian->custom->pesanan->status_pembayaran }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informasi Customer -->
            <div class="mt-4 pt-4 border-top">
                <h5 class="fw-bold mb-3">Informasi Customer</h5>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Customer</label>
                        <p>{{ $antrian->custom->customer->nama }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kontak</label>
                        <p>{{ $antrian->custom->customer->telepon }}</p>
                    </div>
                </div>
            </div>

            <!-- Detail Produk -->
            <div class="mt-4 pt-4 border-top">
                <h5 class="fw-bold mb-3">Detail Produk</h5>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Template</label>
                        <p>{{ $antrian->custom->template->model ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Warna</label>
                        <p>{{ $antrian->custom->warna ?? '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Model</label>
                        <p>{{ $antrian->custom->model ?? '-' }}</p>
                    </div>
                </div>
                @if($antrian->custom->keterangan)
                <div class="row">
                    <div class="col-12">
                        <label class="form-label fw-bold">Keterangan Tambahan</label>
                        <p>{{ $antrian->custom->keterangan }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 pt-4 border-top">
                <div class="d-flex justify-content-between">
                    @if($antrian->status == Antrian::STATUS_DALAM_ANTRIAN_PRODUKSI)
                        <form action="{{ route('antrian.startProduction', $antrian->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-play-circle me-1"></i> Mulai Produksi
                            </button>
                        </form>
                    @elseif($antrian->status == Antrian::STATUS_DALAM_PRODUKSI)
                        <form action="{{ route('antrian.completeProduction', $antrian->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-check-circle me-1"></i> Selesaikan Produksi
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('antrian.destroy', $antrian->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus antrian ini?')">
                            <i class="bx bx-trash me-1"></i> Hapus Antrian
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    .timeline-point {
        position: absolute;
        left: -30px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #e9ecef;
        border: 3px solid #dee2e6;
    }
    .timeline-item.active .timeline-point {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .timeline-content {
        padding-left: 10px;
    }
    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: -22px;
        top: 20px;
        bottom: 0;
        width: 2px;
        background-color: #dee2e6;
    }
</style>
@endsection