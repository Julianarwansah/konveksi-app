@extends('layouts.app')

@section('title', 'Detail Biaya')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Detail Biaya</h4>

        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">ID</label>
                    <p>{{ $biaya->id }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama</label>
                    <p>{{ $biaya->nama }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Harga</label>
                    <p>Rp {{ number_format($biaya->harga, 0, ',', '.') }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <p>{{ $biaya->deskripsi ?? '-' }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Dibuat Pada</label>
                    <p>{{ $biaya->created_at->format('d M Y H:i') }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Diperbarui Pada</label>
                    <p>{{ $biaya->updated_at->format('d M Y H:i') }}</p>
                </div>

                <a href="{{ route('biaya.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>

        {{-- Jika ada custom biaya terkait, tampilkan daftar --}}
        @if($customBiayas->count())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Custom Biaya Terkait</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Custom</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customBiayas as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->custom->nama ?? '-' }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $customBiayas->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
