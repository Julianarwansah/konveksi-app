@extends('layouts.app')

@section('title', 'Edit Template')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Template</h4>

        <!-- Form Edit Template -->
        <form action="{{ route('template.update', $template->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Model -->
                        <div class="col-12">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" name="model" id="model" class="form-control" value="{{ $template->model }}" required>
                        </div>

                        <!-- Kategori -->
                        <div class="col-12">
                            <label for="kategori" class="form-label">Kategori</label>
                            <input type="text" name="kategori" id="kategori" class="form-control" value="{{ $template->kategori }}" required>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3">{{ $template->deskripsi }}</textarea>
                        </div>

                        <!-- Harga Estimasi -->
                        <div class="col-12">
                            <label for="harga_estimasi" class="form-label">Harga Estimasi</label>
                            <input type="number" name="harga_estimasi" id="harga_estimasi" class="form-control" value="{{ $template->harga_estimasi }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Detail Bahan -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Detail Bahan</h5>
                    <div id="detail-bahan-container">
                        <!-- Loop melalui detail bahan yang sudah ada -->
                        @foreach($template->details as $index => $detail)
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Bahan</label>
                                    <select name="details[{{ $index }}][bahan_id]" class="form-select bahan-select" required>
                                        <option value="">Pilih Bahan</option>
                                        @foreach($bahan as $item)
                                            <option value="{{ $item->id }}" data-harga="{{ $item->harga }}" {{ $detail->bahan_id == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="details[{{ $index }}][jumlah]" class="form-control jumlah" value="{{ $detail->jumlah }}" required>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">Harga Satuan</label>
                                    <input type="text" class="form-control harga-satuan" value="{{ $detail->harga }}" readonly>
                                    <input type="hidden" name="details[{{ $index }}][harga]" class="harga-satuan-hidden" value="{{ $detail->harga }}">
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">Subtotal</label>
                                    <input type="text" class="form-control subtotal" value="{{ $detail->subtotal }}" readonly>
                                    <input type="hidden" name="details[{{ $index }}][subtotal]" class="subtotal-hidden" value="{{ $detail->subtotal }}">
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBaris(this)">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahBaris()">Tambah Bahan</button>
                </div>
            </div>

            <!-- Form Warna -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Warna</h5>
                    <div id="warna-container">
                        <!-- Loop melalui warna yang sudah ada -->
                        @foreach($template->warna as $index => $warna)
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Warna</label>
                                    <input type="text" name="warna[{{ $index }}]" class="form-control" value="{{ $warna->warna }}">
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusWarnaBaris(this)">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahWarnaBaris()">Tambah Warna</button>
                </div>
            </div>

            <!-- Form Gambar -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Gambar</h5>
                    <div id="gambar-container">
                        <!-- Loop melalui gambar yang sudah ada -->
                        @foreach($template->gambar as $index => $gambar)
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Gambar</label>
                                    <input type="file" name="gambar[{{ $index }}]" class="form-control" accept="image/*">
                                    <!-- Tampilkan gambar yang sudah ada -->
                                    @if($gambar->gambar)
                                        <img src="{{ asset('storage/' . $gambar->gambar) }}" alt="Gambar Template" width="100" class="mt-2">
                                        <input type="hidden" name="gambar_lama[{{ $index }}]" value="{{ $gambar->gambar }}">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="hapus_gambar[]" id="hapus_gambar_{{ $index }}" value="{{ $gambar->id }}">
                                            <label class="form-check-label" for="hapus_gambar_{{ $index }}">Hapus gambar ini</label>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusGambarBaris(this)">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahGambarBaris()">Tambah Gambar</button>
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
        let barisIndex = {{ count($template->details) }};
        let gambarBarisIndex = {{ count($template->gambar) }};
        let warnaBarisIndex = {{ count($template->warna) }};

        // Fungsi untuk menambah baris bahan
        function tambahBaris() {
            const container = document.getElementById('detail-bahan-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3');
            newRow.innerHTML = `
                <div class="col-12 col-md-4">
                    <select name="details[${barisIndex}][bahan_id]" class="form-select bahan-select" required>
                        <option value="">Pilih Bahan</option>
                        @foreach($bahan as $item)
                            <option value="{{ $item->id }}" data-harga="{{ $item->harga }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <input type="number" name="details[${barisIndex}][jumlah]" class="form-control jumlah" required>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Harga Satuan</label>
                    <input type="text" class="form-control harga-satuan" readonly>
                    <input type="hidden" name="details[${barisIndex}][harga_satuan]" class="harga-satuan-hidden">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control subtotal" readonly>
                    <input type="hidden" name="details[${barisIndex}][subtotal]" class="subtotal-hidden">
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBaris(this)">Hapus</button>
                </div>
            `;
            container.appendChild(newRow);
            barisIndex++;

            // Setup event listeners untuk baris baru
            setupBarisEventListeners(newRow);
        }

        // Fungsi untuk menghapus baris bahan
        function hapusBaris(button) {
            const row = button.closest('.row');
            row.remove();
        }

        // Fungsi untuk menambah baris gambar
        function tambahGambarBaris() {
            const container = document.getElementById('gambar-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3');
            newRow.innerHTML = `
                <div class="col-12 col-md-6">
                    <input type="file" name="gambar[]" class="form-control" accept="image/*">
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusGambarBaris(this)">Hapus</button>
                </div>
            `;
            container.appendChild(newRow);
            gambarBarisIndex++;
        }

        // Fungsi untuk menghapus baris gambar
        function hapusGambarBaris(button) {
            const row = button.closest('.row');
            row.remove();
        }

        // Fungsi untuk menambah baris warna
        function tambahWarnaBaris() {
            const container = document.getElementById('warna-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3');
            newRow.innerHTML = `
                <div class="col-12 col-md-6">
                    <input type="text" name="warna[]" class="form-control">
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusWarnaBaris(this)">Hapus</button>
                </div>
            `;
            container.appendChild(newRow);
            warnaBarisIndex++;
        }

        // Fungsi untuk menghapus baris warna
        function hapusWarnaBaris(button) {
            const row = button.closest('.row');
            row.remove();
        }

        // Fungsi untuk setup event listeners pada baris bahan
        function setupBarisEventListeners(row) {
            const bahanSelect = row.querySelector('.bahan-select');
            const jumlahInput = row.querySelector('.jumlah');
            const hargaSatuanInput = row.querySelector('.harga-satuan');
            const subtotalInput = row.querySelector('.subtotal');
            const hargaHidden = row.querySelector('.harga-satuan-hidden'); // diubah dari hargaSatuanHidden
            const subtotalHidden = row.querySelector('.subtotal-hidden');

            bahanSelect.addEventListener('change', function() {
                const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                hargaSatuanInput.value = harga;
                hargaHidden.value = harga; // diubah untuk sesuai dengan nama field di controller
                hitungSubtotal(jumlahInput, hargaSatuanInput, subtotalInput, subtotalHidden);
            });

            jumlahInput.addEventListener('input', function() {
                hitungSubtotal(jumlahInput, hargaSatuanInput, subtotalInput, subtotalHidden);
            });
        }

        // Fungsi untuk menghitung subtotal
        function hitungSubtotal(jumlahInput, hargaSatuanInput, subtotalInput, subtotalHidden) {
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const hargaSatuan = parseFloat(hargaSatuanInput.value) || 0;
            const subtotal = jumlah * hargaSatuan;
            subtotalInput.value = subtotal.toFixed(2);
            subtotalHidden.value = subtotal.toFixed(2);
        }

        // Inisialisasi event listener untuk semua baris bahan saat dokumen dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('#detail-bahan-container .row');
            rows.forEach(row => {
                setupBarisEventListeners(row);
            });
        });
    </script>
@endsection