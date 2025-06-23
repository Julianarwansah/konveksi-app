@extends('layouts.app')

@section('title', 'Detail Material Usage Request')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail Material Usage Request</h4>

        <!-- Informasi Umum -->
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Informasi Permintaan Penggunaan Material</h5>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Dibuat Oleh</label>
                        <p>{{ $materialUsageRequest->createdBy->nama }}</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tanggal Permintaan</label>
                        <p>{{ \Carbon\Carbon::parse($materialUsageRequest->tanggal_permintaan)->format('d-m-Y') }}</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Status</label>
                        <p>
                            <span class="badge 
                                @if($materialUsageRequest->status == 'Disetujui') bg-success
                                @elseif($materialUsageRequest->status == 'Ditolak') bg-danger
                                @else bg-warning text-dark @endif">
                                {{ $materialUsageRequest->status }}
                            </span>
                        </p>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Catatan</label>
                        <p>{{ $materialUsageRequest->catatan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Material -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detail Material yang Diminta</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Material</th>
                                <th>Jumlah Diminta</th>
                                <th>Jumlah Disetujui</th>
                                <th>Satuan</th>
                                <th>Stok Saat Ini</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materialUsageRequest->details as $detail)
                                <tr>
                                    <td>{{ $detail->material->nama }}</td>
                                    <td>{{ $detail->jumlah_diminta }}</td>
                                    <td>{{ $detail->jumlah_disetujui ?? '-' }}</td>
                                    <td>{{ $detail->material->satuan }}</td>
                                    <td>{{ $detail->material->stok }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="text-end mt-3">
            @if($materialUsageRequest->status == 'Pending')
                <a href="{{ route('material_usage_request.edit', $materialUsageRequest->id) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('material_usage_request.approve', $materialUsageRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">Setujui</button>
                </form>
                <form action="{{ route('material_usage_request.reject', $materialUsageRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </form>
            @endif

            <a href="{{ route('material_usage_request.export_pdf', $materialUsageRequest->id) }}" class="btn btn-info">Export PDF</a>
            <a href="{{ route('material_usage_request.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
@endsection
