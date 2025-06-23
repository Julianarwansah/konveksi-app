<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Custom;
use App\Models\Customer;
use App\Models\PesananDetail;
use App\Models\Template;
use App\Models\TemplateWarna;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PesananCustomController extends Controller
{
    public function create()
    {
        $customers = Customer::all();
        $templates = Template::with('details.bahan')->get();

        return view('pesanan_pakaian_custom.create', compact(
            'customers',
            'templates'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customer,id',
            'total_harga' => 'required|numeric',
            'metode' => 'required|string|in:Transfer Bank,COD,Kartu Kredit,Lainnya',
            'jumlah_pembayaran' => 'required|numeric',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ukuran' => 'required|array',
            'warna' => 'required|array',
            'model' => 'required|array',
            'jumlah' => 'required|array',
            'harga_estimasi' => 'required|array',
            'catatan' => 'nullable|array',
            'img' => 'nullable|array',
            'img.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Membuat pesanan
            $pesanan = Pesanan::create([
                'customer_id' => $request->customer_id,
                'total_harga' => $request->total_harga,
                'status' => 'Menunggu Pembayaran',
                'tanggal_selesai' => null,
            ]);

            // Membuat pembayaran
            $buktiBayarPath = null;
            if ($request->hasFile('bukti_bayar')) {
                $file = $request->file('bukti_bayar');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/bukti_bayar', $fileName);
                $buktiBayarPath = $fileName;
            }

            $pembayaran = Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'jumlah' => $request->jumlah_pembayaran,
                'metode' => $request->metode,
                'status' => 'Menunggu Konfirmasi',
                'bukti_bayar' => $buktiBayarPath,
                'tanggal_bayar' => now(),
                'is_dp' => true,
                'catatan' => null,
            ]);

            // Update pesanan dengan pembayaran_id
            $pesanan->update(['pembayaran_id' => $pembayaran->id]);

            // Loop melalui setiap custom order
            foreach ($request->ukuran as $index => $ukuran) {
                // Handle file upload untuk img (jika ada)
                $imgPath = null;
                if ($request->hasFile('img') && isset($request->file('img')[$index])) {
                    $imgFile = $request->file('img')[$index];
                    $imgFileName = time() . '_' . $imgFile->getClientOriginalName();
                    $imgFile->storeAs('public/custom', $imgFileName);
                    $imgPath = $imgFileName;
                }

                // Membuat custom order
                $custom = Custom::create([
                    'pesanan_id' => $pesanan->id,
                    'customer_id' => $request->customer_id,
                    'template_id' => $request->model[$index] ?? null,
                    'ukuran' => $ukuran,
                    'warna' => $request->warna[$index],
                    'model' => $request->model[$index] ?? null,
                    'jumlah' => $request->jumlah[$index],
                    'harga_estimasi' => $request->harga_estimasi[$index],
                    'status' => 'Belum Diproduksi',
                    'estimasi_selesai' => null,
                    'tanggal_mulai' => null,
                    'catatan' => $request->catatan[$index] ?? null,
                    'img' => $imgPath,
                ]);

                // Simpan ke pesanan_detail
                PesananDetail::create([
                    'pesanan_id' => $pesanan->id,
                    'custom_id' => $custom->id,
                    'jumlah' => $request->jumlah[$index],
                    'ukuran' => $ukuran,
                    'warna' => $request->warna[$index],
                    'harga' => $request->harga_estimasi[$index],
                ]);
            }

            DB::commit();
            return redirect()->route('pesanan.index')->with('success', 'Pesanan custom berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error menyimpan pesanan custom: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $pesanan = Pesanan::with([
            'customer',
            'pembayarans',
            'custom',
            'detailPesanan'
        ])->findOrFail($id);

        $customers = Customer::all();
        $templates = Template::with(['details.bahan', 'warna'])->get();

        return view('pesanan_pakaian_custom.edit', compact(
            'pesanan',
            'customers',
            'templates'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Menunggu Pembayaran,Menunggu Konfirmasi,Pembayaran Diverifikasi,Dalam Antrian Produksi,Dalam Produksi,Selesai Produksi,Sedang Pengemasan,Siap Dikirim,Dalam Pengiriman,Selesai Pengiriman,Selesai',
        ]);

        try {
            DB::beginTransaction();

            $pesanan = Pesanan::findOrFail($id);

            // Update hanya status
            $pesanan->update([
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('pesanan.index')->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error mengupdate status pesanan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function getBahan($modelId)
    {
        $template = Template::with('details.bahan')->findOrFail($modelId);
        return response()->json($template->details);
    }

    public function getWarnaByModel($modelId)
    {
        $warna = TemplateWarna::where('template_id', $modelId)
                    ->select('id', 'warna')
                    ->get();
        
        return response()->json($warna);
    }
}