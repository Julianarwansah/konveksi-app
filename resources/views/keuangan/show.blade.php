@extends('layouts.app')

@section('title', 'Detail Catatan Keuangan')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail Catatan Keuangan</h4>

        <!-- Card Detail Keuangan -->
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Informasi Keuangan</h5>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tanggal</label>
                        <p>{{ $keuangan->tanggal->format('d F Y') }}</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Total Pemasukan</label>
                        <p>Rp {{ number_format($keuangan->total_pemasukan, 2, ',', '.') }}</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Total Pengeluaran</label>
                        <p>Rp {{ number_format($keuangan->total_pengeluaran, 2, ',', '.') }}</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Saldo</label>
                        <p>Rp {{ number_format($keuangan->saldo, 2, ',', '.') }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <p>{{ $keuangan->catatan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Kembali -->
        <div class="text-end">
            <a href="{{ route('keuangan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
@endsection