@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Produk</h4>

        <!-- Form Edit Produk -->
        <form action="{{ route('produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Nama Produk -->
                        <div class="col-12">
                            <label for="nama" class="form-label">Nama Produk</label>
                            <input type="text" name="nama" id="nama" class="form-control" value="{{ $produk->nama }}" required>
                        </div>

                        <!-- Kategori -->
                        <div class="col-12">
                            <label for="kategori" class="form-label">Kategori</label>
                            <input type="text" name="kategori" id="kategori" class="form-control" value="{{ $produk->kategori }}" required>
                        </div>

                        <!-- Custom Produk -->
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_custom" id="is_custom" value="1" {{ $produk->is_custom ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_custom">
                                    Produk Custom
                                </label>
                            </div>
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
                                    <input type="number" name="harga" id="harga" class="form-control" value="{{ $produk->harga }}" readonly required>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-12">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control">{{ $produk->deskripsi }}</textarea>
                        </div>

                        <!-- Gambar Utama -->
                        <div class="col-12">
                            <label for="img" class="form-label">Gambar Utama</label>
                            <input type="file" name="img" id="img" class="form-control" accept="image/*">
                            @if($produk->img)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $produk->img) }}" alt="Gambar Utama" class="img-thumbnail" width="150">
                                    <input type="hidden" name="img_lama" value="{{ $produk->img }}">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Bahan Produk -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Bahan Produk</h5>
                    <div id="bahan-container">
                        @foreach($produk->produkBahan as $index => $bahan)
                            <div class="row g-3 mb-3 bahan-row">
                                <div class="col-12 col-md-5">
                                    <label class="form-label">Bahan</label>
                                    <select name="bahan[{{ $index }}][bahan_id]" class="form-select bahan-select" required>
                                        <option value="">Pilih Bahan</option>
                                        @foreach($bahans as $item)
                                            <option value="{{ $item->id }}" data-harga="{{ $item->harga }}" {{ $bahan->bahan_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="bahan[{{ $index }}][jumlah]" class="form-control jumlah" 
                                           value="{{ $bahan->jumlah }}" step="0.01" min="0.01" required>
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">Harga Satuan</label>
                                    <input type="text" class="form-control harga-satuan" readonly>
                                    <input type="hidden" class="harga-satuan-hidden" value="{{ $bahan->bahan->harga ?? 0 }}">
                                </div>
                                <div class="col-12 col-md-2">
                                    <label class="form-label">Subtotal</label>
                                    <input type="text" class="form-control subtotal" readonly>
                                    <input type="hidden" class="subtotal-hidden">
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusBahanBaris(this)">Hapus</button>
                                </div>
                                <input type="hidden" name="bahan[{{ $index }}][id]" value="{{ $bahan->id }}">
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahBahanBaris()">Tambah Bahan</button>
                </div>
            </div>

            <!-- Form Warna dan Ukuran Produk -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Warna dan Ukuran Produk</h5>
                    <div id="warna-container">
                        @foreach($produk->warna as $warnaIndex => $warna)
                            <div class="warna-row mb-3 border p-3 rounded">
                                <div class="row g-3 align-items-end">
                                    <!-- Input Warna -->
                                    <div class="col-12 col-md-3">
                                        <label class="form-label">Warna</label>
                                        <input type="text" name="warna[{{ $warnaIndex }}][warna]" class="form-control warna-input" 
                                               value="{{ $warna->warna }}" required>
                                        <input type="hidden" name="warna[{{ $warnaIndex }}][id]" value="{{ $warna->id }}">
                                    </div>

                                    <!-- Daftar Ukuran -->
                                    <div class="col-12 col-md-7">
                                        <div class="ukuran-container">
                                            @foreach($warna->ukuran as $ukuranIndex => $ukuran)
                                                <div class="row g-3 mb-3 ukuran-row">
                                                    <div class="col-12 col-md-4">
                                                        <label class="form-label">Ukuran</label>
                                                        <input type="text" name="warna[{{ $warnaIndex }}][ukuran][{{ $ukuranIndex }}][ukuran]" 
                                                               class="form-control" value="{{ $ukuran->ukuran }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <label class="form-label">Stok</label>
                                                        <input type="number" name="warna[{{ $warnaIndex }}][ukuran][{{ $ukuranIndex }}][stok]" 
                                                               class="form-control stok-ukuran" value="{{ $ukuran->stok }}" min="0" required>
                                                    </div>
                                                    <div class="col-12 col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="hapusUkuranBaris(this)">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                    <input type="hidden" name="warna[{{ $warnaIndex }}][ukuran][{{ $ukuranIndex }}][id]" 
                                                           value="{{ $ukuran->id }}">
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="tambahUkuranBaris(this)">
                                            <i class="fas fa-plus"></i> Tambah Ukuran
                                        </button>
                                    </div>

                                    <!-- Tombol Hapus Warna -->
                                    <div class="col-12 col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="hapusWarnaBaris(this)">
                                            <i class="fas fa-trash"></i> Hapus Warna
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahWarnaBaris()">
                        <i class="fas fa-plus"></i> Tambah Warna
                    </button>
                </div>
            </div>

            <!-- Form Gambar Detail -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Gambar Detail Produk</h5>
                    <div id="gambar-container">
                        @foreach($produk->gambarDetails as $index => $gambar)
                            <div class="row g-3 mb-3 gambar-row">
                                <div class="col-12 col-md-10">
                                    <label class="form-label">Gambar</label>
                                    <input type="file" name="gambar[]" class="form-control" accept="image/*">
                                    <input type="hidden" name="gambar_lama[]" value="{{ $gambar->gambar }}">
                                    <img src="{{ asset('storage/' . $gambar->gambar) }}" alt="Gambar Detail" class="img-thumbnail mt-2" width="100">
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm mt-4" onclick="hapusGambarBaris(this)">Hapus</button>
                                    <input type="hidden" name="hapus_gambar[]" value="{{ $gambar->id }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-3" onclick="tambahGambarBaris()">Tambah Gambar</button>
                </div>
            </div>

            <!-- Tombol Simpan dan Batal -->
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('produk.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <!-- JavaScript untuk Menambah dan Menghapus Baris -->
    <script>
        let bahanIndex = {{ count($produk->produkBahan) }};
        let warnaIndex = {{ count($produk->warna) }};
        let ukuranIndex = [];
        let gambarIndex = {{ count($produk->gambarDetails) }};

        // Inisialisasi indeks ukuran untuk setiap warna
        @foreach($produk->warna as $warna)
            ukuranIndex.push({{ count($warna->ukuran) }});
        @endforeach

        // Fungsi untuk menambah baris bahan
        function tambahBahanBaris() {
            const container = document.getElementById('bahan-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3', 'bahan-row');
            newRow.innerHTML = `
                <div class="col-12 col-md-5">
                    <label class="form-label">Bahan</label>
                    <select name="bahan[${bahanIndex}][bahan_id]" class="form-select bahan-select" required>
                        <option value="">Pilih Bahan</option>
                        @foreach($bahans as $bahan)
                            <option value="{{ $bahan->id }}" data-harga="{{ $bahan->harga }}">{{ $bahan->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Jumlah</label>
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
            const row = button.closest('.bahan-row');
            row.remove();
            hitungTotalHarga();
        }

        // Fungsi untuk menambah baris warna
        function tambahWarnaBaris() {
            const container = document.getElementById('warna-container');
            const newRow = document.createElement('div');
            newRow.classList.add('warna-row', 'mb-3', 'border', 'p-3', 'rounded');
            newRow.innerHTML = `
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label class="form-label">Warna</label>
                        <input type="text" name="warna[${warnaIndex}][warna]" class="form-control warna-input" required>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="ukuran-container">
                            <div class="row g-3 mb-3 ukuran-row">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Ukuran</label>
                                    <input type="text" name="warna[${warnaIndex}][ukuran][0][ukuran]" class="form-control" required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Stok</label>
                                    <input type="number" name="warna[${warnaIndex}][ukuran][0][stok]" class="form-control stok-ukuran" min="0" required>
                                </div>
                                <div class="col-12 col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="hapusUkuranBaris(this)">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="tambahUkuranBaris(this)">
                            <i class="fas fa-plus"></i> Tambah Ukuran
                        </button>
                    </div>
                    <div class="col-12 col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="hapusWarnaBaris(this)">
                            <i class="fas fa-trash"></i> Hapus Warna
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            ukuranIndex.push(1);
            warnaIndex++;
        }

        // Fungsi untuk menghapus baris warna
        function hapusWarnaBaris(button) {
            const row = button.closest('.warna-row');
            row.remove();
        }

        // Fungsi untuk menambah baris ukuran
        function tambahUkuranBaris(button) {
            const warnaRow = button.closest('.warna-row');
            const warnaIndex = Array.from(document.querySelectorAll('.warna-row')).indexOf(warnaRow);
            const ukuranContainer = warnaRow.querySelector('.ukuran-container');
            
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3', 'ukuran-row');
            newRow.innerHTML = `
                <div class="col-12 col-md-4">
                    <label class="form-label">Ukuran</label>
                    <input type="text" name="warna[${warnaIndex}][ukuran][${ukuranIndex[warnaIndex]}][ukuran]" class="form-control" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Stok</label>
                    <input type="number" name="warna[${warnaIndex}][ukuran][${ukuranIndex[warnaIndex]}][stok]" class="form-control stok-ukuran" min="0" required>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="hapusUkuranBaris(this)">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            `;
            ukuranContainer.insertBefore(newRow, button.parentNode);
            ukuranIndex[warnaIndex]++;
        }

        // Fungsi untuk menghapus baris ukuran
        function hapusUkuranBaris(button) {
            const row = button.closest('.ukuran-row');
            row.remove();
        }

        // Fungsi untuk menambah baris gambar
        function tambahGambarBaris() {
            const container = document.getElementById('gambar-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'mb-3', 'gambar-row');
            newRow.innerHTML = `
                <div class="col-12 col-md-10">
                    <label class="form-label">Gambar</label>
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
            const row = button.closest('.gambar-row');
            row.remove();
        }

        // Fungsi format rupiah
        function formatRupiah(angka) {
            if (!angka) return 'Rp 0';
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Inisialisasi saat dokumen dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi harga untuk bahan yang sudah ada
            document.querySelectorAll('.bahan-select').forEach((select, index) => {
                const hargaSatuanInput = document.querySelectorAll('.harga-satuan')[index];
                const hargaSatuanHidden = document.querySelectorAll('.harga-satuan-hidden')[index];
                const jumlahInput = document.querySelectorAll('.jumlah')[index];
                const subtotalInput = document.querySelectorAll('.subtotal')[index];
                const subtotalHidden = document.querySelectorAll('.subtotal-hidden')[index];
                
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption) {
                    const harga = selectedOption.getAttribute('data-harga');
                    if (harga) {
                        hargaSatuanInput.value = formatRupiah(harga);
                        hargaSatuanHidden.value = harga;
                        hitungSubtotal(jumlahInput, hargaSatuanHidden, subtotalInput, subtotalHidden);
                    }
                }
                
                // Tambahkan event listener
                select.addEventListener('change', function() {
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
            });
            
            // Hitung total harga awal
            hitungTotalHarga();
        });
    </script>
@endsection