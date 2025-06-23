@extends('layouts.app')

@section('title', 'Tambah Pesanan Custom')

@section('content')
@php
    // Ambil semua data warna dan kelompokkan berdasarkan template_id
    $allWarna = \App\Models\TemplateWarna::all()->groupBy('template_id');
@endphp
<style>
    .warna-select option {
        padding: 5px;
    }
    .warna-select option[value=""] {
        background-color: white !important;
        color: black !important;
    }
</style>
</style>
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Tambah Pesanan Custom</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('pesanan-pakaian-custom.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validasiPembayaran()">
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


                    <!-- Input Metode Pembayaran -->
                    <div class="mb-3">
                        <label for="metode" class="form-label">Metode Pembayaran</label>
                        <select class="form-control" id="metode" name="metode" required>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="COD">COD</option>
                            <option value="Kartu Kredit">Kartu Kredit</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <!-- Input Total Harga -->
                    <div class="mb-3">
                        <label for="total_harga" class="form-label">Total Harga</label>
                        <input type="number" class="form-control" id="total_harga" name="total_harga" readonly>
                    </div>

                    <!-- Input Jumlah Pembayaran -->
                    <div class="mb-3">
                        <label for="jumlah_pembayaran" class="form-label">Jumlah Pembayaran</label>
                        <input type="number" class="form-control" id="jumlah_pembayaran" name="jumlah_pembayaran" value="0" min="0" required>
                        <small class="text-danger">Pembayaran wajib minimal 50% dari total harga.</small>
                    </div>

                    <!-- Input Bukti Pembayaran -->
                    <div class="mb-3">
                        <label for="bukti_bayar" class="form-label">Bukti Pembayaran (Gambar)</label>
                        <input type="file" class="form-control" id="bukti_bayar" name="bukti_bayar">
                    </div>

                    <!-- Input Biaya Tambahan -->
                    <div class="mb-3">
                        <label class="form-label">Biaya Tambahan</label>
                        <div id="biaya-tambahan-container">
                            <div class="row mb-3 biaya-tambahan-item">
                                <div class="col-md-5">
                                    <select class="form-control biaya-tambahan-select" name="biaya_id[]">
                                        <option value="">Pilih Biaya Tambahan</option>
                                        @foreach($biayas as $biaya)
                                            <option value="{{ $biaya->id }}" data-harga="{{ $biaya->harga }}">
                                                {{ $biaya->nama }} (Rp {{ number_format($biaya->harga, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control jumlah-biaya-tambahan" name="jumlah_biaya[]" min="1" value="1">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control subtotal-biaya-tambahan" name="subtotal_biaya[]" readonly>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-biaya-tambahan">Hapus</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="tambah-biaya-tambahan" class="btn btn-secondary btn-sm mt-2">Tambah Biaya Tambahan</button>
                    </div>

                    <!-- Input Custom Order -->
                    <div id="custom-container">
                        <div class="custom-item mb-4 border p-3">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="model" class="form-label">Model</label>
                                    <select class="form-control model-select" name="model[]" required>
                                        <option value="">Pilih Model</option>
                                        @foreach($templates as $template)
                                            <option value="{{ $template->id }}" 
                                                    data-harga-estimasi="{{ $template->harga_estimasi }}"
                                                    data-details='@json($template->details)'>
                                                {{ $template->model }} (Rp {{ number_format($template->harga_estimasi, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Input Warna -->
                                <div class="col-md-2">
                                    <label for="warna" class="form-label">Warna</label>
                                    <select class="form-control warna-select" name="warna[]" required>
                                        <option value="">Pilih Warna</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="ukuran" class="form-label">Ukuran</label>
                                    <input type="text" class="form-control" name="ukuran[]" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="jumlah" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" name="jumlah[]" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="harga_estimasi" class="form-label">Harga Estimasi</label>
                                    <input type="number" class="form-control harga-estimasi-input" name="harga_estimasi[]" min="0" required readonly>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-custom">Hapus</button>
                                </div>
                            </div>

                            <!-- Input Catatan -->
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea class="form-control" name="catatan[]" rows="3"></textarea>
                            </div>

                            <!-- Input Gambar (img) -->
                            <div class="mb-3">
                                <label for="img" class="form-label">Gambar Desain</label>
                                <input type="file" class="form-control" name="img[]">
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tambah Custom Order -->
                    <div class="mb-3">
                        <button type="button" id="tambah-custom" class="btn btn-secondary btn-sm">Tambah Custom Order</button>
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
    // Data warna dari PHP ke JavaScript
    const warnaData = @json($allWarna);

    // Fungsi untuk menentukan warna teks kontras
    function getContrastColor(hexColor) {
        // Convert hex to RGB
        const r = parseInt(hexColor.substr(1, 2), 16);
        const g = parseInt(hexColor.substr(3, 2), 16);
        const b = parseInt(hexColor.substr(5, 2), 16);
        
        // Hitung brightness
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
        
        // Return warna teks berdasarkan brightness
        return brightness > 128 ? '#000000' : '#FFFFFF';
    }

    // Tambah Custom Order
    document.getElementById('tambah-custom').addEventListener('click', function() {
        const customContainer = document.getElementById('custom-container');
        const newCustomItem = document.querySelector('.custom-item').cloneNode(true);

        // Reset nilai input
        newCustomItem.querySelectorAll('input').forEach(input => input.value = '');
        newCustomItem.querySelectorAll('textarea').forEach(textarea => textarea.value = '');
        newCustomItem.querySelector('.warna-select').innerHTML = '<option value="">Pilih Warna</option>';

        customContainer.appendChild(newCustomItem);

        // Tambahkan event listener untuk input jumlah dan harga estimasi pada elemen baru
        newCustomItem.querySelector('input[name="jumlah[]"]').addEventListener('input', hitungTotalHarga);
        newCustomItem.querySelector('.harga-estimasi-input').addEventListener('input', hitungTotalHarga);
    });

    // Hapus Custom Order
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-custom')) {
            const customItem = e.target.closest('.custom-item');
            if (document.querySelectorAll('.custom-item').length > 1) {
                customItem.remove();
                hitungTotalHarga(); // Hitung ulang total harga setelah menghapus
            }
        }
    });

    // Ambil Warna dan Harga Estimasi berdasarkan Model
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('model-select')) {
            const modelId = e.target.value;
            const selectedOption = e.target.options[e.target.selectedIndex];
            const warnaSelect = e.target.closest('.custom-item').querySelector('.warna-select');

            // Kosongkan dropdown warna
            warnaSelect.innerHTML = '<option value="">Pilih Warna</option>';

            // Ambil harga estimasi langsung dari data attribute
            const hargaEstimasi = selectedOption.getAttribute('data-harga-estimasi');
            
            // Update harga estimasi
            const hargaEstimasiInput = e.target.closest('.custom-item').querySelector('.harga-estimasi-input');
            hargaEstimasiInput.value = hargaEstimasi;

            // Isi dropdown warna jika model dipilih
            if (modelId && warnaData[modelId]) {
                // Isi dropdown warna dengan data yang tersedia
                warnaData[modelId].forEach(warna => {
                    const option = document.createElement('option');
                    option.value = warna.warna;
                    option.textContent = warna.warna;
                    
                    // Tambahkan indikator warna jika format hex valid
                    if (/^#[0-9A-F]{6}$/i.test(warna.warna)) {
                        option.style.backgroundColor = warna.warna;
                        option.style.color = getContrastColor(warna.warna);
                    }
                    
                    warnaSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Tidak ada warna tersedia';
                warnaSelect.appendChild(option);
            }

            // Hitung ulang total harga
            hitungTotalHarga();
        }
    });

    // Tambah Biaya Tambahan
    document.getElementById('tambah-biaya-tambahan').addEventListener('click', function() {
        const biayaTambahanContainer = document.getElementById('biaya-tambahan-container');
        const newBiayaTambahanItem = document.querySelector('.biaya-tambahan-item').cloneNode(true);

        // Reset nilai input
        newBiayaTambahanItem.querySelector('.biaya-tambahan-select').selectedIndex = 0;
        newBiayaTambahanItem.querySelector('.jumlah-biaya-tambahan').value = 1;
        newBiayaTambahanItem.querySelector('.subtotal-biaya-tambahan').value = '';

        biayaTambahanContainer.appendChild(newBiayaTambahanItem);
    });

    // Hapus Biaya Tambahan
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-biaya-tambahan')) {
            const biayaTambahanItem = e.target.closest('.biaya-tambahan-item');
            if (document.querySelectorAll('.biaya-tambahan-item').length > 1) {
                biayaTambahanItem.remove();
                hitungTotalHarga(); // Hitung ulang total harga setelah menghapus
            }
        }
    });

    // Hitung Subtotal Biaya Tambahan
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('biaya-tambahan-select') || e.target.classList.contains('jumlah-biaya-tambahan')) {
            const biayaTambahanItem = e.target.closest('.biaya-tambahan-item');
            const biayaTambahanSelect = biayaTambahanItem.querySelector('.biaya-tambahan-select');
            const jumlahBiayaTambahan = biayaTambahanItem.querySelector('.jumlah-biaya-tambahan');
            const subtotalBiayaTambahan = biayaTambahanItem.querySelector('.subtotal-biaya-tambahan');

            const harga = parseFloat(biayaTambahanSelect.options[biayaTambahanSelect.selectedIndex].getAttribute('data-harga')) || 0;
            const jumlah = parseFloat(jumlahBiayaTambahan.value) || 0;
            const subtotal = harga * jumlah;

            subtotalBiayaTambahan.value = subtotal;
            hitungTotalHarga();
        }
    });

    // Hitung Total Harga
    function hitungTotalHarga() {
        let totalHarga = 0;

        // Hitung total harga dari custom order
        document.querySelectorAll('.custom-item').forEach(item => {
            const hargaEstimasi = parseFloat(item.querySelector('.harga-estimasi-input').value) || 0;
            const jumlah = parseFloat(item.querySelector('input[name="jumlah[]"]').value) || 1;
            totalHarga += hargaEstimasi * jumlah;
        });

        // Hitung total harga dari biaya tambahan
        document.querySelectorAll('.biaya-tambahan-item').forEach(item => {
            const subtotalBiayaTambahan = parseFloat(item.querySelector('.subtotal-biaya-tambahan').value) || 0;
            totalHarga += subtotalBiayaTambahan;
        });

        // Update nilai total harga di input
        document.getElementById('total_harga').value = totalHarga;
    }

    // Event listener untuk input jumlah dan harga estimasi
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('harga-estimasi-input') || e.target.name === 'jumlah[]') {
            hitungTotalHarga();
        }
    });

    // Event listener untuk input jumlah pembayaran
    document.getElementById('jumlah_pembayaran').addEventListener('input', hitungTotalHarga);

    // Validasi Pembayaran Minimal 50%
    function validasiPembayaran() {
        const totalHarga = parseFloat(document.getElementById('total_harga').value) || 0;
        const jumlahPembayaran = parseFloat(document.getElementById('jumlah_pembayaran').value) || 0;
        const minimalPembayaran = totalHarga * 0.5;

        if (jumlahPembayaran < minimalPembayaran) {
            alert(`Pembayaran wajib minimal 50% dari total harga. Anda harus membayar minimal Rp ${minimalPembayaran.toLocaleString()}.`);
            return false;
        }
        
        // Validasi metode pembayaran
        const metode = document.getElementById('metode').value;
        const metodeValid = ['Transfer Bank', 'COD', 'Kartu Kredit', 'Lainnya'].includes(metode);
        if (!metodeValid) {
            alert('Metode pembayaran tidak valid');
            return false;
        }
        
        return true;
    }
</script>
@endsection