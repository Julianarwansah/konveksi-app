@extends('layouts.app')

@section('title', 'Detail Supplier')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail Supplier</h4>

        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-bold">ID Supplier</label>
                    <p>{{ $supplier->id }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Nama</label>
                    <p>{{ $supplier->nama }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Kontak</label>
                    <p>{{ $supplier->kontak }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Alamat</label>
                    <p>{{ $supplier->alamat }}</p>
                </div>
                <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection