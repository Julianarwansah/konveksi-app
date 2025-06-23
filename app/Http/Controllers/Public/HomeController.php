<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Produk::with(['gambarDetails', 'ukuran', 'warna'])->get();
        return view('public.index', compact('products'));
    }

    public function produkshopshow($id)
    {
        $products = Produk::with(['gambarDetails', 'ukuran.warna', 'produkWarna'])->findOrFail($id);


        $relatedProducts = Produk::where('kategori', $products->kategori)
                            ->where('id', '!=', $products->id)
                            ->with(['gambarDetails'])
                            ->limit(4)
                            ->get();

        // Pastikan semua kombinasi ukuran dan warna termasuk
        $variantMap = [];
        foreach ($products->ukuran as $ukuran) {
            $variantMap[] = [
                'ukuran_id' => $ukuran->id,
                'ukuran' => $ukuran->ukuran,
                'warna_id' => $ukuran->warna_id,
                'warna' => optional($ukuran->warna)->warna,
                'stok' => $ukuran->stok,
            ];
        }

        return view('public.produkshopdetail', compact('products', 'relatedProducts', 'variantMap'));
    }

    // HomeController.php
    public function produkshop(Request $request)
    {
        // Ambil semua kategori unik
        $categories = Produk::select('kategori')->distinct()->pluck('kategori');
        
        // Query produk
        $query = Produk::with(['gambarDetails', 'ukuran', 'warna']);
        
        // Filter berdasarkan kategori jika ada
        if ($request->has('category') && $request->category != '') {
            $query->where('kategori', $request->category);
        }
        
        $products = $query->get();
        
        // Simpan kategori aktif untuk ditampilkan di view
        $activeCategory = $request->category ?? '';
        
        return view('public.produkshop', compact('products', 'categories', 'activeCategory'));
    }

}
