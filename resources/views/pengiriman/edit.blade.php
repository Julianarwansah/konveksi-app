@extends('layouts.app')

@section('title', 'Edit Pengiriman')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Pengiriman</h4>

        <!-- Notifikasi Error -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('pengiriman.update', $pengiriman->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pesanan_id" class="form-label">Pesanan ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('pesanan_id') is-invalid @enderror" id="pesanan_id" 
                                    name="pesanan_id" value="{{ old('pesanan_id', $pengiriman->pesanan_id) }}" readonly>
                                @error('pesanan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" 
                                    name="alamat" rows="3" required>{{ old('alamat', $pengiriman->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="kurir" class="form-label">Kurir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kurir') is-invalid @enderror" id="kurir" 
                                    name="kurir" value="{{ old('kurir', $pengiriman->kurir) }}" required>
                                @error('kurir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="resi" class="form-label">Nomor Resi</label>
                                <input type="text" class="form-control @error('resi') is-invalid @enderror" id="resi" 
                                    name="resi" value="{{ old('resi', $pengiriman->resi) }}">
                                @error('resi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="foto_resi" class="form-label">Foto Resi</label>
                                <input type="file" class="form-control @error('foto_resi') is-invalid @enderror" 
                                    id="foto_resi" name="foto_resi" accept="image/*">
                                @error('foto_resi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($pengiriman->foto_resi)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $pengiriman->foto_resi) }}" alt="Foto Resi" class="img-thumbnail" style="max-height: 150px;">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="hapus_foto" name="hapus_foto">
                                            <label class="form-check-label" for="hapus_foto">
                                                Hapus foto saat disimpan
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="biaya" class="form-label">Biaya Pengiriman <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('biaya') is-invalid @enderror" 
                                        id="biaya" name="biaya" value="{{ old('biaya', $pengiriman->biaya) }}" required>
                                    @error('biaya')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="Dalam Pengiriman" {{ old('status', $pengiriman->status) == 'Dalam Pengiriman' ? 'selected' : '' }}>
                                        Dalam Pengiriman
                                    </option>
                                    <option value="Selesai Pengiriman" {{ old('status', $pengiriman->status) == 'Selesai Pengiriman' ? 'selected' : '' }}>
                                        Selesai Pengiriman
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('pengiriman.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection