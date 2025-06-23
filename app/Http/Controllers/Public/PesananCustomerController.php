<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PesananDetail;
use App\Models\Pembayaran;
use App\Models\Cart;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;

class PesananCustomerController extends Controller
{
    // Tampilkan halaman daftar pesanan customer
    public function index()
    {
        $user = Auth::user();

        $pesanan = Pesanan::where('customer_id', $user->id)
            ->with('detailPesanan')
            ->when(request('search'), function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('nama', 'like', '%' . request('search') . '%');
                });
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->get();

        $pesanan->transform(function ($item) {
            $detailPesanan = $item->detailPesanan->first();
            if ($detailPesanan) {
                $item->jenis_pesanan = $detailPesanan->produk_id ? 'produk' : 'custom';
            } else {
                $item->jenis_pesanan = 'tidak diketahui';
            }
            
            $item->status_pembayaran = $item->status_pembayaran ?? 'Tidak Ada Status'; 
            
            return $item;
        });

        if (request('jenis_pesanan')) {
            $pesanan = $pesanan->filter(function ($item) {
                return $item->jenis_pesanan == request('jenis_pesanan');
            });
        }

        return view('public.pesanancustomer.pesanancustomer', compact('pesanan'));
    }

    public function showcustomerpesanan($id)
    {
        $user = Auth::user();

        $pesanan = Pesanan::with(['detailPesanan.produk', 'detailPesanan.custom', 'pembayarans'])
            ->where('customer_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $detailPesanan = $pesanan->detailPesanan->first();
        if ($detailPesanan) {
            $pesanan->jenis_pesanan = $detailPesanan->produk_id ? 'produk' : 'custom';
        } else {
            $pesanan->jenis_pesanan = 'tidak diketahui';
        }

        $pesanan->total_pembayaran = $pesanan->pembayarans->sum('jumlah');
        $pesanan->status_pembayaran = $pesanan->pembayarans->pluck('status')->unique()->implode(', ');

        $totalHarga = $pesanan->total_harga;
        $totalDibayar = $pesanan->pembayarans->where('status', 'Diterima')->sum('jumlah');

        return view('public.pesanancustomer.pesanancustomerdetail', compact(
            'pesanan', 'totalHarga', 'totalDibayar'
        ));
    }

    public function pelunasanForm($id)
    {
        $user = Auth::user();
        $pesanan = Pesanan::where('customer_id', $user->id)
            ->where('id', $id)
            ->where('status_pembayaran', 'DP')
            ->firstOrFail();

        $totalDibayar = $pesanan->pembayarans->where('status', 'Berhasil')->sum('jumlah');
        $sisaPembayaran = $pesanan->total_harga - $totalDibayar;

        $pesanan->sisa_pembayaran = $sisaPembayaran;
        $pesanan->save();

        return view('public.pesanancustomer.pelunasan', compact('pesanan', 'sisaPembayaran'));
    }

    public function storePelunasan(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);
        
        $totalDibayar = $pesanan->pembayarans->where('status', 'Berhasil')->sum('jumlah');
        $sisaPembayaran = $pesanan->total_harga - $totalDibayar;

        $data = $request->validate([
            'jumlah'      => "required|numeric|min:{$sisaPembayaran}|max:{$sisaPembayaran}",
            'metode'      => 'required|in:Transfer Bank,COD,Kartu Kredit,Lainnya',
            'bukti_bayar' => 'nullable|image|max:2048',
            'catatan'     => 'nullable|string',
        ]);

        if($request->hasFile('bukti_bayar')){
            $data['bukti_bayar'] = $request->file('bukti_bayar')
                ->store('bukti_pelunasan', 'public');
        }

        Pembayaran::create([
            'pesanan_id'   => $pesanan->id,
            'jumlah'       => $data['jumlah'],
            'metode'       => $data['metode'],
            'status'       => 'Menunggu Konfirmasi',
            'bukti_bayar'  => $data['bukti_bayar'] ?? null,
            'tanggal_bayar'=> now(),
            'catatan'      => $data['catatan'] ?? null,
            'is_dp'        => false,
        ]);

        $pesanan->status_pembayaran = 'Lunas';
        $pesanan->sisa_pembayaran = 0;
        $pesanan->save();

        return redirect()
            ->route('pesanan.pesanancustomerdetail', $pesanan->id)
            ->with('success', 'Permintaan pelunasan berhasil dikirim, silakan tunggu konfirmasi.');
    }

    /**
     * Memproses pembayaran ulang.
     */
    public function storeBayarUlang(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);

        // Pastikan status pembayaran saat ini adalah 'Pembayaran Gagal'
        if ($pesanan->status_pembayaran !== 'Pembayaran Gagal') {
            return redirect()->back()->with('error', 'Pesanan ini tidak dalam status pembayaran gagal.');
        }

        $type = $request->input('type'); // 'dp' atau 'full'

        $jumlahYangHarusDibayar = 0;
        $is_dp = false;

        if ($type === 'full') {
            $jumlahYangHarusDibayar = $pesanan->total_harga;
            $is_dp = false;
        } elseif ($type === 'dp' && $pesanan->jenis_pesanan === 'custom') {
            $jumlahYangHarusDibayar = $pesanan->total_harga * 0.5;
            $is_dp = true;
        } else {
            return redirect()->back()->with('error', 'Tipe pembayaran ulang tidak valid.');
        }

        $data = $request->validate([
            'jumlah'      => "required|numeric|min:{$jumlahYangHarusDibayar}|max:{$jumlahYangHarusDibayar}",
            'metode'      => 'required|in:Transfer Bank,COD,Kartu Kredit,Lainnya',
            'bukti_bayar' => 'nullable|image|max:2048',
            'catatan'     => 'nullable|string',
        ]);

        if($request->hasFile('bukti_bayar')){
            $data['bukti_bayar'] = $request->file('bukti_bayar')
                ->store('bukti_pembayaran_ulang', 'public');
        }

        DB::beginTransaction();
        try {
            // Buat record pembayaran baru
            Pembayaran::create([
                'pesanan_id'   => $pesanan->id,
                'jumlah'       => $data['jumlah'],
                'metode'       => $data['metode'],
                'status'       => 'Menunggu Konfirmasi',
                'bukti_bayar'  => $data['bukti_bayar'] ?? null,
                'tanggal_bayar'=> now(),
                'catatan'      => $data['catatan'] ?? null,
                'is_dp'        => $is_dp,
            ]);

            // Update status pembayaran pesanan
            if ($is_dp) {
                $pesanan->status_pembayaran = 'DP';
                $pesanan->sisa_pembayaran = $pesanan->total_harga - $data['jumlah'];
            } else {
                $pesanan->status_pembayaran = 'Menunggu Verifikasi'; // Atau 'Lunas' jika langsung terverifikasi
                $pesanan->sisa_pembayaran = 0;
            }
            $pesanan->save();

            DB::commit();

            return redirect()
                ->route('pesanan.pesanancustomerdetail', $pesanan->id)
                ->with('success', 'Permintaan pembayaran ulang berhasil dikirim, silakan tunggu konfirmasi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pembayaran ulang: ' . $e->getMessage());
        }
    }

    public function selesaikanPesanan($id)
    {
        $user = Auth::user();

        // Ambil pesanan dengan relasi pengiriman
        $pesanan = Pesanan::with('pengiriman')
            ->where('customer_id', $user->id)
            ->where('id', $id)
            ->where('status', 'Dalam Pengiriman')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Update status pesanan
            $pesanan->status = 'Selesai Pengiriman';
            $pesanan->tanggal_selesai = now();
            $pesanan->save();

            // Update status pengiriman jika ada
            if ($pesanan->pengiriman) {
                $pesanan->pengiriman->status = 'Selesai Pengiriman';
                $pesanan->pengiriman->save();
            }

            DB::commit();

            return redirect()
                ->route('pesanan.customer')
                ->with('success', 'Pesanan telah berhasil ditandai sebagai selesai.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menyelesaikan pesanan: ' . $e->getMessage());
        }
    }

    public function cetakPembayaranPdf($id)
    {
        $bayar = Pembayaran::with('pesanan')->findOrFail($id);

        $pdf = Pdf::loadView('public.pesanancustomer.pdf_pembayaran', compact('bayar'));
        return $pdf->stream('pembayaran_' . $bayar->id . '.pdf');
    }

    public function bayarUlangForm($id)
    {
        $user = Auth::user();
        
        // Ambil pesanan dengan status pembayaran gagal
        $pesanan = Pesanan::where('customer_id', $user->id)
            ->where('id', $id)
            ->where('status_pembayaran', 'Pembayaran Gagal')
            ->firstOrFail();

        // Tentukan jenis pesanan
        $detailPesanan = $pesanan->detailPesanan->first();
        if ($detailPesanan) {
            $pesanan->jenis_pesanan = $detailPesanan->produk_id ? 'produk' : 'custom';
        } else {
            $pesanan->jenis_pesanan = 'tidak diketahui';
        }

        return view('public.pesanancustomer.bayar_ulang', compact('pesanan'));
    }

    public function prosesBayarUlang($id, $persentase)
    {
        $user = Auth::user();
        $pesanan = Pesanan::where('customer_id', $user->id)
            ->where('id', $id)
            ->where('status_pembayaran', 'Pembayaran Gagal')
            ->firstOrFail();

        // Validasi persentase sesuai jenis pesanan
        $detailPesanan = $pesanan->detailPesanan->first();
        $jenisPesanan = $detailPesanan->produk_id ? 'produk' : 'custom';
        
        if ($jenisPesanan === 'produk' && $persentase != 100) {
            return redirect()->back()->with('error', 'Untuk pesanan produk, hanya pembayaran 100% yang tersedia.');
        }

        if (!in_array($persentase, [50, 100])) {
            return redirect()->back()->with('error', 'Persentase pembayaran tidak valid.');
        }

        // Hitung jumlah yang harus dibayar
        $jumlah = $pesanan->total_harga * ($persentase / 100);

        // Simpan data pembayaran sementara di session
        session()->put('bayar_ulang_data', [
            'pesanan_id' => $pesanan->id,
            'jumlah' => $jumlah,
            'persentase' => $persentase,
            'is_dp' => ($persentase == 50)
        ]);

        return view('public.pesanancustomer.form_bayar_ulang', [
            'pesanan' => $pesanan,
            'jumlah' => $jumlah,
            'persentase' => $persentase
        ]);
    }

    public function simpanBayarUlang(Request $request, $id)
    {
        $user = Auth::user();
        $pesanan = Pesanan::where('customer_id', $user->id)
            ->where('id', $id)
            ->where('status_pembayaran', 'Pembayaran Gagal')
            ->firstOrFail();

        // Ambil data dari session
        $bayarUlangData = session()->get('bayar_ulang_data');
        if (!$bayarUlangData || $bayarUlangData['pesanan_id'] != $id) {
            return redirect()->route('pesanan.customer')->with('error', 'Sesi pembayaran tidak valid.');
        }

        $request->validate([
            'metode' => 'required|in:Transfer Bank,COD,Kartu Kredit,Lainnya',
            'bukti_bayar' => 'required|image|max:2048',
            'catatan' => 'nullable|string',
        ]);

        // Upload bukti bayar
        $buktiBayarPath = $request->file('bukti_bayar')->store('bukti_bayar_ulang', 'public');

        // Buat record pembayaran baru
        Pembayaran::create([
            'pesanan_id' => $pesanan->id,
            'jumlah' => $bayarUlangData['jumlah'],
            'metode' => $request->metode,
            'status' => 'Menunggu Konfirmasi', // Status pembayaran di tabel 'pembayaran' akan menunggu konfirmasi
            'bukti_bayar' => $buktiBayarPath,
            'tanggal_bayar' => now(),
            'catatan' => $request->catatan,
            'is_dp' => $bayarUlangData['is_dp'],
        ]);

        // Update status pesanan
        // Setelah bukti bayar di-upload, status_pembayaran pesanan menjadi "Menunggu Verifikasi"
        $pesanan->status_pembayaran = 'Menunggu Verifikasi'; 
        // Status utama pesanan juga berubah menjadi "Menunggu Konfirmasi"
        $pesanan->status = 'Menunggu Konfirmasi'; 
        $pesanan->save();

        // Hapus data session
        session()->forget('bayar_ulang_data');

        return redirect()
            ->route('pesanan.pesanancustomerdetail', $pesanan->id)
            ->with('success', 'Pembayaran ulang berhasil dikirim, silakan tunggu konfirmasi admin.');
    }
}