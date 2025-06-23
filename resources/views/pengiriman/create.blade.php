@extends('layouts.app')

@section('title', 'Tambah Pengiriman')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Tambah Pengiriman</h4>

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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('pengiriman.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pesanan_id" class="form-label">Pesanan ID <span class="text-danger">*</span></label>
                                <select class="form-select @error('pesanan_id') is-invalid @enderror" id="pesanan_id" name="pesanan_id" required onchange="updateAlamat()">
                                    <option value="">Pilih Pesanan</option>
                                    @foreach($pesanans as $pesanan)
                                        <option value="{{ $pesanan->id }}" data-customer-id="{{ $pesanan->customer_id }}" {{ old('pesanan_id') == $pesanan->id ? 'selected' : '' }}>
                                            {{ $pesanan->id }} - {{ $pesanan->customer->nama ?? 'Tanpa Customer' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pesanan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="kurir" class="form-label">Kurir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('kurir') is-invalid @enderror" id="kurir" name="kurir" value="{{ old('kurir') }}" required>
                                @error('kurir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="resi" class="form-label">Nomor Resi</label>
                                <input type="text" class="form-control @error('resi') is-invalid @enderror" id="resi" name="resi" value="{{ old('resi') }}">
                                @error('resi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="foto_resi" class="form-label">Foto Resi</label>
                                <input type="file" class="form-control @error('foto_resi') is-invalid @enderror" id="foto_resi" name="foto_resi" accept="image/*">
                                @error('foto_resi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: JPEG, PNG, JPG, GIF (Maksimal 2MB)</small>
                            </div>

                            <div class="mb-3">
                                <label for="biaya" class="form-label">Biaya Pengiriman <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('biaya') is-invalid @enderror" id="biaya" name="biaya" value="{{ old('biaya') }}" required>
                                    @error('biaya')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="Dalam Pengiriman" {{ old('status') == 'Dalam Pengiriman' ? 'selected' : '' }}>Dalam Pengiriman</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('pengiriman.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Pengiriman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Di JavaScript
const alamatCustomer = @json($alamatCustomer);

function updateAlamat() {
    const pesananId = document.getElementById('pesanan_id').value;
    if (alamatCustomer[pesananId]) {
        document.getElementById('alamat').value = alamatCustomer[pesananId];
    }
}
    </script>
@endsection