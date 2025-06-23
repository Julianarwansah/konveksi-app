@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail Produk</h4>

        <div class="card">
            <div class="card-body">
                <!-- Informasi Dasar Produk -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nama Produk</label>
                            <p>{{ $produk->nama }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Kategori</label>
                            <p>{{ $produk->kategori }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Status</label>
                            <p>
                                <span class="badge {{ $produk->is_custom ? 'bg-primary' : 'bg-success' }}">
                                    {{ $produk->is_custom ? 'Custom' : 'Standard' }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Harga</label>
                            <p>Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Total Stok</label>
                            <p>{{ $produk->total_stok }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <p>{{ $produk->deskripsi ?? '-' }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tanggal Dibuat</label>
                            <p>{{ $produk->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Terakhir Diupdate</label>
                            <p>{{ $produk->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Gambar Utama Produk -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Gambar Utama</label>
                    <div class="text-center">
                        @if($produk->img)
                            <img src="{{ asset('storage/' . $produk->img) }}" alt="Gambar Utama" class="img-thumbnail" style="max-height: 300px;">
                        @else
                            <span class="text-muted">Tidak ada gambar utama</span>
                        @endif
                    </div>
                </div>

                <!-- Gambar Detail Produk -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Gambar Detail</label>
                    <div class="row">
                        @forelse($produk->gambarDetails as $gambar)
                            <div class="col-md-3 mb-3">
                                <img src="{{ asset('storage/' . $gambar->gambar) }}" alt="Gambar Detail" class="img-thumbnail w-100" style="height: 200px; object-fit: cover;">
                            </div>
                        @empty
                            <div class="col-12">
                                <span class="text-muted">Tidak ada gambar detail</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Bahan yang Digunakan -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Bahan yang Digunakan</label>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Bahan</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produk->produkBahan as $bahan)
                                    <tr>
                                        <td>{{ $bahan->bahan->nama }}</td>
                                        <td>{{ $bahan->jumlah }}</td>
                                        <td>Rp {{ number_format($bahan->harga, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($bahan->jumlah * $bahan->harga, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data bahan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Variasi Warna dan Ukuran -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Variasi Warna dan Ukuran</label>
                    @foreach($produk->warna as $warna)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Warna: {{ $warna->warna }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Ukuran</th>
                                                <th>Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($warna->ukuran as $ukuran)
                                                <tr>
                                                    <td>{{ $ukuran->ukuran }}</td>
                                                    <td>{{ $ukuran->stok }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($produk->warna->isEmpty())
                        <div class="alert alert-info">Tidak ada variasi warna dan ukuran</div>
                    @endif
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('produk.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <div>
                        <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('produk.destroy', $produk->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection