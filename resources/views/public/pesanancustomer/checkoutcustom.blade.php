@extends('layoutspublic.app')

@section('title', 'Checkout Custom Order')


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
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Tambah Pesanan Custom</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('public.pesanan.custom.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validasiPembayaran()">
                    @csrf
                    <!-- Tampilkan Nama Customer -->
                    <div class="mb-3">
                        <label class="form-label">Customer</label>
                        <input type="text" class="form-control" value="{{ auth('customer')->user()->nama }}" readonly>
                        <input type="hidden" name="customer_id" value="{{ auth('customer')->user()->id }}">
                    </div>

                    <!-- Input Metode Pembayaran -->
                    <div class="mb-3">
                        <label for="metode" class="form-label">Metode Pembayaran</label>
                        <select class="form-control" id="metode" name="metode" required>
                            <option value="Transfer Bank">Transfer Bank</option>
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

                    <!-- Input Custom Order -->
                    <div id="custom-container">
                        <div class="custom-item mb-4 border p-3">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="model" class="form-label">Model</label>

                                    {{-- Kirim ID model --}}
                                    <input type="hidden" name="model[]" value="{{ $selectedData['template_id'] }}">

                                    {{-- Tampilkan nama model + harga, readonly saja --}}
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        value="{{ 
                                            $templates->firstWhere('id', $selectedData['template_id'])->model
                                            }} (Rp {{ 
                                            number_format(
                                                $templates->firstWhere('id', $selectedData['template_id'])->harga_estimasi, 
                                                0, ',', '.'
                                            ) 
                                        }})" 
                                        readonly
                                    >
                                </div>

                                @php
                                    // ambil objek warna
                                    $warnaObj = optional(
                                        $templates
                                            ->firstWhere('id', $selectedData['template_id'])
                                            ->warna
                                            ->firstWhere('id', $selectedData['warna_id'])
                                    );
                                    $hex = $warnaObj->warna ?? '#ffffff'; // fallback putih
                                @endphp

                                <div class="col-md-2">
                                    <label class="form-label">Warna</label>
                                    {{-- kirim warna_id --}}
                                    <input type="hidden" name="warna_id[]" value="{{ $selectedData['warna_id'] ?? '' }}">

                                    {{-- tampil swatch + kode hex readonly --}}
                                    <div class="d-flex align-items-center">
                                        {{-- swatch kotak kecil --}}
                                        <div 
                                            style="
                                                width: 1.5rem; 
                                                height: 1.5rem; 
                                                background-color: {{ $hex }}; 
                                                border: 1px solid #ccc; 
                                                margin-right: .5rem;
                                            ">
                                        </div>
                                        {{-- kode hex --}}
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            value="{{ $hex }}" 
                                            readonly
                                            style="max-width: 6rem;"
                                        >
                                    </div>
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
    const warnaData = @json($allWarna);
    const templateData = @json($templates->mapWithKeys(function($item) {
        return [$item->id => [
            'model' => $item->model,
            'harga_estimasi' => $item->harga_estimasi
        ]];
    }));

    function getContrastColor(hexColor) {
        const r = parseInt(hexColor.substr(1, 2), 16);
        const g = parseInt(hexColor.substr(3, 2), 16);
        const b = parseInt(hexColor.substr(5, 2), 16);
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
        return brightness > 128 ? '#000000' : '#FFFFFF';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const firstItem = document.querySelector('.custom-item');
        if (firstItem) {
            const hargaEstimasi = parseFloat("{{ $templates->firstWhere('id', $selectedData['template_id'])->harga_estimasi }}") || 0;
            firstItem.querySelector('.harga-estimasi-input').value = hargaEstimasi;
            hitungTotalHarga();
        }
    });

    // Tambah Custom Order
    document.getElementById('tambah-custom').addEventListener('click', function () {
        const customContainer = document.getElementById('custom-container');
        const firstItem = document.querySelector('.custom-item');
        const newCustomItem = firstItem.cloneNode(true);

        // Ambil nilai dari item pertama
        const modelValue = firstItem.querySelector('input[name="model[]"]').value;
        const warnaIdValue = firstItem.querySelector('input[name="warna_id[]"]').value;
        const warnaHex = firstItem.querySelector('input[type="text"][value^="#"]').value;
        const hargaEstimasi = firstItem.querySelector('.harga-estimasi-input').value;

        newCustomItem.querySelectorAll('input, textarea').forEach(input => {
            if (input.name === "model[]") {
                input.value = modelValue;
            } else if (input.name === "warna_id[]") {
                input.value = warnaIdValue;
            } else if (input.classList.contains("harga-estimasi-input")) {
                input.value = hargaEstimasi;
            } else if (input.type !== "hidden") {
                input.value = "";
            }
        });

        const inputModelReadonly = newCustomItem.querySelector('input[readonly]');
        if (inputModelReadonly && templateData[modelValue]) {
            const selectedTemplate = templateData[modelValue];
            inputModelReadonly.value = `${selectedTemplate.model} (Rp ${Number(selectedTemplate.harga_estimasi).toLocaleString('id-ID')})`;
        }

        const warnaSwatch = newCustomItem.querySelector('div[style*="background-color"]');
        if (warnaSwatch) {
            warnaSwatch.style.backgroundColor = warnaHex;
        }

        const hexInput = newCustomItem.querySelector('input[type="text"][value^="#"]');
        if (hexInput) {
            hexInput.value = warnaHex;
        }

        // Reset field input
        newCustomItem.querySelector('textarea[name="catatan[]"]').value = '';
        newCustomItem.querySelector('input[name="img[]"]').value = '';
        newCustomItem.querySelector('input[name="ukuran[]"]').value = '';
        newCustomItem.querySelector('input[name="jumlah[]"]').value = '';

        customContainer.appendChild(newCustomItem);

        // Tambahkan event hitung total pada input jumlah
        newCustomItem.querySelector('input[name="jumlah[]"]').addEventListener('input', function() {
            hitungTotalHarga();
        });
    });

    // Hapus Custom Order
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-custom')) {
            const customItem = e.target.closest('.custom-item');
            if (document.querySelectorAll('.custom-item').length > 1) {
                customItem.remove();
                hitungTotalHarga();
            }
        }
    });

    // Hitung Total
    function hitungTotalHarga() {
        let total = 0;

        document.querySelectorAll('.custom-item').forEach(item => {
            const harga = parseFloat(item.querySelector('.harga-estimasi-input').value) || 0;
            const jumlah = parseFloat(item.querySelector('input[name="jumlah[]"]').value) || 1;
            total += harga * jumlah;
        });

        document.getElementById('total_harga').value = total;
    }

    // Event listener untuk perubahan jumlah model
    document.addEventListener('input', function(e) {
        if (e.target.name === 'jumlah[]') {
            hitungTotalHarga();
        }
    });

    document.getElementById('jumlah_pembayaran').addEventListener('input', function() {
        hitungTotalHarga();
        
        const total = parseFloat(document.getElementById('total_harga').value) || 0;
        const bayar = parseFloat(this.value) || 0;
        const minimal = total * 0.5;

        let warning = document.getElementById('warning-pembayaran');
        if (!warning) {
            warning = document.createElement('div');
            warning.id = 'warning-pembayaran';
            warning.className = 'text-danger mt-1';
            this.parentNode.appendChild(warning);
        }

        if (bayar < minimal) {
            warning.textContent = `Jumlah pembayaran minimal Rp ${minimal.toLocaleString('id-ID')}`;
        } else {
            warning.textContent = '';
        }
    });

    // Validasi Pembayaran
    function validasiPembayaran() {
        const total = parseFloat(document.getElementById('total_harga').value) || 0;
        const bayar = parseFloat(document.getElementById('jumlah_pembayaran').value) || 0;
        const minimal = total * 0.5;

        if (bayar < minimal) {
            alert(`Pembayaran wajib minimal 50% dari total harga. Anda harus membayar minimal Rp ${minimal.toLocaleString()}.`);
            return false;
        }

        const metode = document.getElementById('metode').value;
        const valid = ['Transfer Bank', 'COD', 'Kartu Kredit', 'Lainnya'].includes(metode);
        if (!valid) {
            alert('Metode pembayaran tidak valid');
            return false;
        }

        return true;
    }
</script>

@endsection