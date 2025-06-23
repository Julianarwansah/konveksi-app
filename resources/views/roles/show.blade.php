@extends('layouts.app')

@section('title', 'Detail Role')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Manajemen Role /</span> Detail Role
            </h4>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-2"></i> Kembali
            </a>
        </div>

        <!-- Card Detail Role -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Role</h5>
            </div>
            <div class="card-body">
                <!-- ID Role -->
                <div class="mb-4">
                    <label class="form-label fw-bold">ID Role</label>
                    <p class="mb-0">{{ $role->id }}</p>
                </div>

                <!-- Nama Role -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Nama Role</label>
                    <p class="mb-0">{{ $role->nama }}</p>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex gap-2">
                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">
                        <i class="bx bx-edit me-2"></i> Edit
                    </a>
                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus role ini?')">
                            <i class="bx bx-trash me-2"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection