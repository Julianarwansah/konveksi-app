@extends('layouts.app')

@section('title', 'Edit Biaya')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Biaya</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('biaya.update', $biaya->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Biaya</label>
                        <input
                            type="text"
                            class="form-control @error('nama') is-invalid @enderror"
                            id="nama"
                            name="nama"
                            value="{{ old('nama', $biaya->nama) }}"
                            required
                        >
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input
                            type="number"
                            class="form-control @error('harga') is-invalid @enderror"
                            id="harga"
                            name="harga"
                            value="{{ old('harga', $biaya->harga) }}"
                            min="0"
                            required
                        >
                        @error('harga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi (opsional)</label>
                        <textarea
                            class="form-control @error('deskripsi') is-invalid @enderror"
                            id="deskripsi"
                            name="deskripsi"
                            rows="3"
                        >{{ old('deskripsi', $biaya->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('biaya.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
