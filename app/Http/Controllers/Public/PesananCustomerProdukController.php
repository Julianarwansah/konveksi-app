<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PesananCustomerProdukController extends Controller
{
    /**
     * Menampilkan form untuk membuat pesanan baru dari cart
     */
    public function create()
    {
        // Ambil cart items untuk customer yang login
        $customerId = Auth::guard('customer')->id();
        $cartItems = Cart::with(['produk', 'warna', 'ukuran'])
                        ->where('customer_id', $customerId)
                        ->get();

        // Hitung total harga
        $totalHarga = $cartItems->sum('subtotal');

        return view('public.pesanancustomer.create', [
            'cartItems' => $cartItems,
            'totalHarga' => $totalHarga
        ]);
    }

    /**
     * Menyimpan pesanan baru dari cart ke database
     */
    public function store(Request $request)
    {
        $customerId = Auth::guard('customer')->id();
        
        // Validasi data
        $request->validate([
            'metode_pembayaran' => 'required|string',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Ambil items dari cart
        $cartItems = Cart::with(['produk', 'warna', 'ukuran'])
                        ->where('customer_id', $customerId)
                        ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang belanja kosong.');
        }

        // Hitung total harga
        $totalHarga = $cartItems->sum('subtotal');

        // Buat pesanan baru
        $pesanan = Pesanan::create([
            'customer_id' => $customerId,
            'total_harga' => $totalHarga,
            'sisa_pembayaran' => $totalHarga,
            'status' => 'Menunggu Pembayaran',
        ]);

        // Buat detail pesanan untuk setiap item di cart
        foreach ($cartItems as $item) {
            PesananDetail::create([
                'pesanan_id' => $pesanan->id,
                'produk_id' => $item->produk_id,
                'jumlah' => $item->jumlah,
                'ukuran' => $item->ukuran->ukuran ?? null,
                'warna' => $item->warna->warna ?? null,
                'harga' => $item->harga_satuan,
            ]);
        }

        // Buat pembayaran
        $pembayaranData = [
            'pesanan_id' => $pesanan->id,
            'jumlah' => $totalHarga,
            'metode' => $request->metode_pembayaran,
            'status' => 'Menunggu Konfirmasi',
            'catatan' => $request->catatan,
            'tanggal_bayar' => $request->tanggal_bayar ?? Carbon::now()->toDateString(),
        ];

        // Upload bukti bayar jika ada
        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/bukti_pembayaran', $filename);
            $pembayaranData['bukti_bayar'] = 'bukti_pembayaran/' . $filename;
        }

        // Simpan data pembayaran
        $pembayaran = Pembayaran::create($pembayaranData);

        // Update pesanan agar menyimpan ID pembayaran
        $pesanan->update([
            'pembayaran_id' => $pembayaran->id,
        ]);

        // Kosongkan cart setelah pesanan dibuat
        Cart::where('customer_id', $customerId)->delete();

        return redirect()->route('public.pesanan.thankyou')
                         ->with('success', 'Pesanan berhasil dibuat. Silakan menunggu konfirmasi pembayaran.');
    }
}
