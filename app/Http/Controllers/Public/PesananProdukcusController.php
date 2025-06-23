<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Pembayaran;
use App\Models\Cart;

class PesananProdukcusController extends Controller
{
    public function checkout()
    {
        $customerId = auth()->user()->id;
        $keranjang = Cart::with(['produk', 'warna', 'ukuran'])
                        ->where('customer_id', $customerId)
                        ->get();

        // Hitung total harga dari keranjang
        $totalHarga = $keranjang->sum(function($item) {
            // jika harga di item null, gunakan harga produk
            $harga = $item->harga ?? $item->produk->harga;
            return $harga * $item->jumlah;
        });

        return view('public.pesanancustomer.checkoutcusproduk', compact('keranjang', 'totalHarga'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        // Validasi
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customer,id',
            'total_harga' => 'required|numeric',
            'status' => 'required|string',
            'tanggal_selesai' => 'nullable|date',
            'detail_pesanan' => 'required|array|min:1',
            'detail_pesanan.*.produk_id' => 'nullable|integer|exists:produk,id',
            'detail_pesanan.*.custom_id' => 'nullable|integer|exists:custom,id',
            'detail_pesanan.*.jumlah' => 'required|integer|min:1',
            'detail_pesanan.*.ukuran' => 'nullable|string',
            'detail_pesanan.*.warna' => 'nullable|string',
            'detail_pesanan.*.harga' => 'required|numeric',
            'pembayaran' => 'required|array|min:1',
            'pembayaran.*.metode' => 'required|string',
            'pembayaran.*.jumlah' => 'required|numeric|min:0',
            'pembayaran.*.status' => 'required|string',
            'pembayaran.*.tanggal_bayar' => 'required|date',
            'pembayaran.*.catatan' => 'nullable|string',
            'pembayaran.*.is_dp' => 'nullable|in:0,1',
            'pembayaran.*.bukti_bayar' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Buat pesanan
            $pesanan = Pesanan::create([
                'customer_id' => $validated['customer_id'],
                'total_harga' => $validated['total_harga'],
                'status' => $validated['status'],
                'tanggal_selesai' => $request->input('tanggal_selesai'),
            ]);

            // Simpan detail pesanan
            foreach ($validated['detail_pesanan'] as $detail) {
                PesananDetail::create([
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $detail['produk_id'] ?? null,
                    'custom_id' => $detail['custom_id'] ?? null,
                    'jumlah' => $detail['jumlah'],
                    'ukuran' => $detail['ukuran'] ?? null,
                    'warna' => $detail['warna'] ?? null,
                    'harga' => $detail['harga'],
                    'sub_total' => $detail['harga'] * $detail['jumlah'],
                ]);
            }

            // Simpan pembayaran
            foreach ($validated['pembayaran'] as $bayar) {
                $pembayaranData = [
                    'pesanan_id' => $pesanan->id,
                    'jumlah' => $bayar['jumlah'],
                    'metode' => $bayar['metode'],
                    'status' => $bayar['status'] ?? 'Menunggu Konfirmasi',
                    'tanggal_bayar' => $bayar['tanggal_bayar'] ?? now(),
                    'catatan' => $bayar['catatan'] ?? null,
                    'is_dp' => $bayar['is_dp'] ?? false,
                ];

                // Handle file upload
                if (isset($bayar['bukti_bayar']) && $bayar['bukti_bayar'] instanceof \Illuminate\Http\UploadedFile) {
                    $file = $bayar['bukti_bayar'];
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('bukti_bayar', $filename, 'public');
                    $pembayaranData['bukti_bayar'] = $path;
                }

                Pembayaran::create($pembayaranData);
            }

            // Kosongkan keranjang setelah pesanan berhasil
            Cart::where('customer_id', $validated['customer_id'])->delete();

            DB::commit();

            return redirect()->route('pesanan.customer')->with('success', 'Pesanan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error: '.$e->getMessage());
            \Log::error('Stack Trace: '.$e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

}
