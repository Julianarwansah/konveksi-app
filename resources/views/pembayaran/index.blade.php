@extends('layouts.app')

@section('title', 'Manajemen Pembayaran')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Manajemen Pembayaran</h4>

        <div class="row g-3 align-items-center mb-3">
            <div class="col-12 col-md-auto">
                {{-- Hanya tampilkan tombol tambah jika user adalah 'kasir' --}}
                @if(auth()->user()->role->nama == 'Kasir')
                    <a href="{{ route('pembayaran.create') }}" class="btn btn-primary w-100 w-md-auto">Tambah Pembayaran</a>
                @endif
            </div>

            <div class="col-12 col-md">
                <form action="{{ route('pembayaran.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-4">
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>

                    <div class="col-12 col-md-4">
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>

                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
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

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 15%;">Pesanan</th>
                                <th style="width: 10%;">Jumlah</th>
                                <th style="width: 10%;">Metode</th>
                                <th style="width: 10%;">Tanggal</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 10%;">DP/Lunas</th>
                                <th style="width: 25%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembayaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration + ($pembayaran->currentPage() - 1) * $pembayaran->perPage() }}</td>
                                    <td>
                                        <div>{{ $item->pesanan->id ?? '-' }}</div>
                                        <small class="text-muted">{{ $item->pesanan->customer->nama ?? '-' }}</small>
                                    </td>
                                    <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($item->metode) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($item->status == 'Berhasil') bg-success 
                                            @elseif($item->status == 'Gagal') bg-danger 
                                            @elseif($item->status == 'Dibatalkan') bg-secondary
                                            @else bg-warning text-dark @endif">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->is_dp)
                                            <span class="badge bg-info">DP</span>
                                        @else
                                            <span class="badge bg-success">Lunas</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('pembayaran.show', $item->id) }}" class="btn btn-info btn-sm" title="Detail">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            {{-- Hanya tampilkan tombol edit dan delete jika user adalah 'kasir' --}}
                                            @if(auth()->user()->role->nama == 'Kasir')
                                                <a href="{{ route('pembayaran.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form action="{{ route('pembayaran.destroy', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus pembayaran ini?')" title="Hapus">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data pembayaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pembayaran->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row">
                    <div class="text-muted small">
                        Menampilkan {{ $pembayaran->firstItem() }} - {{ $pembayaran->lastItem() }} dari total {{ $pembayaran->total() }} data pembayaran
                    </div>
                    <div>
                        {{ $pembayaran->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="statusForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Update Status Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="status" id="modalStatus">
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle update status button click
        $('.update-status').click(function() {
            const pembayaranId = $(this).data('id');
            const status = $(this).data('status');
            
            $('#modalStatus').val(status);
            $('#statusForm').attr('action', `/pembayaran/${pembayaranId}/status`);
            
            // Show modal
            $('#statusModal').modal('show');
        });
    });
</script>
@endpush