@extends('layoutspublic.app')

@section('content')

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h2>Panduan Pemesanan</h2>
                        <div class="breadcrumb__links">
                            <a href="{{ url('/') }}">Home</a>
                            <span>Panduan Lengkap Pemesanan Produk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Guide Section Begin -->
    <section class="guide-section spad">
        <div class="container">
            <div class="guide__content">
                <h1 class="guide__title">Panduan Lengkap Pemesanan Produk</h1>
                
                <!-- Registration Step -->
                <div class="guide-step">
                    <h3 class="step-title">1. Registrasi Akun</h3>
                    <div class="step-content">
                        <div class="step-text">
                            <p class="step-description">
                                <span class="step-number">1.</span> Klik tombol <strong>Register</strong> di pojok kanan atas halaman<br>
                                <span class="step-number">2.</span> Isi formulir registrasi dengan data lengkap Anda<br>
                                <span class="step-number">3.</span> Klik buton daftar sekarang<br>
                                <span class="step-number">4.</span> Akun Anda siap digunakan
                            </p>
                        </div>
                        <div class="step-image">
                            <div class="image-container text-center mb-4">
                                <img src="{{ asset('assetspublic/img/faqproduk/register.png') }}" alt="Tutorial Registrasi" class="img-fluid zoomable-image" style="max-width: 100%; height: auto; cursor: zoom-in;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Login Step -->
                <div class="guide-step">
                    <h3 class="step-title">2. Login ke Akun Anda</h3>
                    <div class="step-content">
                        <div class="step-text">
                            <p class="step-description">
                                <span class="step-number">1.</span> Klik tombol <strong>Login</strong> di pojok kanan atas<br>
                                <span class="step-number">2.</span> Masukkan email dan password yang telah didaftarkan<br>
                                <span class="step-number">3.</span> Klik tombol <strong>Login</strong> untuk masuk ke sistem dan melakukan pemesanan
                            </p>
                        </div>
                        <div class="step-image">
                            <div class="image-container text-center mb-4">
                                <img src="{{ asset('assetspublic/img/faqproduk/login.png') }}" alt="Tutorial Login" class="img-fluid zoomable-image" style="max-width: 100%; height: auto; cursor: zoom-in;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product Selection -->
                <div class="guide-step">
                    <h3 class="step-title">3. Memilih Produk</h3>
                    <div class="step-content">
                        <div class="step-text">
                            <p class="step-description">
                                <span class="step-number">1.</span> Buka halaman <a href="{{ route('produkshop.index') }}">Produk Kami</a><br>
                                <span class="step-number">2.</span> Masuk Kedalam Halam Produk<br>
                                <span class="step-number">3.</span> Klik produk untuk melihat detail lengkap<br>
                                <span class="step-number">4.</span> Pilih jumlah dan variasi<br>
                                <span class="step-number">5.</span> Klik tombol <strong>Tambahkan ke Keranjang</strong>
                            </p>
                        </div>
                        <div class="step-image">
                            <div class="image-container text-center mb-4">
                                <img src="{{ asset('assetspublic/img/faqproduk/produk.png') }}" alt="Tutorial Produk" class="img-fluid zoomable-image" style="max-width: 100%; height: auto; cursor: zoom-in;">
                            </div>
                            <div class="image-container text-center mb-4">
                                <img src="{{ asset('assetspublic/img/faqproduk/produkdetail.png') }}" alt="Detail Produk" class="img-fluid zoomable-image" style="max-width: 100%; height: auto; cursor: zoom-in;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cart Page -->
                <div class="guide-step">
                    <h3 class="step-title">4. Halaman Keranjang Belanja</h3>
                    <div class="step-content">
                        <div class="step-text">
                            <p class="step-description">
                                <span class="step-number">1.</span> Klik ikon keranjang di pojok kanan atas<br>
                                <span class="step-number">2.</span> Periksa kembali produk yang dipilih<br>
                                <span class="step-number">3.</span> Update jumlah jika diperlukan<br>
                                <span class="step-number">4.</span> Klik tombol <strong>Lanjutkan ke Pembayaran</strong><br>
                                <span class="step-number">5.</span> Pilih metode pengiriman dan isi alamat lengkap
                            </p>
                        </div>
                        <div class="step-image">
                            <div class="image-container text-center mb-4">
                                <img src="{{ asset('assetspublic/img/faqproduk/kranjang.png') }}" alt="Keranjang Belanja" class="img-fluid zoomable-image" style="max-width: 100%; height: auto; cursor: zoom-in;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Process -->
                <div class="guide-step">
                    <h3 class="step-title">5. Proses Pembayaran</h3>
                    <div class="step-content">
                        <div class="step-text">
                            <p class="step-description">
                                <span class="step-number">1.</span> Pilih metode pembayaran yang tersedia<br>
                                <span class="step-number">2.</span> Klik tombol <strong>Proses Pembayaran</strong><br>
                                <span class="step-number">3.</span> Ikuti instruksi untuk menyelesaikan pembayaran<br>
                            </p>
                        </div>
                        <div class="step-image">
                            <div class="image-container text-center mb-4">
                                <img src="{{ asset('assetspublic/img/faqproduk/checkout.png') }}" alt="Proses Pembayaran" class="img-fluid zoomable-image" style="max-width: 100%; height: auto; cursor: zoom-in;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order History -->
                <div class="guide-step">
                    <h3 class="step-title">6. Melihat History Pemesanan</h3>
                    <div class="step-content">
                        <div class="step-text">
                            <p class="step-description">
                                <span class="step-number">1.</span> Login ke akun Anda<br>
                                <span class="step-number">2.</span> Klik menu <strong>History Pemesanan</strong> di dashboard<br>
                                <span class="step-number">3.</span> Anda akan melihat daftar semua transaksi<br>
                                <span class="step-number">4.</span> Klik detail untuk melihat informasi lengkap pesanan<br>
                                <span class="step-number">5.</span> Lacak pengiriman status pesanan Anda
                            </p>
                        </div>
                        <div class="step-image">
                            <div class="image-container text-center mb-4">
                                <img src="{{ asset('assetspublic/img/faqproduk/pesanan.png') }}" alt="History Pemesanan" class="img-fluid zoomable-image" style="max-width: 100%; height: auto; cursor: zoom-in;">
                            </div>
                            <div class="image-container text-center mb-4">
                                <img src="{{ asset('assetspublic/img/faqproduk/detailpesanan.png') }}" alt="Detail Pemesanan" class="img-fluid zoomable-image" style="max-width: 100%; height: auto; cursor: zoom-in;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Support -->
                <div class="support-section">
                    <h3 class="support-title">Butuh Bantuan?</h3>
                    <div class="support-content">
                        <p class="support-text">
                            Jika mengalami kesulitan dalam proses pemesanan, silakan hubungi kami melalui:
                        </p>
                        <ul class="support-list">
                            <li>WhatsApp: <a href="https://wa.me/6289661770123">089661770123</a></li>
                            <li>Email: <a href="mailto:julianarwansahh@gmail.com">julianarwansahh@gmail.com</a></li>
                            <li>Halaman <a href="{{ url('/contact') }}">Kontak Kami</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Guide Section End -->

    <!-- Image Zoom Modal -->
    <div class="modal fade" id="imageZoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="zoomedImage" src="" alt="Zoomed Image" class="img-fluid" style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Styles for Guide Page */
        .guide-section {
            padding: 50px 0;
            font-size: 18px;
            line-height: 1.6;
        }
        
        .guide__title {
            font-size: 32px;
            margin-bottom: 40px;
            color: #333;
            text-align: center;
        }
        
        .guide-step {
            margin-bottom: 50px;
            padding: 30px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .step-title {
            font-size: 24px;
            color: #2a2a2a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .step-content {
            display: flex;
            gap: 30px;
        }
        
        .step-text {
            flex: 1;
        }
        
        .step-image {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .step-description {
            font-size: 18px;
            color: #444;
        }
        
        .step-number {
            display: inline-block;
            width: 25px;
            font-weight: bold;
            color: #e44d26;
        }
        
        .image-container {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .image-container:hover {
            transform: translateY(-5px);
        }
        
        .zoomable-image {
            transition: transform 0.3s ease;
        }
        
        .zoomable-image:hover {
            transform: scale(1.02);
        }
        
        .support-section {
            margin-top: 50px;
            padding: 30px;
            background: #f0f7ff;
            border-radius: 10px;
        }
        
        .support-title {
            font-size: 24px;
            color: #2a2a2a;
            margin-bottom: 20px;
        }
        
        .support-text {
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .support-list {
            font-size: 18px;
            padding-left: 20px;
        }
        
        .support-list li {
            margin-bottom: 10px;
        }
        
        /* Modal Zoom Styles */
        #imageZoomModal .modal-content {
            background: transparent;
        }
        
        #imageZoomModal .modal-body {
            padding: 0;
        }
        
        #zoomedImage {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }
        
        @media (max-width: 768px) {
            .step-content {
                flex-direction: column;
            }
            
            .guide-step {
                padding: 20px;
            }
            
            .guide__title {
                font-size: 28px;
            }
            
            .step-title, .support-title {
                font-size: 22px;
            }
            
            .step-description, .support-text, .support-list {
                font-size: 16px;
            }
        }
    </style>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize zoom functionality
            $('.zoomable-image').click(function() {
                const imgSrc = $(this).attr('src');
                const imgAlt = $(this).attr('alt');
                
                $('#zoomedImage').attr('src', imgSrc).attr('alt', imgAlt);
                $('#imageZoomModal').modal('show');
            });
            
            // Close modal when clicking outside image
            $('#imageZoomModal').click(function(e) {
                if ($(e.target).is('#imageZoomModal')) {
                    $(this).modal('hide');
                }
            });
            
            // Keyboard navigation
            $(document).keydown(function(e) {
                if (e.key === "Escape") {
                    $('#imageZoomModal').modal('hide');
                }
            });
        });
    </script>
@endsection