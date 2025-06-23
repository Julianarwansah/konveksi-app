@extends('layoutspublic.app')

@section('content')
<style>
    .edit-profile-wrapper {
        padding: 60px 0;
        background-color: #f9f9f9;
    }

    .edit-profile-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 40px;
    }

    .edit-profile-header {
        background: #1e2a38;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        color: #fff;
        padding: 20px;
        text-align: center;
        font-weight: bold;
        font-size: 1.5rem;
        margin: -40px -40px 30px;
    }

    .profile-photo-preview {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #e0e0e0;
        margin-bottom: 10px;
        background-color: #f1f1f1;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .form-control, .form-control-file {
        border-radius: 8px;
    }

    .btn-save {
        border-radius: 30px;
        min-width: 150px;
    }

    .btn-cancel {
        border-radius: 30px;
        min-width: 120px;
        margin-left: 10px;
    }

    @media (max-width: 576px) {
        .btn-cancel {
            margin-left: 0;
            margin-top: 10px;
        }
    }
</style>

<div class="edit-profile-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="edit-profile-card">
                    <div class="edit-profile-header">
                        Edit Profil
                    </div>

                    <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="text-center mb-4">
                            @if($customer->img)
                                <img src="{{ asset('storage/' . $customer->img) }}" class="profile-photo-preview" alt="Foto Profil">
                            @else
                                <img src="{{ asset('assetspublic/img/icon/profile.png') }}" class="profile-photo-preview" alt="Default">
                            @endif
                            <div class="form-group mt-2">
                                <label for="img">Ubah Foto Profil</label>
                                <input type="file" class="form-control-file mt-1" id="img" name="img">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $customer->nama) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="alamat">Alamat Lengkap</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $customer->alamat) }}</textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label for="no_telp">No. Telepon</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp" value="{{ old('no_telp', $customer->no_telp) }}">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-save">
                                <i class="bi bi-save-fill"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('customer.profile') }}" class="btn btn-secondary btn-cancel">
                                <i class="bi bi-x-circle-fill"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
