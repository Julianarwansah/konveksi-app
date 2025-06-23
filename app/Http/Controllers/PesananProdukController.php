<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use App\Models\Customer;
use Illuminate\Http\Request;

class PesananProdukController extends Controller
{
    public function create()
    {
        $produk = Produk::all();
        $customers = Customer::all();
        return view('pesanan_produk.create', compact('produk', 'customers'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'customer_id' => 'required|exists:customer,id',
            'produk_id' => 'required|array',
            'produk_id.*' => 'exists:produk,id',
            'ukuran' => 'required|array',
            'ukuran.*' => 'string',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
            'harga_satuan' => 'required|array',
            'harga_satuan.*' => 'numeric|min:0',
            'warna' => 'required|array',
            'warna.*' => 'string',
            'status' => 'required|string|in:Menunggu Pembayaran,Diproses,Selesai,Dibatalkan',
            'metode_pembayaran' => 'required|string|in:Transfer Bank,COD,Kartu Kredit',
            'pembayaran_manual' => 'nullable|numeric|min:0',
            'sisa_pembayaran' => 'nullable|numeric|min:0',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Simpan pesanan
        $pesanan = Pesanan::create([
            'customer_id' => $request->customer_id,
            'total_harga' => 0,
            'status' => $request->status,
            'metode_pembayaran' => $request->metode_pembayaran,
            'pembayaran_manual' => $request->pembayaran_manual ?? 0,
            'sisa_pembayaran' => $request->sisa_pembayaran ?? 0,
            'bukti_pembayaran' => null,
        ]);

        // Simpan detail pesanan dan hitung total harga
        $totalHarga = 0;
        foreach ($request->produk_id as $index => $produkId) {
            $jumlah = $request->jumlah[$index];
            $hargaSatuan = $request->harga_satuan[$index];
            $ukuran = $request->ukuran[$index];
            $warna = $request->warna[$index];
            $subtotal = $hargaSatuan * $jumlah;

            PesananDetail::create([
                'pesanan_id' => $pesanan->id,
                'produk_id' => $produkId,
                'pakaian_custom_id' => null,
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuan,
                'ukuran' => $ukuran,
                'warna' => $warna,
            ]);

            $totalHarga += $subtotal;
        }

        // Update total harga pesanan
        $pesanan->update([
            'total_harga' => $totalHarga,
        ]);

        // Upload bukti pembayaran jika ada
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/bukti_pembayaran', $fileName);
            $pesanan->update(['bukti_pembayaran' => $fileName]);
        }

        return redirect()->route('pesanan.index')->with('success', 'Pesanan produk berhasil dibuat.');
    }

    public function edit($id)
    {
        $pesanan = Pesanan::with(['customer', 'pesananDetails.produk'])->findOrFail($id);
        return view('pesanan_produk.edit', compact('pesanan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Menunggu Pembayaran,Menunggu Konfirmasi,Pembayaran Diverifikasi,Dalam Antrian Produksi,Dalam Produksi,Selesai Produksi,Sedang Pengemasan,Siap Dikirim,Dalam Pengiriman,Selesai Pengiriman,Selesai',
        ]);

        try {
            $pesanan = Pesanan::findOrFail($id);
            
            $pesanan->update([
                'status' => $request->status,
            ]);

            return redirect()->route('pesanan.index')->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Error mengupdate status pesanan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        PesananDetail::where('pesanan_id', $id)->delete();
        $pesanan->delete();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}