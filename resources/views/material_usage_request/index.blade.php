@extends('layouts.app')

@section('title', 'Manajemen Material Usage Request')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Material Usage Request</h4>

        <!-- Header dengan Tombol Tambah dan Form Search -->
        <div class="row g-3 align-items-center mb-3">
            <!-- Tombol Tambah -->
            <div class="col-12 col-md-auto">
                <a href="{{ route('material_usage_request.create') }}" class="btn btn-primary">Buat Material Usage Request</a>
            </div>

            <!-- Form untuk Search -->
            <div class="col-12 col-md">
                <form action="{{ route('material_usage_request.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari request..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12 col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Cari</button>
                    </div>
                </form>
            </div>

            <!-- Tombol Export Excel -->
            <div class="col-12 col-md-auto">
                <form action="{{ route('material_usage_request.export_excel') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <button type="submit" class="btn btn-success w-100 w-md-auto">Export Excel</button>
                </form>
            </div>
        </div>

        <!-- Notifikasi Sukses -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabel -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Permintaan</th>
                                <th>Jumlah Item</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materialUsageRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->requestedBy->nama ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($request->tanggal_permintaan)->format('d-m-Y') }}</td>
                                    <td>{{ $request->details->count() }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($request->status == 'Disetujui') bg-success
                                            @elseif($request->status == 'Ditolak') bg-danger
                                            @else bg-warning text-dark @endif">
                                            {{ $request->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('material_usage_request.show', $request->id) }}" class="btn btn-success btn-sm">Detail</a>

                                            @if($request->status == 'Pending')
                                                <a href="{{ route('material_usage_request.edit', $request->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="{{ route('material_usage_request.destroy', $request->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus request ini?')">Hapus</button>
                                                </form>
                                            @endif

                                            <a href="{{ route('material_usage_request.export_pdf', $request->id) }}" class="btn btn-info btn-sm">PDF</a>

                                            @can('approve_material_usage_request')
                                                @if($request->status == 'Pending')
                                                    <form action="{{ route('material_usage_request.approve', $request->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                                    </form>
                                                    <form action="{{ route('material_usage_request.reject', $request->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row">
                    <div class="text-muted small">
                        Menampilkan {{ $materialUsageRequests->firstItem() }} - {{ $materialUsageRequests->lastItem() }} dari total {{ $materialUsageRequests->total() }} permintaan
                    </div>
                    <div>
                        {{ $materialUsageRequests->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
