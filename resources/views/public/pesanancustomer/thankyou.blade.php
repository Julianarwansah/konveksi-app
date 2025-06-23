@extends('layoutspublic.app')

@section('title', 'Terima Kasih atas Pesanan Anda')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card border-0 shadow-lg animate__animated animate__fadeIn">
                <div class="card-body py-5 px-4">
                    <!-- Animated Checkmark -->
                    <div class="mb-4 animate__animated animate__bounceIn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>

                    <!-- Main Content -->
                    <h2 class="mb-3 fw-bold text-gradient-success animate__animated animate__fadeInUp">Terima Kasih atas Pesanan Anda!</h2>
                    <p class="lead text-muted animate__animated animate__fadeInUp animate__delay-1s">
                        Pesanan Anda telah berhasil kami terima dan sedang kami proses.
                    </p>
                    <div class="alert alert-light animate__animated animate__fadeInUp animate__delay-2s">
                        <i class="fas fa-envelope me-2"></i> Kami akan mengirimkan email konfirmasi setelah pembayaran Anda diverifikasi.
                    </div>

                    <!-- General Order Info Only (Text) -->
                    <p class="mt-4 animate__animated animate__fadeInUp animate__delay-2s">
                        Mohon periksa email Anda secara berkala untuk informasi status pesanan.
                    </p>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-3 mt-5 animate__animated animate__fadeInUp animate__delay-3s">
                        <a href="{{ url('/') }}" class="btn btn-lg btn-outline-primary px-4 py-2">
                            <i class="fas fa-home me-2"></i> Kembali ke Beranda
                        </a>
                        <a href="{{ route('pesanan.customer') }}" class="btn btn-lg btn-primary px-4 py-2">
                            <i class="fas fa-history me-2"></i> Lihat Riwayat Pesanan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Additional Help Section -->
            <div class="mt-4 text-center animate__animated animate__fadeIn animate__delay-4s">
                <p class="text-muted">Butuh bantuan? <a href="{{ url('contact') }}" class="text-decoration-none">Hubungi kami</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Add Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    .text-gradient-success {
        background: linear-gradient(to right, #28a745, #5cb85c);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .card {
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .btn {
        transition: all 0.3s ease;
        border-radius: 8px;
    }

    .btn-primary {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-primary:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .btn-outline-primary {
        color: #28a745;
        border-color: #28a745;
    }

    .btn-outline-primary:hover {
        background-color: #28a745;
        color: white;
    }
</style>
@endsection
