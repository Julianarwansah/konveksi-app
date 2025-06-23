@extends('layouts.app') 

@section('title', 'Edit Pesanan Custom')

@php
    // Ambil semua data warna dan kelompokkan berdasarkan template_id
    $allWarna = \App\Models\TemplateWarna::all()->groupBy('template_id');
    
    // Fungsi untuk mengecek apakah string adalah hex color
    function isHexColor($color) {
        return preg_match('/^#[0-9A-F]{6}$/i', $color);
    }

    // Fungsi untuk menentukan warna teks kontras (PHP version)
    function getContrastColor($hexColor) {
        // Hilangkan # jika ada
        $hexColor = ltrim($hexColor, '#');
        
        // Konversi ke RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Hitung brightness
        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
        
        // Return warna teks berdasarkan brightness
        return $brightness > 128 ? '#000000' : '#FFFFFF';
    }
@endphp

<style>
    .warna-select option {
        padding: 5px;
    }
    .warna-select option[value=""] {
        background-color: white !important;
        color: black !important;
    }
    .readonly-field {
        background-color: #e9ecef;
        pointer-events: none;
    }
</style>

@section('content')
    <div class="container-fluid">
        <h4 class="fw-bold py-3 mb-4">Edit Pesanan Custom</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('pesanan-pakaian-custom.update', $pesanan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Input Customer ID -->
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select class="form-control readonly-field" id="customer_id" name="customer_id" readonly>
                            <option value="">Pilih Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $pesanan->customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Input Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            {{-- Alur pembayaran & verifikasi --}}
                            <option value="Menunggu Pembayaran" {{ $pesanan->status == 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="Menunggu Konfirmasi" {{ $pesanan->status == 'Menunggu Konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                            <option value="Pembayaran Diverifikasi" {{ $pesanan->status == 'Pembayaran Diverifikasi' ? 'selected' : '' }}>Pembayaran Diverifikasi</option>

                            {{-- Alur custom/manufaktur --}}
                            <option value="Dalam Antrian Produksi" {{ $pesanan->status == 'Dalam Antrian Produksi' ? 'selected' : '' }}>Dalam Antrian Produksi</option>
                            <option value="Dalam Produksi" {{ $pesanan->status == 'Dalam Produksi' ? 'selected' : '' }}>Dalam Produksi</option>
                            <option value="Selesai Produksi" {{ $pesanan->status == 'Selesai Produksi' ? 'selected' : '' }}>Selesai Produksi</option>

                            {{-- Alur pengemasan & pengiriman --}}
                            <option value="Sedang Pengemasan" {{ $pesanan->status == 'Sedang Pengemasan' ? 'selected' : '' }}>Sedang Pengemasan</option>
                            <option value="Siap Dikirim" {{ $pesanan->status == 'Siap Dikirim' ? 'selected' : '' }}>Siap Dikirim</option>
                            <option value="Dalam Pengiriman" {{ $pesanan->status == 'Dalam Pengiriman' ? 'selected' : '' }}>Dalam Pengiriman</option>
                            <option value="Selesai Pengiriman" {{ $pesanan->status == 'Selesai Pengiriman' ? 'selected' : '' }}>Selesai Pengiriman</option>

                            {{-- Status akhir --}}
                            <option value="Selesai" {{ $pesanan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    <!-- Input Metode Pembayaran -->
                    <div class="mb-3">
                        <label for="metode" class="form-label">Metode Pembayaran</label>
                        <select class="form-control readonly-field" id="metode" name="metode" readonly>
                            <option value="Transfer Bank" {{ $pesanan->pembayarans->first()->metode == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="COD" {{ $pesanan->pembayarans->first()->metode == 'COD' ? 'selected' : '' }}>COD</option>
                            <option value="Kartu Kredit" {{ $pesanan->pembayarans->first()->metode == 'Kartu Kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                            <option value="Lainnya" {{ $pesanan->pembayarans->first()->metode == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    <!-- Input Total Harga -->
                    <div class="mb-3">
                        <label for="total_harga" class="form-label">Total Harga</label>
                        <input type="number" class="form-control readonly-field" id="total_harga" name="total_harga" value="{{ $pesanan->total_harga }}" readonly>
                    </div>

                    <!-- Input Jumlah Pembayaran -->
                    <div class="mb-3">
                        <label for="jumlah_pembayaran" class="form-label">Jumlah Pembayaran</label>
                        <input type="number" class="form-control readonly-field" id="jumlah_pembayaran" name="jumlah_pembayaran" value="{{ $pesanan->pembayarans->first()->jumlah ?? 0 }}" readonly>
                    </div>

                    <!-- Input Bukti Pembayaran -->
                    <div class="mb-3">
                        <label class="form-label">Bukti Pembayaran</label>
                        @if($pesanan->pembayarans->first()->bukti_bayar)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $pesanan->pembayarans->first()->bukti_bayar) }}" alt="Bukti Bayar" style="max-width: 200px;">
                            </div>
                        @else
                            <p class="text-muted">Tidak ada bukti pembayaran</p>
                        @endif
                    </div>

                    <!-- Input Custom Order -->
                    <div id="custom-container">
                        @foreach($pesanan->custom as $index => $custom)
                            <div class="custom-item mb-4 border p-3" data-custom-id="{{ $custom->id }}">
                                <input type="hidden" name="custom_id[]" value="{{ $custom->id }}">
                                
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="model" class="form-label">Model</label>
                                        <select class="form-control model-select readonly-field" name="model[]" readonly>
                                            <option value="">Pilih Model</option>
                                            @foreach($templates as $template)
                                                <option value="{{ $template->id }}" 
                                                    {{ $custom->template_id == $template->id ? 'selected' : '' }}>
                                                    {{ $template->model }} (Rp {{ number_format($template->harga_estimasi, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Input Warna -->
                                    <div class="col-md-2">
                                        <label for="warna" class="form-label">Warna</label>
                                        <select class="form-control warna-select readonly-field" name="warna[]" readonly>
                                            <option value="">Pilih Warna</option>
                                            @if($custom->template_id && isset($allWarna[$custom->template_id]))
                                                @foreach($allWarna[$custom->template_id] as $warna)
                                                    <option value="{{ $warna->warna }}" 
                                                        {{ $custom->warna == $warna->warna ? 'selected' : '' }}
                                                        @if(isHexColor($warna->warna))
                                                            style="background-color: {{ $warna->warna }}; color: {{ getContrastColor($warna->warna) }}"
                                                        @endif>
                                                        {{ $warna->warna }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="ukuran" class="form-label">Ukuran</label>
                                        <input type="text" class="form-control readonly-field" name="ukuran[]" value="{{ $custom->ukuran }}" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="jumlah" class="form-label">Jumlah</label>
                                        <input type="number" class="form-control readonly-field" name="jumlah[]" value="{{ $custom->jumlah }}" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="harga_estimasi" class="form-label">Harga Estimasi</label>
                                        <input type="number" class="form-control harga-estimasi-input readonly-field" name="harga_estimasi[]" value="{{ $custom->harga_estimasi }}" readonly>
                                    </div>
                                </div>

                                <!-- Input Catatan -->
                                <div class="mb-3">
                                    <label for="catatan" class="form-label">Catatan</label>
                                    <textarea class="form-control readonly-field" name="catatan[]" rows="3" readonly>{{ $custom->catatan }}</textarea>
                                </div>

                                <!-- Input Gambar (img) -->
                                <div class="mb-3">
                                    <label class="form-label">Gambar Desain</label>
                                    @if($custom->img)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/custom/' . $custom->img) }}" alt="Desain Custom" style="max-width: 200px;">
                                        </div>
                                    @else
                                        <p class="text-muted">Tidak ada gambar desain</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Tombol Simpan dan Batal -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">Update Status</button>
                        <a href="{{ route('pesanan.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection