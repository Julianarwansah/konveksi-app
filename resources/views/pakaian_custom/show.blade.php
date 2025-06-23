@extends('layouts.app')

@section('title', 'Detail Pakaian Custom')

@section('content')
<style>
    /* Tambahkan CSS ini ke file CSS Anda */
    .lb-close {
        display: block !important;
        color: #fff !important;
        font-size: 100px !important;
        opacity: 1 !important;
    }
</style>
<div class="container-fluid">
    <h4 class="fw-bold py-3 mb-4">Detail Pakaian Custom</h4>

    <!-- Card untuk Menampilkan Detail -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Kolom Kiri: Informasi Umum -->
                <div class="col-md-6">
                    <h5 class="fw-bold">Informasi Umum</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>ID:</strong> {{ $pakaianCustom->id }}
                        </li>
                        <li class="list-group-item">
                            <strong>Nama Customer:</strong> {{ $pakaianCustom->customer->nama }}
                        </li>
                        <li class="list-group-item">
                            <strong>Pesanan ID:</strong> {{ $pakaianCustom->pesanan->id }}
                        </li>
                        <li class="list-group-item">
                            <strong>Ukuran:</strong> {{ $pakaianCustom->ukuran }}
                        </li>
                        <li class="list-group-item">
                            <strong>Warna:</strong> {{ $pakaianCustom->warna }}
                        </li>
                        <li class="list-group-item">
                            <strong>Model:</strong> {{ $pakaianCustom->model }}
                        </li>
                        <li class="list-group-item">
                            <strong>Harga Estimasi:</strong> Rp {{ number_format($pakaianCustom->harga_estimasi, 0, ',', '.') }}
                        </li>
                        <li class="list-group-item">
                            <strong>Status Produksi:</strong> {{ $pakaianCustom->status_produksi }}
                        </li>
                        <li class="list-group-item">
                            <strong>Catatan:</strong> {{ $pakaianCustom->catatan }}
                        </li>
                    </ul>
                </div>

                <!-- Kolom Kanan: Bahan Baku dan Gambar -->
                <div class="col-md-6">
                    <h5 class="fw-bold">Bahan Baku</h5>
                    <ul class="list-group list-group-flush">
                        @foreach($pakaianCustom->pakaianCustomDetail as $detail)
                            <li class="list-group-item">
                                <strong>Bahan Baku:</strong> {{ $detail->bahanBaku->nama }} <br>
                                <strong>Jumlah:</strong> {{ $detail->jumlah }} <br>
                                <strong>Harga Satuan:</strong> Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }} <br>
                                <strong>Sub Total:</strong> Rp {{ number_format($detail->sub_total, 0, ',', '.') }}
                            </li>
                        @endforeach
                    </ul>

                    <!-- Gambar -->
                    <div class="mt-4">
                        <h5 class="fw-bold">Gambar</h5>
                        @if($pakaianCustom->img)
                            <a href="{{ asset('storage/pakaian_custom/' . $pakaianCustom->img) }}" data-lightbox="image-1" data-title="Gambar Pakaian Custom">
                                <img src="{{ asset('storage/pakaian_custom/' . $pakaianCustom->img) }}" alt="Gambar Pakaian Custom" class="img-thumbnail" width="300">
                            </a>
                        @else
                            <span class="text-muted">Tidak ada gambar</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Kembali -->
    <div class="mt-3">
        <a href="{{ route('pakaian_custom.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection