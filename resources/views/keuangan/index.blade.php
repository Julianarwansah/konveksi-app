@extends('layouts.app')

@section('title', 'Manajemen Keuangan')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Keuangan</h4>

        <!-- Kotak Total Pemasukan, Pengeluaran dan Saldo -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-success shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Pemasukan</h5>
                        <h3 class="card-text">Rp {{ number_format($keuangan->sum('total_pemasukan'), 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Pengeluaran</h5>
                        <h3 class="card-text">Rp {{ number_format($keuangan->sum('total_pengeluaran'), 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-primary shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Saldo</h5>
                        <h3 class="card-text">Rp {{ number_format($keuangan->sum('saldo'), 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header dengan Tombol Tambah dan Form Filter -->
        <div class="row g-3 align-items-center mb-3">

            <div class="col-12 col-md">
                <form action="{{ route('keuangan.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-4">
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>

                    <div class="col-12 col-md-4">
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>

                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                    </div>

                    <div class="col-12 col-md-2">
                        <a href="{{ route('keuangan.export', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                        class="btn btn-success btn-sm w-100">
                            Export Excel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabel Keuangan -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 20%;">Pemasukan</th>
                                <th style="width: 20%;">Pengeluaran</th>
                                <th style="width: 20%;">Saldo</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($keuangan as $item)
                                <tr>
                                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                                    <td>Rp {{ number_format($item->total_pemasukan, 2, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->total_pengeluaran, 2, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->saldo, 2, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('keuangan.show', $item->id) }}" class="btn btn-success btn-sm">Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection