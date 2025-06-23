@extends('layouts.app')

@section('title', 'Edit Bahan Baku')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Bahan Baku</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('bahan.update', $bahan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Bahan Baku <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                       id="nama" name="nama" value="{{ old('nama', $bahan->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                                <select class="form-select @error('satuan') is-invalid @enderror" id="satuan" name="satuan" required>
                                    <option value="">Pilih Satuan</option>
                                    <option value="kg" {{ old('satuan', $bahan->satuan) == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                    <option value="Buah" {{ old('satuan', $bahan->satuan) == 'Buah' ? 'selected' : '' }}>Buah (b)</option>
                                    <option value="liter" {{ old('satuan', $bahan->satuan) == 'liter' ? 'selected' : '' }}>Liter</option>
                                    <option value="Meter" {{ old('satuan', $bahan->satuan) == 'Meter' ? 'selected' : '' }}>Meter (m)</option>
                                    <option value="Roll" {{ old('satuan', $bahan->satuan) == 'Roll' ? 'selected' : '' }}>Roll (rl)</option>
                                    <option value="pcs" {{ old('satuan', $bahan->satuan) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                </select>
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('stok') is-invalid @enderror" 
                                       id="stok" name="stok" value="{{ old('stok', $bahan->stok) }}" min="0" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga') is-invalid @enderror" 
                                           id="harga" name="harga" value="{{ old('harga', $bahan->harga) }}" min="0" required>
                                    @error('harga')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="img" class="form-label">Gambar</label>
                                <input type="file" class="form-control @error('img') is-invalid @enderror" 
                                       id="img" name="img" accept="image/*">
                                @error('img')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                @if($bahan->img)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $bahan->img) }}" alt="{{ $bahan->nama }}" 
                                             class="img-thumbnail" width="100">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="remove_img" name="remove_img">
                                            <label class="form-check-label" for="remove_img">
                                                Hapus gambar saat disimpan
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <small class="text-muted">Format: jpeg, png, jpg, gif (max: 2MB)</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('bahan.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection