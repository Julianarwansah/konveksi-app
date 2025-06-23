<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Custom;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        $pesanans = Pesanan::with(['customer', 'detailPesanan', 'pembayarans'])
            ->when(request('search'), function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('nama', 'like', '%' . request('search') . '%');
                });
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->paginate(10);
    
        // Tambahkan jenis_pesanan berdasarkan detailPesanan
        $pesanans->getCollection()->transform(function ($pesanan) {
            $detailPesanan = $pesanan->detailPesanan->first();
            if ($detailPesanan) {
                $pesanan->jenis_pesanan = $detailPesanan->produk_id ? 'produk' : 'custom';
            } else {
                $pesanan->jenis_pesanan = 'tidak diketahui';
            }
            
            // Tambahkan informasi pembayaran
            $pesanan->total_pembayaran = $pesanan->pembayarans->sum('jumlah');
            
            return $pesanan;
        });
    
        // Lakukan filter jenis pesanan setelah transformasi
        if (request('jenis_pesanan')) {
            $pesanans = $pesanans->filter(function ($pesanan) {
                return $pesanan->jenis_pesanan == request('jenis_pesanan');
            });
    
            // Re-paginate secara manual karena filter dilakukan setelah transformasi
            $perPage = 10;
            $currentPage = request('page', 1);
            $currentItems = $pesanans->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $pesanans = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $pesanans->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }
    
        return view('pesanan.index', compact('pesanans'));
    }    

    public function show($id)
    {
        $pesanan = Pesanan::with([
            'customer', 
            'detailPesanan.produk', 
            'detailPesanan.custom',
            'custom',
            'pembayarans',
            'pengiriman',
            'custom.customBiayas.biayaTambahan' // Load biaya tambahan melalui relasi custom
        ])->findOrFail($id);
        
        return view('pesanan.show', compact('pesanan'));
    }

    public function destroy($id)
    {
        // Cari pesanan berdasarkan ID
        $pesanan = Pesanan::findOrFail($id);

        // Hapus detail pesanan yang terkait
        PesananDetail::where('pesanan_id', $id)->delete();

        // Hapus custom yang terkait
        Custom::where('pesanan_id', $id)->delete();

        // Hapus pembayaran yang terkait
        Pembayaran::where('pesanan_id', $id)->delete();

        // Hapus pesanan
        $pesanan->delete();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}