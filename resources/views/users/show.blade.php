@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail User</h4>

        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-bold">ID User</label>
                    <p>{{ $user->id }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Nama</label>
                    <p>{{ $user->nama }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Email</label>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Role</label>
                    <p>{{ $user->role->nama }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Gambar</label>
                    <p>{{ $user->img ?? 'Tidak ada gambar' }}</p>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection