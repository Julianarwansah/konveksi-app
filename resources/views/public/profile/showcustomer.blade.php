@extends('layoutspublic.app')

@section('content')
<style>
    .profile-wrapper {
        background: #f1f1f1;
        padding: 50px 0;
    }

    .profile-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        text-align: center;
        padding: 40px 30px;
        transition: all 0.3s ease-in-out;
    }

    .profile-card:hover {
        transform: translateY(-5px);
    }

    .profile-image {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #e1e1e1;
        margin-bottom: 20px;
        background-color: #f9f9f9;
    }

    .profile-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
    }

    .profile-email {
        color: #888;
        font-size: 0.95rem;
        margin-bottom: 25px;
    }

    .info-box {
        background: #f8f8f8;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        color: #333;
        font-size: 0.95rem;
    }

    .btn-profile {
        min-width: 130px;
        border-radius: 30px;
        padding: 10px 20px;
    }

    .btn-profile + .btn-profile {
        margin-left: 10px;
    }

    @media (max-width: 576px) {
        .btn-profile {
            width: 100%;
            margin-bottom: 10px;
        }

        .btn-profile + .btn-profile {
            margin-left: 0;
        }
    }
</style>

<div class="profile-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="profile-card">

                    @if($customer->img)
                        <img src="{{ asset('storage/' . $customer->img) }}" alt="Foto Profil" class="profile-image">
                    @else
                        <img src="{{ asset('assetspublic/img/icon/profile.png') }}" alt="Default" class="profile-image">
                    @endif

                    <div class="profile-name">{{ $customer->nama }}</div>
                    <div class="profile-email">{{ $customer->email }}</div>

                    <div class="info-box">
                        <strong>Alamat:</strong><br>{{ $customer->alamat ?? '-' }}
                    </div>
                    <div class="info-box">
                        <strong>No. Telepon:</strong><br>{{ $customer->no_telp ?? '-' }}
                    </div>

                    <div class="d-flex flex-wrap justify-content-center mt-4">
                        <a href="{{ route('customer.profile.edit') }}" class="btn btn-warning btn-profile">
                            <i class="bi bi-pencil-fill"></i> Edit Profil
                        </a>
                        <a href="{{ route('customer.password.edit') }}" class="btn btn-info text-white btn-profile">
                            <i class="bi bi-lock-fill"></i> Ganti Password
                        </a>
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="btn btn-danger btn-profile">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
