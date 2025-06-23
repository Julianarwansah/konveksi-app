<?php

namespace App\Http\Controllers\Public;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProdukUkuran;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;

class CartController extends Controller
{
    // Tampilkan isi cart customer yang login
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $cartItems = Cart::with(['produk.gambarDetails', 'warna', 'ukuran'])
            ->where('customer_id', $customer->id)
            ->get();

        $totalHarga = $cartItems->sum('subtotal');

        return view('public.cart', compact('cartItems', 'totalHarga'));
    }

    public function store(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login')->with('error', 'Silakan login terlebih dahulu');
        }

        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'warna_id' => 'nullable|exists:produk_warna,id',
            'ukuran_id' => 'nullable|exists:produk_ukuran,id',
            'quantity' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric',
        ]);

        $customerId = Auth::guard('customer')->id();

        // Cek stok tersedia
        if ($request->ukuran_id) {
            $variant = ProdukUkuran::find($request->ukuran_id);
            $stokTersedia = $variant->stok;
        } else {
            $produk = Produk::find($request->produk_id);
            $stokTersedia = $produk->total_stok;
        }

        if ($request->quantity > $stokTersedia) {
            return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $stokTersedia);
        }

        // Cek apakah item sudah ada di cart
        $existingCart = Cart::where('customer_id', $customerId)
                            ->where('produk_id', $request->produk_id)
                            ->where('warna_id', $request->warna_id)
                            ->where('ukuran_id', $request->ukuran_id)
                            ->first();

        if ($existingCart) {
            // Hitung total yang akan dimasukkan
            $totalJumlah = $existingCart->jumlah + $request->quantity;
            
            if ($totalJumlah > $stokTersedia) {
                // Set ke stok maksimum jika melebihi
                $existingCart->jumlah = $stokTersedia;
                $existingCart->subtotal = $existingCart->harga_satuan * $stokTersedia;
                $existingCart->save();
                return redirect()->route('cart.index')->with('warning', 'Jumlah produk disesuaikan dengan stok tersedia ('.$stokTersedia.')');
            }

            $existingCart->jumlah = $totalJumlah;
            $existingCart->subtotal = $existingCart->harga_satuan * $totalJumlah;
            $existingCart->save();
        } else {
            Cart::create([
                'customer_id' => $customerId,
                'produk_id' => $request->produk_id,
                'warna_id' => $request->warna_id,
                'ukuran_id' => $request->ukuran_id,
                'jumlah' => $request->quantity,
                'harga_satuan' => $request->harga_satuan,
                'subtotal' => $request->harga_satuan * $request->quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::with(['ukuran'])->findOrFail($id);

        if ($cartItem->customer_id != Auth::guard('customer')->id()) {
            abort(403);
        }

        // Dapatkan stok tersedia
        $stokTersedia = $cartItem->ukuran ? $cartItem->ukuran->stok : $cartItem->produk->total_stok;
        
        // Jika jumlah yang diminta melebihi stok, set ke stok maksimum
        $jumlahFinal = min($request->jumlah, $stokTersedia);

        // Jika stok habis, hapus item dari cart
        if ($jumlahFinal < 1) {
            $cartItem->delete();
            return redirect()->route('cart.index')->with('warning', 'Produk telah habis dan dihapus dari keranjang');
        }

        $cartItem->jumlah = $jumlahFinal;
        $cartItem->subtotal = $cartItem->harga_satuan * $jumlahFinal;
        $cartItem->save();

        // Beri pesan jika jumlah disesuaikan
        if ($jumlahFinal < $request->jumlah) {
            return redirect()->route('cart.index')->with('warning', 'Jumlah produk disesuaikan dengan stok tersedia ('.$stokTersedia.')');
        }

        return redirect()->route('cart.index')->with('success', 'Jumlah produk berhasil diupdate');
    }

    public function destroy($id)
    {
        $cartItem = Cart::findOrFail($id);

        if ($cartItem->customer_id != Auth::guard('customer')->id()) {
            abort(403);
        }

        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Produk berhasil dihapus dari cart');
    }
}