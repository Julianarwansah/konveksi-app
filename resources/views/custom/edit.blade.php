@extends('layouts.app')

@section('title', 'Edit Custom Produk')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Custom Produk</h4>

        <!-- Form Edit Custom Produk -->
        <form action="{{ route('custom.update', $custom->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Customer -->
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-select" required>
                                <option value="">Pilih Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $custom->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Template -->
                        <div class="col-md-6">
                            <label for="template_id" class="form-label">Template (Opsional)</label>
                            <select name="template_id" id="template_id" class="form-select">
                                <option value="">Pilih Template</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" {{ $custom->template_id == $template->id ? 'selected' : '' }}>{{ $template->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Model -->
                        <div class="col-md-6">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" name="model" id="model" class="form-control" value="{{ $custom->model }}" required>
                        </div>

                        <!-- Jumlah -->
                        <div class="col-md-6">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="{{ $custom->jumlah }}" required>
                        </div>

                        <!-- Ukuran -->
                        <div class="col-md-6">
                            <label for="ukuran" class="form-label">Ukuran</label>
                            <input type="text" name="ukuran" id="ukuran" class="form-control" value="{{ $custom->ukuran }}" required>
                        </div>

                        <!-- Warna -->
                        <div class="col-md-6">
                            <label for="warna" class="form-label">Warna</label>
                            <input type="text" name="warna" id="warna" class="form-control" value="{{ $custom->warna }}" required>
                        </div>

                        <!-- Harga Estimasi -->
                        <div class="col-md-6">
                            <label for="harga_estimasi" class="form-label">Harga Estimasi</label>
                            <input type="number" name="harga_estimasi" id="harga_estimasi" class="form-control" min="0" value="{{ $custom->harga_estimasi }}" required>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                @foreach($statusList as $status)
                                    <option value="{{ $status }}" {{ $custom->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estimasi Selesai -->
                        <div class="col-md-6">
                            <label for="estimasi_selesai" class="form-label">Estimasi Selesai</label>
                            <input type="date" name="estimasi_selesai" id="estimasi_selesai" class="form-control" value="{{ $custom->estimasi_selesai->format('Y-m-d') }}" required>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="col-md-6">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai (Opsional)</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ $custom->tanggal_mulai ? $custom->tanggal_mulai->format('Y-m-d') : '' }}">
                        </div>

                        <!-- Catatan -->
                        <div class="col-12">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3">{{ $custom->catatan }}</textarea>
                        </div>

                        <!-- Gambar -->
                        <div class="col-12">
                            <label for="img" class="form-label">Gambar Desain</label>
                            <input type="file" name="img" id="img" class="form-control" accept="image/*">
                            @if($custom->img)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $custom->img) }}" alt="Gambar Desain" class="img-thumbnail" width="150">
                                    <input type="hidden" name="img_lama" value="{{ $custom->img }}">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Bahan yang Digunakan -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Bahan yang Digunakan</h5>
                    <div id="bahan-container">
                        @foreach($custom->customDetails as $index => $detail)
                            <div class="row g-3 mb-3 bahan-row">
                                <div class="col-md-5">
                                    <label class="form-label">Bahan</label>
                                    <select name="bahan[{{ $index }}][bahan_id]" class="form-select bahan-select" required>
                                        <option value="">Pilih Bahan</option>
                                        @foreach($bahans as $bahan)
                                            <option value="{{ $bahan->id }}" data-harga="{{ $bahan->harga }}" 
                                                {{ $detail->bahan_id == $bahan->id ? 'selected' : '' }}>
                                                {{ $bahan->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="bahan[{{ $index }}][jumlah]" class="form-control jumlah" 
                                           value="{{ $detail->jumlah }}" step="0.01" min="0.01" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Harga Satuan</label>
                                    <input type="text" class="form-control harga-satuan" value="{{ 'Rp ' . number_format($detail->harga, 0, ',', '.') }}" readonly>
                                    <input type="hidden" name="bahan[{{ $index }}][harga]" class="harga-satuan-hidden" value="{{ $detail->harga }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Subtotal</label>
                                    <input type="text" class="form-control subtotal" value="{{ number_format($detail->sub_total) }}" readonly>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBahanBaris(this)">Hapus</button>
                                </div>
                                <input type="hidden" name="bahan[{{ $index }}][id]" value="{{ $detail->id }}">
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahBahanBaris()">Tambah Bahan</button>
                    <input type="hidden" id="hapus-bahan-input" name="hapus_bahan" value="">
                </div>
            </div>

            <!-- Form Biaya Tambahan -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Biaya Tambahan</h5>
                    <div id="biaya-container">
                        @foreach($custom->customBiayas as $index => $biaya)
                            <div class="row g-3 mb-3 biaya-row">
                                <div class="col-md-5">
                                    <label class="form-label">Jenis Biaya</label>
                                    <select name="biaya[{{ $index }}][biaya_id]" class="form-select biaya-select" required>
                                        <option value="">Pilih Biaya</option>
                                        @foreach($biayas as $item)
                                            <option value="{{ $item->id }}" data-harga="{{ $item->harga }}" 
                                                {{ $biaya->biaya_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="biaya[{{ $index }}][jumlah]" class="form-control jumlah-biaya" 
                                           value="{{ $biaya->jumlah }}" step="0.01" min="0.01" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Harga Satuan</label>
                                    <input type="text" class="form-control harga-satuan-biaya" 
                                           value="{{ number_format($biaya->biayaTambahan->harga ?? 0) }}" readonly>
                                    <input type="hidden" class="harga-satuan-biaya-hidden" value="{{ $biaya->biayaTambahan->harga ?? 0 }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Subtotal</label>
                                    <input type="text" class="form-control subtotal-biaya" value="{{ number_format($biaya->subtotal) }}" readonly>
                                    <input type="hidden" name="biaya[{{ $index }}][subtotal]" class="subtotal-biaya-hidden" value="{{ $biaya->subtotal }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBiayaBaris(this)">Hapus</button>
                                </div>
                                <input type="hidden" name="biaya[{{ $index }}][id]" value="{{ $biaya->id }}">
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahBiayaBaris()">Tambah Biaya</button>
                    <input type="hidden" id="hapus-biaya-input" name="hapus_biaya" value="">
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('custom.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <!-- JavaScript untuk Menambah dan Menghapus Baris -->
    <script>
        let bahanIndex = {{ count($custom->customDetails) }};
        let biayaIndex = {{ count($custom->customBiayas) }};
        let bahanDihapus = [];
        let biayaDihapus = [];

        // Fungsi untuk menambah baris bahan
        function tambahBahanBaris() {
            const container = document.getElementById('bahan-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3', 'bahan-row');
            newRow.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label">Bahan</label>
                    <select name="bahan[${bahanIndex}][bahan_id]" class="form-select bahan-select" required>
                        <option value="">Pilih Bahan</option>
                        @foreach($bahans as $bahan)
                            <option value="{{ $bahan->id }}" data-harga="{{ $bahan->harga }}">{{ $bahan->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah</label>
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
            newRow.classList.add('row', 'g-3', 'mb-3', 'biaya-row');
            newRow.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label">Jenis Biaya</label>
                    <select name="biaya[${biayaIndex}][biaya_id]" class="form-select biaya-select" required>
                        <option value="">Pilih Biaya</option>
                        @foreach($biayas as $biaya)
                            <option value="{{ $biaya->id }}" data-harga="{{ $biaya->harga }}">{{ $biaya->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jumlah</label>
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
            const row = button.closest('.bahan-row');
            const idInput = row.querySelector('input[name*="[id]"]');
            
            if (idInput && idInput.value) {
                bahanDihapus.push(idInput.value);
                document.getElementById('hapus-bahan-input').value = bahanDihapus.join(',');
            }
            
            row.remove();
        }

        // Fungsi untuk menghapus baris biaya
        function hapusBiayaBaris(button) {
            const row = button.closest('.biaya-row');
            const idInput = row.querySelector('input[name*="[id]"]');
            
            if (idInput && idInput.value) {
                biayaDihapus.push(idInput.value);
                document.getElementById('hapus-biaya-input').value = biayaDihapus.join(',');
            }
            
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
            document.querySelectorAll('.bahan-select').forEach((select, index) => {
                const hargaSatuanInput = document.querySelectorAll('.harga-satuan')[index];
                const hargaSatuanHidden = document.querySelectorAll('.harga-satuan-hidden')[index];
                const jumlahInput = document.querySelectorAll('.jumlah')[index];
                const subtotalInput = document.querySelectorAll('.subtotal')[index];
                
                select.addEventListener('change', function() {
                    const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                    hargaSatuanInput.value = formatRupiah(harga);
                    hargaSatuanHidden.value = harga;
                    hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput);
                });
                
                jumlahInput.addEventListener('input', function() {
                    hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput);
                });
            });
            
            // Biaya
            document.querySelectorAll('.biaya-select').forEach((select, index) => {
                const hargaSatuanInput = document.querySelectorAll('.harga-satuan-biaya')[index];
                const hargaSatuanHidden = document.querySelectorAll('.harga-satuan-biaya-hidden')[index];
                const jumlahInput = document.querySelectorAll('.jumlah-biaya')[index];
                const subtotalInput = document.querySelectorAll('.subtotal-biaya')[index];
                const subtotalHidden = document.querySelectorAll('.subtotal-biaya-hidden')[index];
                
                select.addEventListener('change', function() {
                    const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                    hargaSatuanInput.value = formatRupiah(harga);
                    hargaSatuanHidden.value = harga;
                    hitungSubtotalBiaya(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden);
                });
                
                jumlahInput.addEventListener('input', function() {
                    hitungSubtotalBiaya(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden);
                });
            });
        });
    </script>
@endsection