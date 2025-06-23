@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Beranda /</span> Dashboard
    </h4>

    <!-- Statistik Utama -->
    <div class="row mb-4">
        <!-- Total Pendapatan -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2">TOTAL PENDAPATAN</h6>
                            <h3 class="mb-0">@currency($totalPendapatan)</h3>
                            <small class="text-white-50">Bulan Ini</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-white-10">
                                <i class="bx bx-dollar-circle bx-lg"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pesanan -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2">TOTAL PESANAN</h6>
                            <h3 class="mb-0">{{ $totalPesanan }}</h3>
                            <small class="text-white-50">Bulan Ini</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-white-10">
                                <i class="bx bx-shopping-bag bx-lg"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Tersedia -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2">PRODUK TERSEDIA</h6>
                            <h3 class="mb-0">{{ $totalProduk }}</h3>
                            <small class="text-white-50">Total Stok</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-white-10">
                                <i class="bx bx-tshirt bx-lg"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pelanggan -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2">PELANGGAN</h6>
                            <h3 class="mb-0">{{ $totalPelanggan }}</h3>
                            <small class="text-white-50">Total Terdaftar</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-white-10">
                                <i class="bx bx-user bx-lg"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Informasi -->
    <div class="row mb-4">
        <!-- Grafik Pendapatan -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="mb-0">Statistik Pendapatan</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt1">
                            <a class="dropdown-item" href="javascript:void(0);">Lihat Detail</a>
                            <a class="dropdown-item" href="javascript:void(0);">Ekspor Data</a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <canvas id="pendapatanChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Pesanan -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Status Pesanan</h5>
                </div>
                <div class="card-body pt-0">
                    <canvas id="statusPesananChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pesanan Terbaru -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="mb-0">Pesanan Terbaru</h5>
                    <a href="{{ route('pesanan.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-list-ul me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesananTerbaru as $pesanan)
                            <tr>
                                <td><strong>#{{ $pesanan->id }}</strong></td>
                                <td>{{ $pesanan->customer->nama }}</td>
                                <td>{{ $pesanan->created_at->format('d M Y') }}</td>
                                <td>@currency($pesanan->total_harga)</td>
                                <td>
                                    @if($pesanan->status == 'Menunggu Pembayaran')
                                        <span class="badge bg-label-warning">Menunggu Pembayaran</span>
                                    @elseif($pesanan->status == 'Menunggu Konfirmasi')
                                        <span class="badge bg-label-info">Menunggu Konfirmasi</span>
                                    @elseif($pesanan->status == 'Pembayaran Diverifikasi')
                                        <span class="badge bg-label-primary">Pembayaran Diverifikasi</span>
                                    @elseif($pesanan->status == 'Dalam Antrian Produksi')
                                        <span class="badge bg-label-secondary">Dalam Antrian Produksi</span>
                                    @elseif($pesanan->status == 'Dalam Produksi')
                                        <span class="badge bg-label-secondary">Dalam Produksi</span>
                                    @elseif($pesanan->status == 'Selesai Produksi')
                                        <span class="badge bg-label-success">Selesai Produksi</span>
                                    @elseif($pesanan->status == 'Sedang Pengemasan')
                                        <span class="badge bg-label-info">Sedang Pengemasan</span>
                                    @elseif($pesanan->status == 'Siap Dikirim')
                                        <span class="badge bg-label-primary">Siap Dikirim</span>
                                    @elseif($pesanan->status == 'Dalam Pengiriman')
                                        <span class="badge bg-label-warning">Dalam Pengiriman</span>
                                    @elseif($pesanan->status == 'Selesai Pengiriman')
                                        <span class="badge bg-label-success">Selesai Pengiriman</span>
                                    @elseif($pesanan->status == 'Selesai')
                                        <span class="badge bg-label-success">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('pesanan.show', $pesanan->id) }}" class="btn btn-sm btn-icon btn-outline-primary me-2" data-bs-toggle="tooltip" title="Detail">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk dan Bahan Baku -->
    <div class="row">
        <!-- Produk Terlaris -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="mb-0">Produk Terlaris</h5>
                    <a href="{{ route('produk.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-list-ul me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Terjual</th>
                                <th>Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produkTerlaris as $produk)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md me-3">
                                            @if($produk->img)
                                                <img src="{{ asset('storage/' . $produk->img) }}" alt="Produk" class="rounded">
                                            @else
                                                <img src="{{ asset('assets/img/default-product.png') }}" alt="Produk" class="rounded">
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $produk->nama }}</h6>
                                            <small class="text-muted">{{ $produk->kategori }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-label-primary">{{ $produk->total_terjual }} pcs</span></td>
                                <td>@currency($produk->total_pendapatan)</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Template Terlaris -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="mb-0">Template Terlaris</h5>
                    <a href="{{ route('template.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-list-ul me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Template</th>
                                <th>Terjual</th>
                                <th>Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templateTerlaris as $template)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md me-3">
                                            @if($template->gambar->first())
                                                <img src="{{ asset('storage/' . $template->gambar->first()->gambar) }}" alt="Template" class="rounded">
                                            @else
                                                <img src="{{ asset('assets/img/default-template.png') }}" alt="Template" class="rounded">
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $template->model }}</h6>
                                            <small class="text-muted">Template Custom</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-label-primary">{{ $template->total_terjual }} pcs</span></td>
                                <td>@currency($template->total_pendapatan)</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bahan Baku -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="mb-0">Stok Bahan Baku</h5>
                    <a href="{{ route('bahan.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-list-ul me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Bahan</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stokBahan as $bahan)
                            <tr>
                                <td>{{ $bahan->nama }}</td>
                                <td>{{ $bahan->stok }}</td>
                                <td>{{ $bahan->satuan }}</td>
                                <td>
                                    @if($bahan->stok < 10)
                                        <span class="badge bg-label-danger">Hampir Habis</span>
                                    @elseif($bahan->stok < 30)
                                        <span class="badge bg-label-warning">Sedikit</span>
                                    @else
                                        <span class="badge bg-label-success">Aman</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Grafik Pendapatan Bulanan
        const pendapatanCtx = document.getElementById('pendapatanChart').getContext('2d');
        const pendapatanChart = new Chart(pendapatanCtx, {
            type: 'line',
            data: {
                labels: @json($chartPendapatan['labels']),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($chartPendapatan['data']),
                    backgroundColor: 'rgba(105, 108, 255, 0.2)',
                    borderColor: 'rgba(105, 108, 255, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Grafik Status Pesanan
        const statusPesananCtx = document.getElementById('statusPesananChart').getContext('2d');
        const statusPesananChart = new Chart(statusPesananCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartStatusPesanan['labels']),
                datasets: [{
                    data: @json($chartStatusPesanan['data']),
                    backgroundColor: [
                        '#ff6384',
                        '#36a2eb',
                        '#ffce56',
                        '#4bc0c0',
                        '#9966ff'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Inisialisasi tooltip
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip()
        });
    </script>
@endsection
