@extends('layouts.app')

@section('title', 'Tambah Custom Produk')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Tambah Custom Produk</h4>

        <!-- Form Tambah Custom Produk -->
        <form action="{{ route('custom.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Customer -->
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-select" required>
                                <option value="">Pilih Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Template -->
                        <div class="col-md-6">
                            <label for="template_id" class="form-label">Template (Opsional)</label>
                            <select name="template_id" id="template_id" class="form-select">
                                <option value="">Pilih Template</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Model -->
                        <div class="col-md-6">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" name="model" id="model" class="form-control" required>
                        </div>

                        <!-- Jumlah -->
                        <div class="col-md-6">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required>
                        </div>

                        <!-- Ukuran -->
                        <div class="col-md-6">
                            <label for="ukuran" class="form-label">Ukuran</label>
                            <input type="text" name="ukuran" id="ukuran" class="form-control" required>
                        </div>

                        <!-- Warna -->
                        <div class="col-md-6">
                            <label for="warna" class="form-label">Warna</label>
                            <input type="text" name="warna" id="warna" class="form-control" required>
                        </div>

                        <!-- Harga Estimasi -->
                        <div class="col-md-6">
                            <label for="harga_estimasi" class="form-label">Harga Estimasi</label>
                            <input type="number" name="harga_estimasi" id="harga_estimasi" class="form-control" min="0" required>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                @foreach($statusList as $status)
                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estimasi Selesai -->
                        <div class="col-md-6">
                            <label for="estimasi_selesai" class="form-label">Estimasi Selesai</label>
                            <input type="date" name="estimasi_selesai" id="estimasi_selesai" class="form-control" required>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="col-md-6">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai (Opsional)</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control">
                        </div>

                        <!-- Catatan -->
                        <div class="col-12">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3"></textarea>
                        </div>

                        <!-- Gambar -->
                        <div class="col-12">
                            <label for="img" class="form-label">Gambar Desain</label>
                            <input type="file" name="img" id="img" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Bahan yang Digunakan -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Bahan yang Digunakan</h5>
                    <div id="bahan-container">
                        <!-- Baris pertama bahan -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Bahan</label>
                                <select name="bahan[0][bahan_id]" class="form-select bahan-select" required>
                                    <option value="">Pilih Bahan</option>
                                    @foreach($bahans as $bahan)
                                        <option value="{{ $bahan->id }}" data-harga="{{ $bahan->harga }}">{{ $bahan->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="bahan[0][jumlah]" class="form-control jumlah" step="0.01" min="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Harga Satuan</label>
                                <input type="text" class="form-control harga-satuan" readonly>
                                <input type="hidden" name="bahan[0][harga]" class="harga-satuan-hidden">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control subtotal" readonly>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBahanBaris(this)">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahBahanBaris()">Tambah Bahan</button>
                </div>
            </div>

            <!-- Form Biaya Tambahan -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Biaya Tambahan</h5>
                    <div id="biaya-container">
                        <!-- Baris pertama biaya -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Jenis Biaya</label>
                                <select name="biaya[0][biaya_id]" class="form-select biaya-select" required>
                                    <option value="">Pilih Biaya</option>
                                    @foreach($biayas as $biaya)
                                        <option value="{{ $biaya->id }}" data-harga="{{ $biaya->harga }}">{{ $biaya->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" name="biaya[0][jumlah]" class="form-control jumlah-biaya" step="0.01" min="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Harga Satuan</label>
                                <input type="text" class="form-control harga-satuan-biaya" readonly>
                                <input type="hidden" class="harga-satuan-biaya-hidden">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control subtotal-biaya" readonly>
                                <input type="hidden" name="biaya[0][subtotal]" class="subtotal-biaya-hidden">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBiayaBaris(this)">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahBiayaBaris()">Tambah Biaya</button>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Simpan Custom Produk</button>
            </div>
        </form>
    </div>

    <!-- JavaScript untuk Menambah dan Menghapus Baris -->
    <script>
        let bahanIndex = 1;
        let biayaIndex = 1;

        // Fungsi untuk menambah baris bahan
        function tambahBahanBaris() {
            const container = document.getElementById('bahan-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3');
            newRow.innerHTML = `
                <div class="col-md-5">
                    <select name="bahan[${bahanIndex}][bahan_id]" class="form-select bahan-select" required>
                        <option value="">Pilih Bahan</option>
                        @foreach($bahans as $bahan)
                            <option value="{{ $bahan->id }}" data-harga="{{ $bahan->harga }}">{{ $bahan->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="bahan[${bahanIndex}][jumlah]" class="form-control jumlah" step="0.01" min="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Harga Satuan</label>
                    <input type="text" class="form-control harga-satuan" readonly>
                    <input type="hidden" name="bahan[${bahanIndex}][harga]" class="harga-satuan-hidden">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control subtotal" readonly>
                </div>
                <div class="col-md-2">
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
            
            bahanSelect.addEventListener('change', function() {
                const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                hargaSatuanInput.value = formatRupiah(harga);
                hargaSatuanHidden.value = harga;
                hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput);
            });
            
            jumlahInput.addEventListener('input', function() {
                hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput);
            });
            
            bahanIndex++;
        }

        // Fungsi untuk menambah baris biaya
        function tambahBiayaBaris() {
            const container = document.getElementById('biaya-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3');
            newRow.innerHTML = `
                <div class="col-md-5">
                    <select name="biaya[${biayaIndex}][biaya_id]" class="form-select biaya-select" required>
                        <option value="">Pilih Biaya</option>
                        @foreach($biayas as $biaya)
                            <option value="{{ $biaya->id }}" data-harga="{{ $biaya->harga }}">{{ $biaya->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="biaya[${biayaIndex}][jumlah]" class="form-control jumlah-biaya" step="0.01" min="0.01" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Harga Satuan</label>
                    <input type="text" class="form-control harga-satuan-biaya" readonly>
                    <input type="hidden" class="harga-satuan-biaya-hidden">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Subtotal</label>
                    <input type="text" class="form-control subtotal-biaya" readonly>
                    <input type="hidden" name="biaya[${biayaIndex}][subtotal]" class="subtotal-biaya-hidden">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBiayaBaris(this)">Hapus</button>
                </div>
            `;
            container.appendChild(newRow);
            
            // Tambahkan event listener untuk biaya baru
            const biayaSelect = newRow.querySelector('.biaya-select');
            const jumlahInput = newRow.querySelector('.jumlah-biaya');
            const hargaSatuanInput = newRow.querySelector('.harga-satuan-biaya');
            const hargaSatuanHidden = newRow.querySelector('.harga-satuan-biaya-hidden');
            const subtotalInput = newRow.querySelector('.subtotal-biaya');
            const subtotalHidden = newRow.querySelector('.subtotal-biaya-hidden');
            
            biayaSelect.addEventListener('change', function() {
                const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                hargaSatuanInput.value = formatRupiah(harga);
                hargaSatuanHidden.value = harga;
                hitungSubtotalBiaya(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden);
            });
            
            jumlahInput.addEventListener('input', function() {
                hitungSubtotalBiaya(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden);
            });
            
            biayaIndex++;
        }

        // Fungsi untuk menghitung subtotal bahan
        function hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput) {
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const hargaSatuan = parseFloat(hargaSatuanHidden.value) || 0;
            const subtotal = jumlah * hargaSatuan;
            subtotalInput.value = formatRupiah(subtotal);
        }

        // Fungsi untuk menghitung subtotal biaya
        function hitungSubtotalBiaya(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden) {
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const hargaSatuan = parseFloat(hargaSatuanHidden.value) || 0;
            const subtotal = jumlah * hargaSatuan;
            subtotalInput.value = formatRupiah(subtotal);
            subtotalHidden.value = subtotal;
        }

        // Fungsi untuk menghapus baris bahan
        function hapusBahanBaris(button) {
            const row = button.closest('.row');
            row.remove();
        }

        // Fungsi untuk menghapus baris biaya
        function hapusBiayaBaris(button) {
            const row = button.closest('.row');
            row.remove();
        }

        // Fungsi format rupiah
        function formatRupiah(angka) {
            if (!angka) return 'Rp 0';
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Inisialisasi event listener untuk baris pertama
        document.addEventListener('DOMContentLoaded', function() {
            // Bahan
            const bahanSelect = document.querySelector('.bahan-select');
            const jumlahInput = document.querySelector('.jumlah');
            const hargaSatuanInput = document.querySelector('.harga-satuan');
            const hargaSatuanHidden = document.querySelector('.harga-satuan-hidden');
            const subtotalInput = document.querySelector('.subtotal');
            
            if (bahanSelect && jumlahInput && hargaSatuanInput) {
                bahanSelect.addEventListener('change', function() {
                    const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                    hargaSatuanInput.value = formatRupiah(harga);
                    hargaSatuanHidden.value = harga;
                    hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput);
                });

                jumlahInput.addEventListener('input', function() {
                    hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput);
                });
            }
            
            // Biaya
            const biayaSelect = document.querySelector('.biaya-select');
            const jumlahBiayaInput = document.querySelector('.jumlah-biaya');
            const hargaSatuanBiayaInput = document.querySelector('.harga-satuan-biaya');
            const hargaSatuanBiayaHidden = document.querySelector('.harga-satuan-biaya-hidden');
            const subtotalBiayaInput = document.querySelector('.subtotal-biaya');
            const subtotalBiayaHidden = document.querySelector('.subtotal-biaya-hidden');
            
            if (biayaSelect && jumlahBiayaInput && hargaSatuanBiayaInput) {
                biayaSelect.addEventListener('change', function() {
                    const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                    hargaSatuanBiayaInput.value = formatRupiah(harga);
                    hargaSatuanBiayaHidden.value = harga;
                    hitungSubtotalBiaya(jumlahBiayaInput, hargaSatuanBiayaHidden, subtotalBiayaInput, subtotalBiayaHidden);
                });

                jumlahBiayaInput.addEventListener('input', function() {
                    hitungSubtotalBiaya(jumlahBiayaInput, hargaSatuanBiayaHidden, subtotalBiayaInput, subtotalBiayaHidden);
                });
            }
        });
    </script>
@endsection