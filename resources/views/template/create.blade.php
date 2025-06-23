@extends('layouts.app')

@section('title', 'Tambah Template')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Tambah Template</h4>

        <!-- Form Tambah Template -->
        <form action="{{ route('template.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Model -->
                        <div class="col-12">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" name="model" id="model" class="form-control" required>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                        </div>

                        <!-- Kategori -->
                        <div class="col-12">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select name="kategori" id="kategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Baju">Baju</option>
                                <option value="Celana">Celana</option>
                                <option value="Jaket">Jaket</option>
                                <option value="Dress">Dress</option>
                                <option value="Aksesoris">Aksesoris</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <!-- Harga Estimasi -->
                        <div class="col-12">
                            <label for="harga_estimasi" class="form-label">Harga Estimasi</label>
                            <input type="number" name="harga_estimasi" id="harga_estimasi" class="form-control" readonly>
                            <small class="text-muted">*Harga estimasi dihitung otomatis dari total bahan baku + markup 80%</small>
                            <div id="perhitungan-harga" class="mt-2 p-2 bg-light rounded d-none">
                                <p class="mb-1">Perhitungan Harga:</p>
                                <p class="mb-1">Total Harga Bahan: Rp <span id="total-harga-bahan">0</span></p>
                                <p class="mb-1">Markup (80%): Rp <span id="markup-harga">0</span></p>
                                <p class="mb-0 fw-bold">Harga Estimasi: Rp <span id="harga-estimasi-display">0</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Detail Bahan -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Detail Bahan</h5>
                    <div id="detail-bahan-container">
                        <!-- Baris pertama -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">Bahan</label>
                                <select name="details[0][bahan_id]" class="form-select bahan-select" required>
                                    <option value="">Pilih Bahan</option>
                                    @foreach($bahan as $item)
                                        <option value="{{ $item->id }}" data-harga="{{ $item->harga }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="details[0][jumlah]" class="form-control jumlah" required>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label">Harga Satuan</label>
                                <input type="text" class="form-control harga-satuan" readonly>
                                <input type="hidden" name="details[0][harga]" class="harga-satuan-hidden">
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control subtotal" readonly>
                                <input type="hidden" name="details[0][subtotal]" class="subtotal-hidden">
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBaris(this)">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahBaris()">Tambah Bahan</button>
                </div>
            </div>

            <!-- Form Warna -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Warna</h5>
                    <div id="warna-container">
                        <!-- Baris pertama -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Warna</label>
                                <input type="text" name="warna[]" class="form-control">
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusWarnaBaris(this)">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahWarnaBaris()">Tambah Warna</button>
                </div>
            </div>

            <!-- Form Gambar -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Gambar</h5>
                    <div id="gambar-container">
                        <!-- Baris pertama -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Gambar</label>
                                <input type="file" name="gambar[]" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusGambarBaris(this)">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahGambarBaris()">Tambah Gambar</button>
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
        let gambarBarisIndex = 1;
        let warnaBarisIndex = 1;
        const markupPersentase = 0.8; // 80% markup

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
                    <input type="hidden" name="details[${barisIndex}][harga]" class="harga-satuan-hidden">
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

            // Tambahkan event listener untuk baris baru
            setupBarisEventListeners(newRow);
        }

        // Fungsi untuk menghapus baris bahan
        function hapusBaris(button) {
            const row = button.closest('.row');
            row.remove();
            hitungTotalHarga(); // Hitung ulang total setelah menghapus baris
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
            const hargaHidden = row.querySelector('.harga-satuan-hidden');
            const subtotalHidden = row.querySelector('.subtotal-hidden');

            bahanSelect.addEventListener('change', function() {
    const harga = this.options[this.selectedIndex].getAttribute('data-harga');
    hargaSatuanInput.value = formatRupiah(harga);
    hargaHidden.value = harga;  // pakai hargaHidden, bukan hargaSatuanHidden
    hitungSubtotal(jumlahInput, hargaSatuanInput, subtotalInput, subtotalHidden);
    hitungTotalHarga();
});


            jumlahInput.addEventListener('input', function() {
                hitungSubtotal(jumlahInput, hargaSatuanInput, subtotalInput, subtotalHidden);
                hitungTotalHarga();
            });
        }

        // Fungsi untuk menghitung subtotal
        function hitungSubtotal(jumlahInput, hargaSatuanInput, subtotalInput, subtotalHidden) {
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const hargaSatuan = parseFloat(hargaSatuanInput.value.replace(/[^0-9]/g, '')) || 0;
            const subtotal = jumlah * hargaSatuan;
            subtotalInput.value = formatRupiah(subtotal.toString());
            subtotalHidden.value = subtotal.toFixed(2);
        }

        // Fungsi untuk menghitung total harga dan harga estimasi
        function hitungTotalHarga() {
            let totalHargaBahan = 0;
            const subtotalInputs = document.querySelectorAll('.subtotal-hidden');
            
            subtotalInputs.forEach(input => {
                totalHargaBahan += parseFloat(input.value) || 0;
            });

            const markupHarga = totalHargaBahan * markupPersentase;
            const hargaEstimasi = totalHargaBahan + markupHarga;

            // Update tampilan
            document.getElementById('total-harga-bahan').textContent = formatRupiah(totalHargaBahan.toString());
            document.getElementById('markup-harga').textContent = formatRupiah(markupHarga.toString());
            document.getElementById('harga-estimasi-display').textContent = formatRupiah(hargaEstimasi.toString());
            
            // Update input harga estimasi
            document.getElementById('harga_estimasi').value = hargaEstimasi.toFixed(2);
            
            // Tampilkan perhitungan jika ada bahan
            const perhitunganDiv = document.getElementById('perhitungan-harga');
            if (totalHargaBahan > 0) {
                perhitunganDiv.classList.remove('d-none');
            } else {
                perhitunganDiv.classList.add('d-none');
            }
        }

        // Fungsi format Rupiah
        function formatRupiah(angka) {
            if (!angka) return '0';
            const number = parseFloat(angka);
            return new Intl.NumberFormat('id-ID', { 
                style: 'currency', 
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number).replace('Rp', 'Rp ');
        }

        // Inisialisasi event listener untuk baris pertama saat dokumen dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const firstRow = document.querySelector('#detail-bahan-container .row');
            if (firstRow) {
                setupBarisEventListeners(firstRow);
            }
        });
    </script>

@endsection