@extends('layoutspublic.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-md-6">
        <!-- Tambahkan class animasi -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden animate__animated animate__fadeInUp">
            <div class="bg-primary text-white text-center py-3">
                <h4 class="mb-0">Ganti Password</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('customer.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success px-5 py-2">Ganti Password</button>
                        <a href="{{ route('customer.profile') }}" class="btn btn-outline-secondary ms-2 px-5 py-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
