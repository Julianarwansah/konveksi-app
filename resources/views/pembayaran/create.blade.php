@extends('layouts.app')

@section('title', 'Tambah Pembayaran')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Tambah Pembayaran</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pesanan_id" class="form-label">Pesanan <span class="text-danger">*</span></label>
                                <select class="form-select @error('pesanan_id') is-invalid @enderror" 
                                        id="pesanan_id" name="pesanan_id" required>
                                    <option value="">Pilih Pesanan</option>
                                    @foreach($pesanan as $item)
                                        <option value="{{ $item->id }}" {{ old('pesanan_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->kode }} - {{ $item->customer->nama ?? 'Tanpa Customer' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pesanan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('jumlah') is-invalid @enderror" 
                                           id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="0" step="0.01" required>
                                    @error('jumlah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="metode" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-select @error('metode') is-invalid @enderror" 
                                        id="metode" name="metode" required>
                                    <option value="">Pilih Metode</option>
                                    <option value="transfer" {{ old('metode') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="tunai" {{ old('metode') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                    <option value="qris" {{ old('metode') == 'qris' ? 'selected' : '' }}>QRIS</option>
                                    <option value="e-wallet" {{ old('metode') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                                </select>
                                @error('metode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_bayar" class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                                       id="tanggal_bayar" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                                @error('tanggal_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="bukti_bayar" class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('bukti_bayar') is-invalid @enderror" 
                                       id="bukti_bayar" name="bukti_bayar" accept="image/*,.pdf" required>
                                @error('bukti_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: JPG, JPEG, PNG, PDF (Maks 2MB)</small>
                            </div>

                            <div class="mb-3">
                                <label for="is_dp" class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_dp" id="is_dp1" value="1" {{ old('is_dp') == '1' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="is_dp1">
                                        Uang Muka (DP)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_dp" id="is_dp0" value="0" {{ old('is_dp') == '0' ? 'checked' : (old('is_dp') === null ? 'checked' : '') }}>
                                    <label class="form-check-label" for="is_dp0">
                                        Pelunasan
                                    </label>
                                </div>
                                @error('is_dp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                  id="catatan" name="catatan" rows="2">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection