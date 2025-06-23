@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Account Settings /</span> Account
  </h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">Profile Details</h5>
        <!-- Account -->
        <hr class="my-0" />
        <div class="card-body">
          <!-- Tampilkan pesan sukses -->
          @if(session('success'))
            <div class="alert alert-success">
              {{ session('success') }}
            </div>
          @endif

          <!-- Tampilkan pesan error -->
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="nama" class="form-label">Nama</label>
                <input
                  class="form-control"
                  type="text"
                  id="nama"
                  name="nama"
                  value="{{ Auth::user()->nama }}"
                  autofocus
                />
              </div>
              <div class="mb-3 col-md-6">
                <label for="email" class="form-label">E-mail</label>
                <input
                  class="form-control"
                  type="email"
                  id="email"
                  name="email"
                  value="{{ Auth::user()->email }}"
                  placeholder="Email"
                />
              </div>
              <div class="mb-3 col-md-6">
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  name="password"
                  placeholder="Password"
                />
              </div>
              <div class="mb-3 col-md-6">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password_confirmation"
                  name="password_confirmation"
                  placeholder="Confirm Password"
                />
              </div>
              <div class="mb-3 col-md-6">
                <label for="role_id" class="form-label">Bagian</label>
                <input
                  type="text"
                  class="form-control"
                  id="role_id"
                  name="role_id"
                  value="{{ Auth::user()->role->nama }}"
                  readonly
                />
              </div>
              <div class="mb-3 col-md-6">
                <label for="img" class="form-label">Avatar</label>
                <input
                  type="file"
                  class="form-control"
                  id="img"
                  name="img"
                />
                @if(Auth::user()->img)
                  <img id="uploadedAvatar" src="{{ Storage::url('avatars/' . Auth::user()->img) }}" alt="Avatar" class="rounded-circle mt-2" width="100" height="100">
                @else
                  <img id="uploadedAvatar" src="{{ asset('assets/img/avatars/default.png') }}" alt="Avatar" class="rounded-circle mt-2" width="100" height="100">
                @endif
              </div>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-primary me-2">Save changes</button>
              <button type="reset" class="btn btn-outline-secondary">Cancel</button>
            </div>
          </form>
        </div>
        <!-- /Account -->
      </div>
    </div>
  </div>
</div>

<script>
  // Script untuk menampilkan preview gambar
  document.getElementById('img').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('uploadedAvatar').src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
</script>
@endsection