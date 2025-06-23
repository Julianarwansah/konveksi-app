@extends('layouts.app')

@section('title', 'Edit Pembayaran')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Pembayaran</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('pembayaran.update', $pembayaran->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3" >
                                <label for="pesanan_id" class="form-label">Pesanan <span class="text-danger">*</span></label>
                                <select class="form-select" id="pesanan_id" name="pesanan_id_display" disabled>
                                    @foreach($pesanan as $item)
                                        <option value="{{ $item->id }}" {{ old('pesanan_id', $pembayaran->pesanan_id) == $item->id ? 'selected' : '' }}>
                                            Pesanan #{{ $item->id }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="pesanan_id" value="{{ old('pesanan_id', $pembayaran->pesanan_id) }}">
                                @error('pesanan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input readonly type="number" class="form-control @error('jumlah') is-invalid @enderror" 
                                           id="jumlah" name="jumlah" value="{{ old('jumlah', $pembayaran->jumlah) }}" min="0" required>
                                    @error('jumlah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3" >
                                <label for="metode" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <input readonly type="text" class="form-control @error('metode') is-invalid @enderror" 
                                       id="metode" name="metode" value="{{ old('metode', $pembayaran->metode) }}" required>
                                @error('metode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="Menunggu Konfirmasi" {{ old('status', $pembayaran->status) == 'Menunggu Konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                    <option value="Berhasil" {{ old('status', $pembayaran->status) == 'Berhasil' ? 'selected' : '' }}>Berhasil</option>
                                    <option value="Gagal" {{ old('status', $pembayaran->status) == 'Gagal' ? 'selected' : '' }}>Gagal</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_bayar" class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                                <input readonly type="date" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                                       id="tanggal_bayar" name="tanggal_bayar" value="{{ old('tanggal_bayar', $pembayaran->tanggal_bayar->format('Y-m-d')) }}" required>
                                @error('tanggal_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" hidden>
                                <label for="is_dp" class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_dp" id="is_dp1" value="1" {{ old('is_dp', $pembayaran->is_dp) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_dp1">DP (Uang Muka)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_dp" id="is_dp0" value="0" {{ old('is_dp', $pembayaran->is_dp) == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_dp0">Pelunasan</label>
                                </div>
                                @error('is_dp')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" hidden>
                            <div class="mb-3">
                                <label for="bukti_bayar" class="form-label">Bukti Bayar</label>
                                <input type="file" class="form-control @error('bukti_bayar') is-invalid @enderror" 
                                       id="bukti_bayar" name="bukti_bayar" accept="image/*,.pdf">
                                @error('bukti_bayar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($pembayaran->bukti_bayar)
                                    <div class="mt-2">
                                        <small>File saat ini: </small>
                                        <a href="{{ Storage::url($pembayaran->bukti_bayar) }}" target="_blank">Lihat Bukti Bayar</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6" hidden>
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                          id="catatan" name="catatan" rows="2">{{ old('catatan', $pembayaran->catatan) }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection