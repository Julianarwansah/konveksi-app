@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Tambah Produk</h4>

        <!-- Form Tambah Produk -->
        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Nama Produk -->
                        <div class="col-12">
                            <label for="nama" class="form-label">Nama Produk</label>
                            <input type="text" name="nama" id="nama" class="form-control" required>
                        </div>

                        <!-- Kategori -->
                        <div class="col-12">
                            <label for="kategori" class="form-label">Kategori</label>
                            <input type="text" name="kategori" id="kategori" class="form-control" required>
                        </div>

                        <!-- Informasi Harga -->
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Total Harga Bahan</label>
                                    <input type="text" id="total-harga-bahan" class="form-control" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Markup (50%)</label>
                                    <input type="text" id="markup" class="form-control" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="harga" class="form-label">Harga Produk (Auto)</label>
                                    <input type="number" name="harga" id="harga" class="form-control" readonly required>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control"></textarea>
                        </div>

                        <!-- Gambar Utama -->
                        <div class="col-12">
                            <label for="img" class="form-label">Gambar Utama</label>
                            <input type="file" name="img" id="img" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Bahan Produk -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Bahan Produk</h5>
                    <div id="bahan-container">
                        <!-- Baris pertama -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label">Bahan</label>
                                <select name="bahan[0][bahan_id]" class="form-select bahan-select" required>
                                    <option value="">Pilih Bahan</option>
                                    @foreach($bahans as $bahan)
                                        <option value="{{ $bahan->id }}" data-harga="{{ $bahan->harga }}">{{ $bahan->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="bahan[0][jumlah]" class="form-control jumlah" step="0.01" min="0.01" required>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label">Harga Satuan</label>
                                <input type="text" class="form-control harga-satuan" readonly>
                                <input type="hidden" class="harga-satuan-hidden">
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control subtotal" readonly>
                                <input type="hidden" class="subtotal-hidden">
                            </div>
                            <div class="col-12 col-md-2">
                                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBahanBaris(this)">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahBahanBaris()">Tambah Bahan</button>
                </div>
            </div>

            <!-- Form Warna dan Ukuran Produk -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Varian Produk (Warna dan Ukuran)</h5>
                    <div id="warna-container">
                        <!-- Baris pertama -->
                        <div class="warna-row mb-3">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Warna</label>
                                    <input type="text" name="warna[0][warna]" class="form-control" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div id="ukuran-container-0">
                                        <!-- Baris pertama ukuran -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-4">
                                                <label class="form-label">Ukuran</label>
                                                <input type="text" name="warna[0][ukuran][0][ukuran]" class="form-control" required>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label class="form-label">Stok</label>
                                                <input type="number" name="warna[0][ukuran][0][stok]" class="form-control stok-ukuran" min="0" required>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusUkuranBaris(this)">Hapus</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahUkuranBaris(0)">Tambah Ukuran</button>
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusWarnaBaris(this)">Hapus Warna</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahWarnaBaris()">Tambah Warna</button>
                </div>
            </div>

            <!-- Form Gambar Detail -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Gambar Detail</h5>
                    <div id="gambar-detail-container">
                        <!-- Baris pertama -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-10">
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
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>
        </form>
    </div>

    <!-- JavaScript untuk Menambah dan Menghapus Baris -->
    <script>
        let bahanIndex = 1;
        let gambarIndex = 1;
        let warnaIndex = 1;
        let ukuranIndex = [1]; // Array untuk menyimpan indeks ukuran per warna

        // Fungsi untuk menambah baris bahan
        function tambahBahanBaris() {
            const container = document.getElementById('bahan-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3');
            newRow.innerHTML = `
                <div class="col-12 col-md-5">
                    <select name="bahan[${bahanIndex}][bahan_id]" class="form-select bahan-select" required>
                        <option value="">Pilih Bahan</option>
                        @foreach($bahans as $bahan)
                            <option value="{{ $bahan->id }}" data-harga="{{ $bahan->harga }}">{{ $bahan->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <input type="number" name="bahan[${bahanIndex}][jumlah]" class="form-control jumlah" step="0.01" min="0.01" required>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Harga Satuan</label>
                    <input type="text" class="form-control harga-satuan" readonly>
                    <input type="hidden" class="harga-satuan-hidden">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control subtotal" readonly>
                    <input type="hidden" class="subtotal-hidden">
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBahanBaris(this)">Hapus</button>
                </div>
            `;
            container.appendChild(newRow);
            
            // Tambahkan event listener untuk bahan baru
            const bahanSelect = newRow.querySelector('.bahan-select');
            const jumlahInput = newRow.querySelector('.jumlah');
            const hargaSatuanInput = newRow.querySelector('.harga-satuan');
            const hargaSatuanHidden = newRow.querySelector('.harga-satuan-hidden');
            const subtotalInput = newRow.querySelector('.subtotal');
            const subtotalHidden = newRow.querySelector('.subtotal-hidden');
            
            bahanSelect.addEventListener('change', function() {
                const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                hargaSatuanInput.value = formatRupiah(harga);
                hargaSatuanHidden.value = harga;
                hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden);
                hitungTotalHarga();
            });
            
            jumlahInput.addEventListener('input', function() {
                hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden);
                hitungTotalHarga();
            });
            
            bahanIndex++;
        }

        // Fungsi untuk menghitung subtotal
        function hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden) {
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const hargaSatuan = parseFloat(hargaSatuanHidden.value) || 0;
            const subtotal = jumlah * hargaSatuan;
            subtotalInput.value = formatRupiah(subtotal);
            subtotalHidden.value = subtotal;
        }

        // Fungsi untuk menghitung total harga bahan, markup, dan harga produk
        function hitungTotalHarga() {
            const subtotalHiddenInputs = document.querySelectorAll('.subtotal-hidden');
            let totalHargaBahan = 0;

            subtotalHiddenInputs.forEach(input => {
                totalHargaBahan += parseFloat(input.value) || 0;
            });

            // Hitung markup 50%
            const markup = totalHargaBahan * 0.5;

            // Hitung harga produk dengan markup 50%
            const hargaProduk = totalHargaBahan + markup;

            // Tampilkan total harga bahan, markup, dan harga produk
            document.getElementById('total-harga-bahan').value = formatRupiah(totalHargaBahan);
            document.getElementById('markup').value = formatRupiah(markup);
            document.getElementById('harga').value = hargaProduk;
        }

        // Fungsi untuk menghapus baris bahan
        function hapusBahanBaris(button) {
            const row = button.closest('.row');
            row.remove();
            hitungTotalHarga();
        }

        // Fungsi untuk menambah baris warna
        function tambahWarnaBaris() {
            const container = document.getElementById('warna-container');
            const newRow = document.createElement('div');
            newRow.classList.add('warna-row', 'mb-3');
            newRow.innerHTML = `
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Warna</label>
                        <input type="text" name="warna[${warnaIndex}][warna]" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <div id="ukuran-container-${warnaIndex}">
                            <!-- Baris pertama ukuran -->
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Ukuran</label>
                                    <input type="text" name="warna[${warnaIndex}][ukuran][0][ukuran]" class="form-control" required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Stok</label>
                                    <input type="number" name="warna[${warnaIndex}][ukuran][0][stok]" class="form-control stok-ukuran" min="0" required>
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusUkuranBaris(this)">Hapus</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahUkuranBaris(${warnaIndex})">Tambah Ukuran</button>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusWarnaBaris(this)">Hapus Warna</button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            ukuranIndex.push(1); // Tambahkan indeks ukuran baru
            warnaIndex++;
        }

        // Fungsi untuk menghapus baris warna
        function hapusWarnaBaris(button) {
            const warnaRow = button.closest('.warna-row');
            warnaRow.remove();
        }

        // Fungsi untuk menambah baris ukuran
        function tambahUkuranBaris(warnaIdx) {
            const container = document.getElementById(`ukuran-container-${warnaIdx}`);
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3');
            newRow.innerHTML = `
                <div class="col-12 col-md-4">
                    <input type="text" name="warna[${warnaIdx}][ukuran][${ukuranIndex[warnaIdx]}][ukuran]" class="form-control" required>
                </div>
                <div class="col-12 col-md-4">
                    <input type="number" name="warna[${warnaIdx}][ukuran][${ukuranIndex[warnaIdx]}][stok]" class="form-control stok-ukuran" min="0" required>
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusUkuranBaris(this)">Hapus</button>
                </div>
            `;
            container.appendChild(newRow);
            ukuranIndex[warnaIdx]++;
        }

        // Fungsi untuk menghapus baris ukuran
        function hapusUkuranBaris(button) {
            const row = button.closest('.row');
            row.remove();
        }

        // Fungsi untuk menambah baris gambar
        function tambahGambarBaris() {
            const container = document.getElementById('gambar-detail-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3');
            newRow.innerHTML = `
                <div class="col-12 col-md-10">
                    <input type="file" name="gambar[]" class="form-control" accept="image/*">
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusGambarBaris(this)">Hapus</button>
                </div>
            `;
            container.appendChild(newRow);
            gambarIndex++;
        }

        // Fungsi untuk menghapus baris gambar
        function hapusGambarBaris(button) {
            const row = button.closest('.row');
            row.remove();
        }

        // Fungsi format rupiah
        function formatRupiah(angka) {
            if (!angka) return 'Rp 0';
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Inisialisasi event listener untuk baris pertama bahan
        document.addEventListener('DOMContentLoaded', function() {
            const bahanSelect = document.querySelector('.bahan-select');
            const jumlahInput = document.querySelector('.jumlah');
            const hargaSatuanInput = document.querySelector('.harga-satuan');
            const hargaSatuanHidden = document.querySelector('.harga-satuan-hidden');
            const subtotalInput = document.querySelector('.subtotal');
            const subtotalHidden = document.querySelector('.subtotal-hidden');
            
            if (bahanSelect && jumlahInput && hargaSatuanInput) {
                bahanSelect.addEventListener('change', function() {
                    const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                    hargaSatuanInput.value = formatRupiah(harga);
                    hargaSatuanHidden.value = harga;
                    hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden);
                    hitungTotalHarga();
                });

                jumlahInput.addEventListener('input', function() {
                    hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden);
                    hitungTotalHarga();
                });
            }
            
            // Tambahkan event listener untuk semua input stok ukuran
            document.querySelectorAll('.stok-ukuran').forEach(input => {
                input.addEventListener('input', function() {
                    // Anda bisa menambahkan logika perhitungan stok total di sini jika diperlukan
                });
            });
        });
    </script>
@endsection