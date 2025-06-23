<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Customer;
use App\Models\Pemasukan;
use App\Models\Pesanan;
use App\Models\Produk;
use Carbon\Carbon;
use App\Models\Keuangan;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total pendapatan bulan ini
        $totalPendapatan = Keuangan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total_pemasukan');

        // Hitung total pesanan bulan ini
        $totalPesanan = Pesanan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Hitung total produk
        $totalProduk = Produk::count();

        // Hitung total pelanggan
        $totalPelanggan = Customer::count();

        // Ambil data untuk grafik pendapatan 6 bulan terakhir
        $chartPendapatan = $this->getChartPendapatan();

        // Ambil data untuk grafik status pesanan
        $chartStatusPesanan = $this->getChartStatusPesanan();

        // Ambil 5 pesanan terbaru
        $pesananTerbaru = Pesanan::with('customer')
            ->latest()
            ->take(5)
            ->get();

        // Ambil 5 produk terlaris
        $produkTerlaris = Produk::withCount([
                'pesananDetail as total_terjual' => function($query) {
                    $query->select(DB::raw('COALESCE(SUM(jumlah), 0)'));
                }
            ])
            ->withSum('pesananDetail as total_pendapatan', 'sub_total') // Alias total_pendapatan
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        // Ambil 5 template terlaris
        $templateTerlaris = Template::withCount([
                'custom as total_terjual' => function($query) {
                    $query->select(DB::raw('COALESCE(SUM(jumlah), 0)'));
                }
            ])
            ->withSum('custom as total_pendapatan', 'harga_estimasi')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();


        // Ambil stok bahan baku
        $stokBahan = Bahan::orderBy('stok', 'asc')
            ->take(5)
            ->get();

        return view('pages.dashboard', compact(
            'totalPendapatan',
            'totalPesanan',
            'totalProduk',
            'totalPelanggan',
            'chartPendapatan',
            'chartStatusPesanan',
            'pesananTerbaru',
            'produkTerlaris',
            'templateTerlaris',
            'stokBahan'
        ));
    }

    private function getChartPendapatan()
    {
        $months = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $total = Pemasukan::whereMonth('tanggal', $date->month)
                ->whereYear('tanggal', $date->year)
                ->sum('jumlah');
            
            $data[] = $total;
        }
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    private function getChartStatusPesanan()
    {
        $statuses = [
            'Menunggu Pembayaran',
            'Menunggu Konfirmasi',
            'Pembayaran Diverifikasi',
            'Dalam Produksi',
            'Selesai'
        ];
        
        $labels = [];
        $data = [];
        
        foreach ($statuses as $status) {
            $count = Pesanan::where('status', $status)->count();
            
            if ($count > 0) {
                $labels[] = $status;
                $data[] = $count;
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}