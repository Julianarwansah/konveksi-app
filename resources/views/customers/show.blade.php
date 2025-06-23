@extends('layouts.app')

@section('title', 'Detail Customer')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail Customer</h4>

        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-bold">ID Customer</label>
                    <p>{{ $customer->id }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Nama</label>
                    <p>{{ $customer->nama }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Email</label>
                    <p>{{ $customer->email }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Alamat</label>
                    <p>{{ $customer->alamat }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">No. Telp</label>
                    <p>{{ $customer->no_telp }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Gambar</label>
                    <p>{{ $customer->img ?? 'Tidak ada gambar' }}</p>
                </div>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection