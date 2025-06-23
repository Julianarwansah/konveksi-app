@extends('layouts.app')

@section('title', 'Profil Customer')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded-3">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Profil Customer</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($customer->img)
                            <img src="{{ asset('storage/' . $customer->img) }}" class="rounded-circle" width="150" height="150" alt="Foto Profil">
                        @else
                            <img src="{{ asset('assetspublic/img/icon/profile.png') }}" class="rounded-circle" width="150" height="150" alt="Default">
                        @endif
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th>Nama</th>
                            <td>{{ $customer->nama }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $customer->alamat }}</td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td>{{ $customer->no_telp }}</td>
                        </tr>
                    </table>

                    <div class="text-center">
                        <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-warning">Edit Profil</a>
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                           class="btn btn-danger">
                           Logout
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
