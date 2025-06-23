@extends('layouts.app')

@section('title', 'Detail Pengiriman')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail Pengiriman</h4>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">ID Pengiriman</label>
                            <p>{{ $pengiriman->id }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pesanan ID</label>
                            <p>{{ $pengiriman->pesanan_id }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Alamat Pengiriman</label>
                            <p>{{ $pengiriman->alamat }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Kurir</label>
                            <p>{{ $pengiriman->kurir }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Tanggal Dibuat</label>
                            <p>{{ $pengiriman->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nomor Resi</label>
                            <p>{{ $pengiriman->resi ?? '-' }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Foto Resi</label>
                            @if($pengiriman->foto_resi)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $pengiriman->foto_resi) }}" 
                                         alt="Foto Resi" 
                                         class="img-thumbnail" 
                                         style="max-height: 200px;">
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $pengiriman->foto_resi) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-primary">
                                            Lihat Full Size
                                        </a>
                                    </div>
                                </div>
                            @else
                                <p>-</p>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Biaya Pengiriman</label>
                            <p>Rp {{ number_format($pengiriman->biaya, 0, ',', '.') }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Status</label>
                            <p>
                                <span class="badge bg-{{ $pengiriman->status == 'terkirim' ? 'success' : ($pengiriman->status == 'diproses' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($pengiriman->status) }}
                                </span>
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Terakhir Diperbarui</label>
                            <p>{{ $pengiriman->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('pengiriman.index') }}" class="btn btn-secondary">Kembali</a>
                    <div>
                        <a href="{{ route('pengiriman.edit', $pengiriman->id) }}" class="btn btn-warning me-2">Edit</a>
                        <form action="{{ route('pengiriman.destroy', $pengiriman->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus data pengiriman ini?')">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection