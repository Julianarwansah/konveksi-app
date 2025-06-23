<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registrasi Customer - Perusahaan Konveksi</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .strength-0 { width: 0%; background-color: #ff0000; }
        .strength-1 { width: 25%; background-color: #ff5252; }
        .strength-2 { width: 50%; background-color: #ffb142; }
        .strength-3 { width: 75%; background-color: #33d9b2; }
        .strength-4 { width: 100%; background-color: #2ed573; }
        .form-control:focus {
            border-color: #7367f0;
            box-shadow: 0 0 0 0.25rem rgba(115, 103, 240, 0.25);
        }
        .register-card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .app-brand {
            margin-bottom: 1.5rem;
        }
        .app-brand-text {
            font-size: 1.75rem;
            color: #7367f0;
            font-weight: 700;
        }
        .btn-register {
            background-color: #7367f0;
            border: none;
            padding: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-register:hover {
            background-color: #5d50e6;
        }
        .login-link {
            color: #7367f0;
            font-weight: 500;
        }
        .login-link:hover {
            text-decoration: underline;
        }
        .input-group-text {
            cursor: pointer;
            transition: all 0.3s;
        }
        .input-group-text:hover {
            background-color: #f8f8f8;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card register-card">
                    <div class="card-body">
                        <div class="app-brand justify-content-center">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-text demo">jul konvek</span>
                            </a>
                        </div>
                        <h4 class="mb-2 text-center fw-bold">Buat Akun Baru</h4>
                        <p class="mb-4 text-center">Daftar sekarang untuk mulai memesan produk konveksi kami</p>

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('customer.register') }}" class="mt-4" id="registerForm">
                            @csrf

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email') }}" placeholder="contoh@email.com" required>
                                </div>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                                    <small class="text-muted">Minimal 8 karakter</small>
                                </div>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="password" class="form-control" name="password"
                                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                                           aria-describedby="password" required minlength="8" />
                                    <span class="input-group-text cursor-pointer toggle-password"><i class="fas fa-eye"></i></span>
                                </div>
                                <div class="password-strength strength-0 mt-2" id="passwordStrength"></div>
                                <div class="password-requirements mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Gunakan kombinasi huruf besar, kecil, angka, dan simbol
                                    </small>
                                </div>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="password_confirmation" class="form-control" 
                                           name="password_confirmation"
                                           placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                                           aria-describedby="password" required minlength="8" />
                                    <span class="input-group-text cursor-pointer toggle-password"><i class="fas fa-eye"></i></span>
                                </div>
                                <div id="passwordMatch" class="mt-2 small"></div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Lengkap</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="2"
                                              placeholder="Jl. Contoh No. 123, Kota/Kabupaten, Provinsi">{{ old('alamat') }}</textarea>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="no_telp" class="form-label">Nomor Telepon/WhatsApp <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control" id="no_telp" name="no_telp" 
                                           value="{{ old('no_telp') }}" placeholder="081234567890" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        Saya menyetujui <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Syarat dan Ketentuan</a>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button class="btn btn-register d-grid w-100" type="submit" id="registerButton">
                                    <span class="d-flex align-items-center justify-content-center">
                                        <span id="registerText">Daftar Sekarang</span>
                                        <span id="registerSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                                    </span>
                                </button>
                            </div>
                        </form>

                        <p class="text-center mt-4">
                            <span>Sudah punya akun?</span>
                            <a href="{{ route('login') }}" class="login-link">
                                <span>Masuk disini</span>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Syarat dan Ketentuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Ketentuan Umum</h6>
                    <p>Dengan mendaftar sebagai customer, Anda menyetujui semua syarat dan ketentuan yang berlaku di platform kami.</p>
                    
                    <h6>2. Data Pribadi</h6>
                    <p>Kami akan menjaga kerahasiaan data pribadi Anda sesuai dengan kebijakan privasi kami.</p>
                    
                    <h6>3. Akun Pengguna</h6>
                    <p>Anda bertanggung jawab penuh atas kerahasiaan akun dan password Anda.</p>
                    
                    <h6>4. Pemesanan Produk</h6>
                    <p>Setiap pemesanan yang dilakukan melalui akun Anda menjadi tanggung jawab Anda.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
    
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('.toggle-password').click(function() {
                const input = $(this).siblings('input');
                const icon = $(this).find('i');
                
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
            // Password strength meter
            $('#password').on('input', function() {
                const password = $(this).val();
                const result = zxcvbn(password);
                const strength = result.score;
                
                $('#passwordStrength').removeClass().addClass('password-strength strength-' + strength);
                
                // Update password requirements text
                if (password.length === 0) {
                    $('.password-requirements small').html('<i class="fas fa-info-circle"></i> Gunakan kombinasi huruf besar, kecil, angka, dan simbol');
                } else if (password.length < 8) {
                    $('.password-requirements small').html('<i class="fas fa-exclamation-circle text-danger"></i> Password minimal 8 karakter');
                } else if (strength < 2) {
                    $('.password-requirements small').html('<i class="fas fa-exclamation-circle text-danger"></i> Password terlalu lemah');
                } else if (strength < 4) {
                    $('.password-requirements small').html('<i class="fas fa-check-circle text-warning"></i> Password cukup, bisa lebih kuat');
                } else {
                    $('.password-requirements small').html('<i class="fas fa-check-circle text-success"></i> Password kuat');
                }
            });
            
            // Password confirmation check
            $('#password_confirmation').on('input', function() {
                const password = $('#password').val();
                const confirmPassword = $(this).val();
                
                if (confirmPassword.length === 0) {
                    $('#passwordMatch').html('');
                } else if (password !== confirmPassword) {
                    $('#passwordMatch').html('<i class="fas fa-times-circle text-danger"></i> Password tidak cocok');
                } else {
                    $('#passwordMatch').html('<i class="fas fa-check-circle text-success"></i> Password cocok');
                }
            });
            
            // Form submission
            $('#registerForm').submit(function() {
                $('#registerButton').prop('disabled', true);
                $('#registerText').text('Mendaftarkan...');
                $('#registerSpinner').removeClass('d-none');
            });
            
            // Format phone number
            $('#no_telp').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>
</html>