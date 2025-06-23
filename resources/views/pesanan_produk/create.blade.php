@extends('layouts.app')

@section('title', 'Tambah Pesanan Produk')

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Tambah Pesanan Produk</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('pesanan-produk.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validasiPembayaran()">
                    @csrf
                    <!-- Input Customer ID -->
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select class="form-control" id="customer_id" name="customer_id" required>
                            <option value="">Pilih Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Input Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                            <option value="Verif Pembayaran">Verif Pembayaran</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Pesanan Selesai">Pesanan Selesai</option>
                        </select>
                    </div>

                    <!-- Input Metode Pembayaran -->
                    <div class="mb-3">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <select class="form-control" id="metode_pembayaran" name="metode_pembayaran" required>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="COD">COD</option>
                            <option value="Kartu Kredit">Kartu Kredit</option>
                        </select>
                    </div>

                    <!-- Input Total Harga -->
                    <div class="mb-3">
                        <label for="total_harga" class="form-label">Total Harga</label>
                        <input type="number" class="form-control" id="total_harga" name="total_harga" readonly>
                    </div>

                    <!-- Input Pembayaran Manual -->
                    <div class="mb-3">
                        <label for="pembayaran_manual" class="form-label">Pembayaran Manual (Jumlah yang Dibayar)</label>
                        <input type="number" class="form-control" id="pembayaran_manual" name="pembayaran_manual" value="0" min="0">
                        <small class="text-danger">Pembayaran wajib 100% dari total harga untuk produk jadi.</small>
                    </div>

                    <!-- Input Sisa Pembayaran -->
                    <div class="mb-3">
                        <label for="sisa_pembayaran" class="form-label">Sisa Pembayaran</label>
                        <input type="number" class="form-control" id="sisa_pembayaran" name="sisa_pembayaran" value="0" min="0" readonly>
                    </div>

                    <!-- Input Bukti Pembayaran -->
                    <div class="mb-3">
                        <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran (Gambar)</label>
                        <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran">
                    </div>

                    <!-- Input Produk, Ukuran, Jumlah, dan Harga Satuan -->
                    <div id="produk-container">
                        <div class="row mb-3 produk-item">
                            <div class="col-md-3">
                                <label for="produk_id" class="form-label">Produk</label>
                                <select class="form-control produk-select" name="produk_id[]" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produk as $item)
                                        <option value="{{ $item->id }}" data-harga="{{ $item->harga }}">
                                            {{ $item->nama }} (Rp {{ number_format($item->harga, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="warna" class="form-label">Warna</label>
                                <select class="form-control warna-select" name="warna[]" required>
                                    <option value="">Pilih Warna</option>
                                    <!-- Opsi warna akan diisi secara dinamis menggunakan JavaScript -->
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="ukuran" class="form-label">Ukuran</label>
                                <select class="form-control ukuran-select" name="ukuran[]" required>
                                    <option value="">Pilih Ukuran</option>
                                    <!-- Opsi ukuran akan diisi secara dinamis menggunakan JavaScript -->
                                </select>
                            </div>
                            <!-- taruh stok untuk mengetahui disini tanpa menginputnya kedalam databases -->
                            <div class="col-md-2">
                                <label for="stok" class="form-label">Stok Tersedia</label>
                                <input type="number" class="form-control stok-input" name="stok[]" readonly>
                            </div>
                            <div class="col-md-2">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control jumlah-input" name="jumlah[]" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label for="harga_satuan" class="form-label">Harga Satuan</label>
                                <input type="number" class="form-control harga-satuan-input" name="harga_satuan[]" min="0" required readonly>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm remove-produk">Hapus</button>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tambah Produk -->
                    <div class="mb-3">
                        <button type="button" id="tambah-produk" class="btn btn-secondary btn-sm">Tambah Produk</button>
                    </div>

                    <!-- Tombol Simpan dan Batal -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('pesanan.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    // Tambah Produk
    document.getElementById('tambah-produk').addEventListener('click', function() {
        const produkContainer = document.getElementById('produk-container');
        const newProdukItem = document.querySelector('.produk-item').cloneNode(true);

        // Reset nilai input
        newProdukItem.querySelector('.produk-select').selectedIndex = 0;
        newProdukItem.querySelector('.warna-select').innerHTML = '<option value="">Pilih Warna</option>';
        newProdukItem.querySelector('.ukuran-select').innerHTML = '<option value="">Pilih Ukuran</option>';
        newProdukItem.querySelector('.jumlah-input').value = '';
        newProdukItem.querySelector('.harga-satuan-input').value = '';

        produkContainer.appendChild(newProdukItem);
    });

    // Hapus Produk
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-produk')) {
            const produkItem = e.target.closest('.produk-item');
            if (document.querySelectorAll('.produk-item').length > 1) {
                produkItem.remove();
            }
        }
    });

    // Ambil Harga Satuan dan Warna dari Produk yang Dipilih
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('produk-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const hargaSatuan = selectedOption.getAttribute('data-harga');
            const hargaSatuanInput = e.target.closest('.produk-item').querySelector('.harga-satuan-input');
            hargaSatuanInput.value = hargaSatuan;

            // Ambil data warna dari server
            const produkId = e.target.value;
            const warnaSelect = e.target.closest('.produk-item').querySelector('.warna-select');

            if (produkId) {
                fetch(`/api/warna-by-produk/${produkId}`)
                    .then(response => response.json())
                    .then(data => {
                        warnaSelect.innerHTML = '<option value="">Pilih Warna</option>';
                        data.forEach(warna => {
                            const option = document.createElement('option');
                            option.value = warna.warna; // Simpan nama warna sebagai value
                            option.textContent = warna.warna; // Tampilkan nama warna di dropdown
                            option.setAttribute('data-id', warna.id); // Simpan ID warna di data attribute
                            warnaSelect.appendChild(option);
                        });
                    });
            } else {
                warnaSelect.innerHTML = '<option value="">Pilih Warna</option>';
            }

            // Reset ukuran
            const ukuranSelect = e.target.closest('.produk-item').querySelector('.ukuran-select');
            ukuranSelect.innerHTML = '<option value="">Pilih Ukuran</option>';

            // Hitung ulang total harga
            hitungTotalHarga();
        }
    });

    // Ambil Ukuran berdasarkan Warna yang Dipilih
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('warna-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const warnaId = selectedOption.getAttribute('data-id'); // Ambil ID warna dari data attribute
            const ukuranSelect = e.target.closest('.produk-item').querySelector('.ukuran-select');
            const stokInput = e.target.closest('.produk-item').querySelector('.stok-input');

            if (warnaId) {
                fetch(`/api/ukuran-by-warna/${warnaId}`) // Kirim ID warna sebagai parameter
                    .then(response => response.json())
                    .then(data => {
                        ukuranSelect.innerHTML = '<option value="">Pilih Ukuran</option>';
                        data.forEach(ukuran => {
                            const option = document.createElement('option');
                            option.value = ukuran.ukuran; // Mengambil ukuran, bukan ID
                            option.textContent = ukuran.ukuran;
                            option.setAttribute('data-stok', ukuran.stok); // Simpan stok di data attribute
                            ukuranSelect.appendChild(option);
                        });

                        // Reset stok saat warna dipilih
                        stokInput.value = '';
                    });
            } else {
                ukuranSelect.innerHTML = '<option value="">Pilih Ukuran</option>';
                stokInput.value = ''; // Reset stok jika warna tidak dipilih
            }
        }
    });

    // Ambil Stok saat Ukuran Dipilih
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('ukuran-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const stok = selectedOption.getAttribute('data-stok'); // Ambil stok dari data attribute
            const stokInput = e.target.closest('.produk-item').querySelector('.stok-input');

            if (stok) {
                stokInput.value = stok; // Tampilkan stok di input stok
            } else {
                stokInput.value = ''; // Reset stok jika ukuran tidak dipilih
            }
        }
    });


    // Hitung Total Harga dan Sisa Pembayaran
    function hitungTotalHarga() {
        let totalHarga = 0;
        document.querySelectorAll('.produk-item').forEach(item => {
            const jumlah = item.querySelector('.jumlah-input').value || 0;
            const hargaSatuan = item.querySelector('.harga-satuan-input').value || 0;
            totalHarga += jumlah * hargaSatuan;
        });
        document.getElementById('total_harga').value = totalHarga;

        // Hitung Sisa Pembayaran
        const pembayaranManual = parseFloat(document.getElementById('pembayaran_manual').value) || 0;
        const sisaPembayaran = totalHarga - pembayaranManual;

        // Pastikan sisa pembayaran tidak minus
        if (sisaPembayaran < 0) {
            document.getElementById('sisa_pembayaran').value = 0;
        } else {
            document.getElementById('sisa_pembayaran').value = sisaPembayaran;
        }
    }

    // Event listener untuk input jumlah dan pembayaran manual
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('jumlah-input') || e.target.classList.contains('harga-satuan-input') || e.target.id === 'pembayaran_manual') {
            hitungTotalHarga();
        }
    });

    // Hitung total harga saat halaman dimuat pertama kali
    hitungTotalHarga();

    // Validasi Pembayaran Minimal 100%
    function validasiPembayaran() {
        const totalHarga = parseFloat(document.getElementById('total_harga').value) || 0;
        const pembayaranManual = parseFloat(document.getElementById('pembayaran_manual').value) || 0;

        if (pembayaranManual < totalHarga) {
            alert(`Pembayaran wajib 100% dari total harga. Anda harus membayar Rp ${totalHarga.toLocaleString()}.`);
            return false; // Menghentikan proses submit form
        }
        return true; // Lanjutkan proses submit form
    }
</script>
@endsection