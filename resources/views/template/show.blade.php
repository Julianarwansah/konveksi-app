@extends('layouts.app')

@section('title', 'Detail Template')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail Template</h4>

        <!-- Tombol Kembali -->
        <div class="text-end mb-3">
            <a href="{{ route('template.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        <!-- Card Informasi Template -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Informasi Template</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Model</label>
                            <p>{{ $template->model }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <p>{{ $template->kategori->nama ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <p>{{ $template->deskripsi ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Harga Estimasi</label>
                            <p>{{ $template->harga_estimasi ? 'Rp ' . number_format($template->harga_estimasi, 2) : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Detail Bahan -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Detail Bahan</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Bahan</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($template->details as $detail)
                                <tr>
                                    <td>{{ $detail->bahan->nama }}</td>
                                    <td>{{ $detail->jumlah }}</td>
                                    <td>Rp {{ number_format($detail->harga_satuan, 2) }}</td>
                                    <td>Rp {{ number_format($detail->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="fw-bold">Rp {{ number_format($template->details->sum('subtotal'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card Warna -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Warna Tersedia</h5>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($template->warna as $warna)
                        <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            {{ $warna->warna }}
                        </span>
                    @endforeach
                    @if($template->warna->isEmpty())
                        <p class="text-muted">Tidak ada warna yang tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card Gambar -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Gambar Template</h5>
                @if($template->gambar->isNotEmpty())
                    <div class="row g-3">
                        @foreach($template->gambar as $gambar)
                            <div class="col-md-4 col-lg-3">
                                <div class="card h-100">
                                    <img src="{{ asset('storage/' . $gambar->gambar) }}" 
                                         class="card-img-top img-thumbnail" 
                                         alt="Gambar Template"
                                         style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <p class="card-text">Gambar {{ $loop->iteration }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Tidak ada gambar yang tersedia</p>
                @endif
            </div>
        </div>
    </div>
@endsection