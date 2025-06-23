@extends('layoutspublic.app')

@section('content')

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Form Pembayaran Ulang</h4>
                    <div class="breadcrumb__links">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('pesanan.customer') }}">Pesanan Saya</a>
                        <span>Form Pembayaran Ulang</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Shop Section Begin -->
<section class="shop spad">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Form Pembayaran Ulang Pesanan #{{ $pesanan->id }}</h4>
                    </div>

                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Detail Pembayaran</h5>
                            <table class="table table-bordered"><br>
                                <tr>
                                    <th>Total Harga</th>
                                    <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Pembayaran</th>
                                    <td class="fw-bold">
                                        {{ $persentase }}% (Rp {{ number_format($jumlah, 0, ',', '.') }})
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <hr>

                        <form action="{{ route('pesanan.simpan-bayar-ulang', $pesanan->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="metode" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="metode" name="metode" required disabled>
                                    <option value="Transfer Bank" selected>Transfer Bank</option>
                                </select>
                                <input type="hidden" name="metode" value="Transfer Bank">
                                <small class="text-muted">Pembayaran hanya dapat dilakukan via Transfer Bank</small>
                            </div>

                            <div class="mb-3 border p-3 bg-light">
                                <h6>Informasi Rekening Tujuan:</h6>
                                <p>
                                    <strong>Bank:</strong> BCA (Bank Central Asia)<br>
                                    <strong>Nomor Rekening:</strong> 089661770123<br>
                                    <strong>Atas Nama:</strong> Jul Konvek<br>
                                    <strong>Jumlah Transfer:</strong> Rp {{ number_format($jumlah, 0, ',', '.') }}
                                </p>
                                <div class="alert alert-warning">
                                    Harap transfer sesuai nominal yang tertera dan upload bukti transfer sebagai verifikasi.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="bukti_bayar" class="form-label">Bukti Pembayaran</label>
                                <input type="file" class="form-control" id="bukti_bayar" name="bukti_bayar" required>
                                <small class="text-muted">Upload bukti transfer (format: JPG/PNG/PDF, max 2MB)</small>
                            </div>

                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan (Opsional)</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Kirim Pembayaran Ulang
                                </button>
                                <a href="{{ route('pesanan.bayar-ulang', $pesanan->id) }}" class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Shop Section End -->

@endsection