@extends('layouts.app')

@section('title', 'Edit Antrian Produksi')

@section('content')
<div class="container-fluid">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Produksi /</span> Edit Antrian Produksi
    </h4>

    <!-- Notifikasi -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Form Edit Antrian</h5>
            <a href="{{ route('antrian.index') }}" class="btn btn-sm btn-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('antrian.update', $antrian->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="custom_id" class="form-label">Pesanan Custom <span class="text-danger">*</span></label>
                            <select class="form-select @error('custom_id') is-invalid @enderror" 
                                    id="custom_id" name="custom_id" disabled>
                                @foreach($customs as $custom)
                                    <option value="{{ $custom->id }}" 
                                        {{ $antrian->custom_id == $custom->id ? 'selected' : '' }}
                                        data-customer="{{ $custom->customer->nama }}"
                                        data-template="{{ $custom->template->nama ?? '-' }}"
                                        data-jumlah="{{ $custom->jumlah }}"
                                        data-warna="{{ $custom->warna }}"
                                        data-model="{{ $custom->model }}"
                                        data-status-pembayaran="{{ $custom->pesanan->status_pembayaran }}">
                                        {{ $custom->id }} - {{ $custom->customer->nama }} 
                                        ({{ $custom->template->nama ?? '-' }}) 
                                        - {{ $custom->pesanan->status_pembayaran }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- Tambahkan input hidden untuk mengirim nilai -->
                            <input type="hidden" name="custom_id" value="{{ $antrian->custom_id }}">
                            @error('custom_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Informasi Customer -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Informasi Customer</h6>
                            </div>
                            <div class="card-body">
                                <div id="customer-info">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Nama:</strong> {{ $antrian->custom->customer->nama }}</li>
                                        <li><strong>Pesanan ID:</strong> {{ $antrian->custom_id }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal Produksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                   id="tanggal" name="tanggal" 
                                   value="{{ old('tanggal', $antrian->tanggal) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Detail Produk -->
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Detail Produk</h6>
                                <div>
                                    <label for="jumlah" class="form-label mb-0 me-2">Jumlah</label>
                                    <input readonly type="number" id="jumlah" name="jumlah" 
                                           class="form-control @error('jumlah') is-invalid @enderror d-inline-block" 
                                           style="width: 100px;" 
                                           value="{{ old('jumlah', $antrian->jumlah) }}" min="1" required>
                                    @error('jumlah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="product-info">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Template:</strong> {{ $antrian->custom->template->nama ?? '-' }}</li>
                                        <li><strong>Warna:</strong> {{ $antrian->custom->warna ?? '-' }}</li>
                                        <li><strong>Model:</strong> {{ $antrian->custom->model ?? '-' }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pembayaran -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Status Pembayaran</h6>
                            </div>
                            <div class="card-body">
                                <div id="payment-info">
                                    @php
                                        $paymentStatus = $antrian->custom->pesanan->status_pembayaran;
                                        $alertClass = $paymentStatus === 'Lunas' ? 'alert-success' : 'alert-warning';
                                    @endphp
                                    <div class="alert {{ $alertClass }} mb-0">
                                        <strong>Status Pembayaran:</strong> {{ $paymentStatus }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status Produksi <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ old('status', $antrian->status) == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end mt-4">
                    <button type="reset" class="btn btn-outline-secondary me-2">
                        <i class="bx bx-reset"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function updateInfo(selected) {
            if(selected && selected.val()) {
                // Customer info
                $('#customer-info').html(`
                    <ul class="list-unstyled mb-0">
                        <li><strong>Nama:</strong> ${selected.data('customer') || 'Tidak tersedia'}</li>
                        <li><strong>Pesanan ID:</strong> ${selected.val()}</li>
                    </ul>
                `);
                
                // Product info
                $('#product-info').html(`
                    <ul class="list-unstyled mb-0">
                        <li><strong>Template:</strong> ${selected.data('template') || '-'}</li>
                        <li><strong>Warna:</strong> ${selected.data('warna') || '-'}</li>
                        <li><strong>Model:</strong> ${selected.data('model') || '-'}</li>
                    </ul>
                `);

                // Update jumlah input
                $('#jumlah').val(selected.data('jumlah') || 0);

                // Payment info
                const paymentStatus = selected.data('status-pembayaran');
                const alertClass = paymentStatus === 'Lunas' ? 'alert-success' : 'alert-warning';
                $('#payment-info').html(`
                    <div class="alert ${alertClass} mb-0">
                        <strong>Status Pembayaran:</strong> ${paymentStatus || 'Tidak diketahui'}
                    </div>
                `);
            } else {
                resetInfo();
            }
        }

        function resetInfo() {
            $('#customer-info').html('<p class="text-muted mb-0">Pilih pesanan custom terlebih dahulu</p>');
            $('#product-info').html('<p class="text-muted mb-0">Pilih pesanan custom terlebih dahulu</p>');
            $('#payment-info').html('<p class="text-muted mb-0">Pilih pesanan custom terlebih dahulu</p>');
            $('#jumlah').val(0);
        }

        $('#custom_id').on('change', function() {
            updateInfo($(this).find('option:selected'));
        });

        @if(old('custom_id'))
            $('#custom_id').trigger('change');
        @endif
    });
</script>
@endsection