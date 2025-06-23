@extends('layouts.app')

@section('title', 'Detail Custom Produk')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail Custom Produk</h4>

        <div class="card">
            <div class="card-body">
                <!-- Informasi Dasar Custom Produk -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Customer</label>
                            <p>{{ $custom->customer->nama }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Model</label>
                            <p>{{ $custom->model }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Ukuran</label>
                            <p>{{ $custom->ukuran }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Warna</label>
                            <p>{{ $custom->warna }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Jumlah</label>
                            <p>{{ $custom->jumlah }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Template</label>
                            <p>{{ $custom->template->model ?? '-' }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Status</label>
                            <p>
                                <span class="badge bg-{{ $custom->status == 'selesai' ? 'success' : ($custom->status == 'proses' ? 'warning' : 'primary') }}">
                                    {{ ucfirst($custom->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Harga Estimasi</label>
                            <p>Rp {{ number_format($custom->harga_estimasi, 0, ',', '.') }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Estimasi Selesai</label>
                            <p>{{ \Carbon\Carbon::parse($custom->estimasi_selesai)->format('d M Y') }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tanggal Mulai</label>
                            <p>{{ $custom->tanggal_mulai ? \Carbon\Carbon::parse($custom->tanggal_mulai)->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Gambar Desain -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Gambar Desain</label>
                    <div class="text-center">
                        @if($custom->img)
                            <img src="{{ Storage::url($custom->img) }}" alt="Gambar Desain" class="img-thumbnail" style="max-height: 300px;">
                        @else
                            <span class="text-muted">Tidak ada gambar desain</span>
                        @endif
                    </div>
                </div>

                <!-- Catatan -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Catatan</label>
                    <div class="border rounded p-3">
                        {{ $custom->catatan ?? 'Tidak ada catatan' }}
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('custom.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <div>
                        <a hidden href="{{ route('custom.edit', $custom->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        @if($custom->status == 'selesai' && !$custom->pesanan_id)
                            <a hidden href="{{ route('custom.createPesanan', $custom->id) }}" class="btn btn-success me-2">
                                <i class="fas fa-shopping-cart me-1"></i> Buat Pesanan
                            </a>
                        @endif
                        <form action="{{ route('custom.destroy', $custom->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button hidden type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus custom produk ini?')">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection