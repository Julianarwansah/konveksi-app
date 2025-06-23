@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail Pembayaran</h4>

        <div class="card">
            <div class="card-header">
                <a href="{{ route('pembayaran.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-arrow-back"></i> Kembali ke Daftar Pembayaran
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">Kode Pesanan</th>
                                    <td>{{ $pembayaran->pesanan->kode ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Pelanggan</th>
                                    <td>{{ $pembayaran->pesanan->customer->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Pembayaran</th>
                                    <td>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Metode Pembayaran</th>
                                    <td>{{ ucfirst($pembayaran->metode) }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pembayaran</th>
                                    <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Status Pembayaran</th>
                                    <td>
                                        <span class="badge 
                                            @if($pembayaran->status == 'Berhasil') bg-success 
                                            @elseif($pembayaran->status == 'Gagal') bg-danger 
                                            @elseif($pembayaran->status == 'Dibatalkan') bg-secondary
                                            @else bg-warning text-dark @endif">
                                            {{ $pembayaran->status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jenis Pembayaran</th>
                                    <td>
                                        @if($pembayaran->is_dp)
                                            <span class="badge bg-info">DP (Down Payment)</span>
                                        @else
                                            <span class="badge bg-success">Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Catatan</th>
                                    <td>{{ $pembayaran->catatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>{{ $pembayaran->createdBy->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diperbarui Oleh</th>
                                    <td>{{ $pembayaran->updatedBy->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Pada</th>
                                    <td>{{ \Carbon\Carbon::parse($pembayaran->created_at)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Diperbarui Pada</th>
                                    <td>{{ \Carbon\Carbon::parse($pembayaran->updated_at)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Bukti Pembayaran</h5>
                        @if($pembayaran->bukti_bayar)
                            @php
                                $fileExtension = pathinfo($pembayaran->bukti_bayar, PATHINFO_EXTENSION);
                            @endphp

                            @if(in_array($fileExtension, ['jpg', 'jpeg', 'png']))
                                <img src="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 400px;">
                            @elseif($fileExtension == 'pdf')
                                <div class="alert alert-info" role="alert">
                                    <i class="bx bxs-file-pdf"></i> Bukti pembayaran adalah file PDF. Klik untuk melihat atau mengunduh.
                                </div>
                                <a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank" class="btn btn-primary btn-sm">Lihat/Unduh PDF</a>
                            @else
                                <div class="alert alert-warning" role="alert">
                                    Tidak ada preview tersedia untuk tipe file ini.
                                </div>
                                <a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank" class="btn btn-primary btn-sm">Unduh File</a>
                            @endif
                            <p class="mt-2"><a href="{{ asset('storage/' . $pembayaran->bukti_bayar) }}" target="_blank">Lihat bukti pembayaran</a></p>
                        @else
                            <p>Tidak ada bukti pembayaran diunggah.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                {{-- Hanya tampilkan tombol edit jika user adalah 'kasir' --}}
                @if(auth()->user()->role->nama == 'Kasir')
                    <a href="{{ route('pembayaran.edit', $pembayaran->id) }}" class="btn btn-warning">
                        <i class="bx bx-edit"></i> Edit Pembayaran
                    </a>
                    <form action="{{ route('pembayaran.destroy', $pembayaran->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')">
                            <i class="bx bx-trash"></i> Hapus Pembayaran
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection