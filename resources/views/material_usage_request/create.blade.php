@extends('layouts.app')

@section('title', 'Buat Material Usage Request')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Buat Material Usage Request</h4>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Buat Material Usage Request -->
        <form action="{{ route('material_usage_request.store') }}" method="POST">
            @csrf
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Produksi -->
                        <div class="col-12 col-md-6">
                            <label for="produksi_id" class="form-label">Produksi</label>
                            <select name="produksi_id" id="produksi_id" class="form-select" required>
                                <option value="">Pilih Produksi</option>
                                @foreach($produksiList as $produksi)
                                    <option value="{{ $produksi->id }}">
                                        {{ $produksi->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pembuat Request -->
                        <div class="col-12 col-md-6">
                            <label for="requested_by" class="form-label">Pembuat Request</label>
                            <select name="requested_by" id="requested_by" class="form-select" required>
                                <option value="">Pilih Pembuat Request</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ Auth::id() == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal Permintaan -->
                        <div class="col-12 col-md-6">
                            <label for="tanggal_permintaan" class="form-label">Tanggal Permintaan</label>
                            <input type="date" name="tanggal_permintaan" id="tanggal_permintaan" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Catatan Produksi -->
                        <div class="col-12">
                            <label for="catatan_produksi" class="form-label">Catatan Produksi</label>
                            <textarea name="catatan_produksi" id="catatan_produksi" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Detail Material -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Detail Material</h5>
                    <div id="detail-bahan-container">
                        <!-- Baris pertama -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label">Material</label>
                                <select name="bahan_baku[0][id]" class="form-select bahan-baku-select" required>
                                    <option value="">Pilih Material</option>
                                    @foreach($bahanBaku as $bahan)
                                        <option value="{{ $bahan->id }}">
                                            {{ $bahan->nama }} (Stok: {{ $bahan->stok }} {{ $bahan->satuan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-5">
                                <label class="form-label">Jumlah Diminta</label>
                                <input type="number" name="bahan_baku[0][jumlah_diminta]" class="form-control jumlah" min="1" required>
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBaris(this)">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahBaris()">Tambah Material</button>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>

    <!-- JavaScript untuk Menambah dan Menghapus Baris -->
    <script>
        let barisIndex = 1;

        function tambahBaris() {
            const container = document.getElementById('detail-bahan-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3');
            newRow.innerHTML = `
                <div class="col-12 col-md-5">
                    <select name="bahan_baku[${barisIndex}][id]" class="form-select bahan-baku-select" required>
                        <option value="">Pilih Material</option>
                        @foreach($bahanBaku as $bahan)
                            <option value="{{ $bahan->id }}">
                                {{ $bahan->nama }} (Stok: {{ $bahan->stok }} {{ $bahan->satuan }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <input type="number" name="bahan_baku[${barisIndex}][jumlah_diminta]" class="form-control jumlah" min="1" required>
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