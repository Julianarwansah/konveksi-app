@extends('layoutspublic.app')

@section('content')

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Pembayaran Ulang</h4>
                    <div class="breadcrumb__links">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('pesanan.customer') }}">Pesanan Saya</a>
                        <span>Pembayaran Ulang</span>
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
                        <h4>Pembayaran Ulang Pesanan #{{ $pesanan->id }}</h4>
                    </div>

                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="mb-4">
                            <h5>Detail Pesanan</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Jenis Pesanan</th>
                                    <td>{{ ucfirst($pesanan->jenis_pesanan) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Harga</th>
                                    <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Status Pembayaran</th>
                                    <td class="text-danger fw-bold">{{ $pesanan->status_pembayaran }}</td>
                                </tr>
                            </table>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h5>Pilih Metode Pembayaran Ulang</h5>
                            
                            @if($pesanan->jenis_pesanan === 'produk')
                                <div class="text-center">
                                    <p>Untuk pesanan produk, Anda harus membayar penuh total pesanan.</p>
                                    <a href="{{ route('pesanan.proses-bayar-ulang', ['id' => $pesanan->id, 'persentase' => 100]) }}" 
                                       class="btn btn-primary btn-lg">
                                        Bayar 100% (Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }})
                                    </a>
                                </div>
                            @elseif($pesanan->jenis_pesanan === 'custom')
                                <div class="row text-center">
                                    <div class="col-md-6 mb-3">
                                        <h6>Pembayaran Sebagian (DP 50%)</h6>
                                        <p>Bayar 50% dari total harga sebagai DP</p>
                                        @php $dpAmount = $pesanan->total_harga * 0.5; @endphp
                                        <a href="{{ route('pesanan.proses-bayar-ulang', ['id' => $pesanan->id, 'persentase' => 50]) }}" 
                                           class="btn btn-warning btn-lg">
                                            Bayar 50% (Rp {{ number_format($dpAmount, 0, ',', '.') }})
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Pembayaran Penuh (100%)</h6>
                                        <p>Bayar seluruh total harga sekaligus</p>
                                        <a href="{{ route('pesanan.proses-bayar-ulang', ['id' => $pesanan->id, 'persentase' => 100]) }}" 
                                           class="btn btn-success btn-lg">
                                            Bayar 100% (Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }})
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('pesanan.customer') }}" class="btn btn-secondary">
                                Kembali ke Daftar Pesanan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Shop Section End -->

@endsection