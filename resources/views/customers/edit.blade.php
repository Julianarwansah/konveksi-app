@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Customer</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="{{ $customer->nama }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $customer->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" value="{{ $customer->alamat }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_telp" class="form-label">No. Telp</label>
                        <input type="text" class="form-control" id="no_telp" name="no_telp" value="{{ $customer->no_telp }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="img" class="form-label">Gambar (URL)</label>
                        <input type="text" class="form-control" id="img" name="img" value="{{ $customer->img }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection