<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\ProdukUkuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * Menampilkan daftar pembayaran
     */
    public function index(Request $request) // Tambahkan Request sebagai parameter
    {
        
        $pembayaran = Pembayaran::with('pesanan')
            ->when($request->filled('start_date'), function ($query) use ($request) {
                $query->whereDate('tanggal_bayar', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($query) use ($request) {
                $query->whereDate('tanggal_bayar', '<=', $request->end_date);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Tetap sertakan parameter query string untuk pagination
            
        return view('pembayaran.index', compact('pembayaran'));
    }
    /**
     * Menampilkan form untuk membuat pembayaran baru
     */
    public function create()
    {
        $pesanan = Pesanan::whereDoesntHave('pembayaran', function($query) {
            $query->where('is_dp', true);
        })->get();
        
        return view('pembayaran.create', compact('pesanan'));
    }

    /**
     * Menyimpan pembayaran baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanan,id',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|string|max:255',
            'status' => 'nullable|string|in:Menunggu Konfirmasi,Berhasil,Gagal,Dibatalkan',
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tanggal_bayar' => 'required|date',
            'catatan' => 'nullable|string',
            'is_dp' => 'required|boolean',
        ]);

        // Upload bukti bayar
        if ($request->hasFile('bukti_bayar')) {
            $validated['bukti_bayar'] = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        }

        // Set default status jika tidak diisi
        if (!isset($validated['status'])) {
            $validated['status'] = 'Menunggu Konfirmasi';
        }

        Pembayaran::create($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail pembayaran
     */
    public function show(Pembayaran $pembayaran)
    {
        return view('pembayaran.show', compact('pembayaran'));
    }

    /**
     * Menampilkan form untuk mengedit pembayaran
     */
    public function edit(Pembayaran $pembayaran)
    {
        $pesanan = Pesanan::all();
        return view('pembayaran.edit', compact('pembayaran', 'pesanan'));
    }

    /**
     * Memperbarui data pembayaran
     */
    public function update(Request $request, Pembayaran $pembayaran)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanan,id',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|string|max:255',
            'status' => 'required|string|in:Menunggu Konfirmasi,Berhasil,Gagal,Dibatalkan',
            'bukti_bayar' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tanggal_bayar' => 'required|date',
            'catatan' => 'nullable|string',
            'is_dp' => 'required|boolean',
        ]);

        // Handle bukti bayar update
        if ($request->hasFile('bukti_bayar')) {
            // Hapus file lama jika ada
            if ($pembayaran->bukti_bayar) {
                Storage::disk('public')->delete($pembayaran->bukti_bayar);
            }
            $validated['bukti_bayar'] = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        } else {
            // Pertahankan bukti bayar yang lama jika tidak diupdate
            $validated['bukti_bayar'] = $pembayaran->bukti_bayar;
        }

        // Simpan status lama untuk perbandingan
        $oldStatus = $pembayaran->status;
        $newStatus = $validated['status'];

        // Update data pembayaran
        $pembayaran->update($validated);

        // Jika status berubah, update status_pembayaran di pesanan
        if ($oldStatus !== $newStatus) {
            $pesanan = $pembayaran->pesanan;

            if ($newStatus === 'Gagal') {
                // Jika status pembayaran diubah menjadi Gagal
                $pesanan->update([
                    'status_pembayaran' => 'Pembayaran Gagal',
                    'status' => 'Menunggu Pembayaran' // Kembalikan status pesanan
                ]);
            } elseif ($newStatus === 'Berhasil') {
                // Jika status pembayaran diubah menjadi Berhasil
                $totalPembayaran = Pembayaran::where('pesanan_id', $pesanan->id)
                    ->where('status', 'Berhasil')
                    ->sum('jumlah');

                if ($totalPembayaran >= $pesanan->total_harga) {
                    // Jika total pembayaran >= total harga, status Lunas
                    $pesanan->update([
                        'status_pembayaran' => 'Lunas',
                        'sisa_pembayaran' => 0
                    ]);
                } else {
                    // Jika total pembayaran < total harga, status DP
                    $pesanan->update([
                        'status_pembayaran' => 'DP',
                        'sisa_pembayaran' => $pesanan->total_harga - $totalPembayaran
                    ]);
                }

                // Update status pesanan jika masih menunggu konfirmasi
                if ($pesanan->status === 'Menunggu Konfirmasi') {
                    $pesanan->update(['status' => 'Pembayaran Diverifikasi']);
                }

                // Kurangi stok produk jika pembayaran berhasil
                $this->kurangiStokProduk($pesanan->id);
            } elseif ($newStatus === 'Menunggu Konfirmasi') {
                // Jika status dikembalikan ke Menunggu Konfirmasi
                $pesanan->update([
                    'status_pembayaran' => 'Menunggu Verifikasi',
                    'status' => 'Menunggu Konfirmasi'
                ]);
            }
        }

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'status' => 'required|in:Menunggu Konfirmasi,Berhasil,Gagal,Dibatalkan',
            'catatan' => 'nullable|string'
        ]);

        // Simpan status lama untuk perbandingan
        $oldStatus = $pembayaran->status;
        $newStatus = $request->status;

        $pembayaran->update([
            'status' => $newStatus,
            'catatan' => $request->catatan ?? $pembayaran->catatan
        ]);

        // Jika status berubah, update status_pembayaran di pesanan
        if ($oldStatus !== $newStatus) {
            $pesanan = $pembayaran->pesanan;

            if ($newStatus === 'Gagal') {
                // Jika status pembayaran diubah menjadi Gagal
                $pesanan->update([
                    'status_pembayaran' => 'Pembayaran Gagal',
                    'status' => 'Menunggu Pembayaran' // Kembalikan status pesanan
                ]);
            } elseif ($newStatus === 'Berhasil') {
                // Jika status pembayaran diubah menjadi Berhasil
                $totalPembayaran = Pembayaran::where('pesanan_id', $pesanan->id)
                    ->where('status', 'Berhasil')
                    ->sum('jumlah');

                if ($totalPembayaran >= $pesanan->total_harga) {
                    // Jika total pembayaran >= total harga, status Lunas
                    $pesanan->update([
                        'status_pembayaran' => 'Lunas',
                        'sisa_pembayaran' => 0
                    ]);
                } else {
                    // Jika total pembayaran < total harga, status DP
                    $pesanan->update([
                        'status_pembayaran' => 'DP',
                        'sisa_pembayaran' => $pesanan->total_harga - $totalPembayaran
                    ]);
                }

                // Update status pesanan jika masih menunggu konfirmasi
                if ($pesanan->status === 'Menunggu Konfirmasi') {
                    $pesanan->update(['status' => 'Pembayaran Diverifikasi']);
                }

                // Kurangi stok produk jika pembayaran berhasil
                $this->kurangiStokProduk($pesanan->id);
            } elseif ($newStatus === 'Menunggu Konfirmasi') {
                // Jika status dikembalikan ke Menunggu Konfirmasi
                $pesanan->update([
                    'status_pembayaran' => 'Menunggu Verifikasi',
                    'status' => 'Menunggu Konfirmasi'
                ]);
            }
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /**
     * Method untuk mengurangi stok produk berdasarkan pesanan
     */
    protected function kurangiStokProduk($pesananId)
    {
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Ambil semua detail pesanan yang memiliki produk_id (bukan custom_id)
            $details = PesananDetail::where('pesanan_id', $pesananId)
                ->whereNotNull('produk_id')
                ->get();

            foreach ($details as $detail) {
                // Cari produk ukuran berdasarkan produk_id, warna, dan ukuran
                $produkUkuran = ProdukUkuran::where('produk_id', $detail->produk_id)
                    ->whereHas('warna', function($query) use ($detail) {
                        $query->where('warna', $detail->warna);
                    })
                    ->where('ukuran', $detail->ukuran)
                    ->first();

                if ($produkUkuran) {
                    // Kurangi stok
                    $produkUkuran->stok -= $detail->jumlah;
                    
                    // Pastikan stok tidak negatif
                    if ($produkUkuran->stok < 0) {
                        throw new \Exception("Stok tidak mencukupi untuk produk ID {$detail->produk_id}");
                    }
                    
                    $produkUkuran->save();

                    // Update total_stok di tabel produk
                    $produk = $produkUkuran->produk;
                    $produk->total_stok = $produk->produkUkuran()->sum('stok');
                    $produk->save();
                }
            }

            // Commit transaksi jika semua berhasil
            DB::commit();
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Menghapus pembayaran
     */
    public function destroy(Pembayaran $pembayaran)
    {
        // Hapus file bukti bayar jika ada
        if ($pembayaran->bukti_bayar) {
            Storage::disk('public')->delete($pembayaran->bukti_bayar);
        }

        $pembayaran->delete();

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }
    
}