@extends('layouts.app')

@section('title', 'Edit Material Usage Request')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Material Usage Request</h4>

        <!-- Form Edit Material Usage Request -->
        <form action="{{ route('material_usage_request.update', $materialUsageRequest->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Pembuat Request -->
                        <div class="col-12 col-md-6">
                            <label for="created_by" class="form-label">Pembuat Request</label>
                            <select name="requested_by" id="requested_by" class="form-select" required>
                                <option value="">Pilih Pembuat Request</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $materialUsageRequest->requested_by == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Yang Memproses -->
                        <div class="col-12 col-md-6">
                            <label for="processed_by" class="form-label">Yang Memproses / Menyetujui</label>
                            <select name="processed_by" id="processed_by" class="form-select" required>
                                <option value="">Pilih Yang Memproses</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $materialUsageRequest->processed_by == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal Permintaan -->
                        <div class="col-12 col-md-6">
                            <label for="tanggal_permintaan" class="form-label">Tanggal Permintaan</label>
                            <input type="date" name="tanggal_permintaan" id="tanggal_permintaan" class="form-control"
                                value="{{ \Carbon\Carbon::parse($materialUsageRequest->tanggal_permintaan)->format('Y-m-d') }}" required>
                        </div>

                        <!-- Status -->
                        <div class="col-12 col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="Pending" {{ $materialUsageRequest->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Disetujui" {{ $materialUsageRequest->status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="Ditolak" {{ $materialUsageRequest->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <!-- Produksi ID (Readonly) -->
                        <div class="col-12 col-md-6">
                            <label for="produksi_id_display" class="form-label">ID Produksi</label>
                            <input type="text" id="produksi_id_display" class="form-control" value="{{ $materialUsageRequest->produksi_id }}" readonly>
                            <input type="hidden" name="produksi_id" value="{{ $materialUsageRequest->produksi_id }}">
                        </div>

                        <!-- Catatan Produksi -->
                        <div class="col-12">
                            <label for="catatan_produksi" class="form-label">Catatan Produksi</label>
                            <textarea readonly name="catatan_produksi" id="catatan_produksi" class="form-control" rows="3">{{ $materialUsageRequest->catatan_produksi }}</textarea>
                        </div>

                        <!-- Catatan Gudang -->
                        <div class="col-12">
                            <label for="catatan_gudang" class="form-label">Catatan Gudang</label>
                            <textarea name="catatan_gudang" id="catatan_gudang" class="form-control" rows="3">{{ $materialUsageRequest->catatan_gudang }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Detail Bahan Baku -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Detail Penggunaan Material</h5>
                    <div id="detail-bahan-container">
                        @foreach($materialUsageRequest->details as $index => $detail)
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Material</label>
                                    <select name="bahan_baku[{{ $index }}][id]" class="form-select bahan-baku-select" required>
                                        <option value="">Pilih Material</option>
                                        @foreach($bahanBaku as $bahan)
                                            <option value="{{ $bahan->id }}" 
                                                {{ $detail->bahan_baku_id == $bahan->id ? 'selected' : '' }}>
                                                {{ $bahan->nama }} (Stok: {{ $bahan->stok }} {{ $bahan->satuan }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Jumlah Diminta</label>
                                    <input type="number" name="bahan_baku[{{ $index }}][jumlah_diminta]" 
                                        class="form-control" min="1" value="{{ $detail->jumlah_diminta }}" readonly>
                                    <input type="hidden" name="bahan_baku[{{ $index }}][jumlah_diminta]" value="{{ $detail->jumlah_diminta }}">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Jumlah Disetujui</label>
                                    <input type="number" name="bahan_baku[{{ $index }}][jumlah_disetujui]" 
                                           class="form-control" min="0" value="{{ $detail->jumlah_disetujui }}" required>
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBaris(this)">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahBaris()">Tambah Material</button>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <!-- JavaScript untuk Menambah dan Menghapus Baris -->
    <script>
        let barisIndex = {{ count($materialUsageRequest->details) }};

        function tambahBaris() {
            const container = document.getElementById('detail-bahan-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3');
            newRow.innerHTML = `
                <div class="col-12 col-md-4">
                    <select name="bahan_baku[${barisIndex}][id]" class="form-select bahan-baku-select" required>
                        <option value="">Pilih Material</option>
                        @foreach($bahanBaku as $bahan)
                            <option value="{{ $bahan->id }}">
                                {{ $bahan->nama }} (Stok: {{ $bahan->stok }} {{ $bahan->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <input type="number" name="bahan_baku[${barisIndex}][jumlah_diminta]" 
                           class="form-control" min="1" value="0" readonly>
                </div>
                <div class="col-12 col-md-3">
                    <input type="number" name="bahan_baku[${barisIndex}][jumlah_disetujui]" 
                           class="form-control" min="0" required>
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBaris(this)">Hapus</button>
                </div>
            `;
            container.appendChild(newRow);
            barisIndex++;
        }

        function hapusBaris(button) {
            const row = button.closest('.row');
            row.remove();
        }
    </script>
@endsection
