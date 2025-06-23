<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Pastikan ini ada di sini!

use App\Models\Customer;
use App\Models\Template;
use App\Models\TemplateWarna;
use App\Models\Produk;
use App\Models\Bahan;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use App\Models\Custom;
use App\Models\PesananDetail;

class PesananCustomerCustomController extends Controller
{
    public function showCheckoutForm(Request $request)
    {
        //dd($request->all());
        $template = Template::with(['warna', 'details.bahan'])->findOrFail($request->template_id);
        $selectedData = [
            'template_id' => $request->template_id,
            'warna_id'    => $request->warna_id,    // <-- gunakan ini
            'quantity'    => $request->quantity ?? 1,
        ];
        $templates = Template::all();
        $bahans = Bahan::all();

        return view('public.pesanancustomer.checkoutcustom', compact(
            'template', 'selectedData', 'templates', 'bahans', 'selectedData'
        ));
    }
    /**
     * Form awal pemilihan template
     */
    public function create()
    {
        $customers = Customer::all();
        $produks   = Produk::all();
        $templates = Template::with(['details.bahan', 'warna'])->get();
        $bahans    = Bahan::all();

        return view('public.pesanancustomer.create', compact(
            'customers', 'produks', 'templates', 'bahans'
        ));
    }

    /**
     * Tampilkan halaman checkout detail custom
     */
    public function checkout(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'template_id' => 'required|exists:template,id',
            'warna_id'    => 'nullable|exists:template_warna,id',
        ]);

        // Ambil data template lengkap
        $template = Template::with(['warna', 'gambar'])->findOrFail($data['template_id']);

        // Ambil data warna jika ada
        $warna = $data['warna_id'] ? TemplateWarna::find($data['warna_id']) : null;

        return view('public.pesanancustomer.checkoutcustom', [
            'template' => $template,
            'warna'    => $warna,
        ]);
    }

    /**
     * Simpan pesanan custom dari form checkout
     */
    public function store(Request $request)
    {
        //dd($request->all());
        // 1. Validasi input
        $request->validate([
            'customer_id'         => 'required|exists:customer,id',
            'total_harga'         => 'required|numeric|min:0',
            'metode'              => 'required|in:Transfer Bank,COD,Kartu Kredit,Lainnya',
            'jumlah_pembayaran'   => 'required|numeric|min:0',
            'bukti_bayar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Custom items
            'model'               => 'required|array',
            'model.*'             => 'exists:template,id',
            'warna_id'            => 'required|array',
            'warna_id.*'          => 'exists:template_warna,id',
            'ukuran.*'            => 'required|string|max:50',
            'jumlah.*'            => 'required|integer|min:1',
            'harga_estimasi.*'    => 'required|numeric|min:0',
            'catatan.*'           => 'nullable|string|max:500',
            'img'                 => 'nullable|array',
            'img.*'               => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        //dd($request->all());

        DB::beginTransaction();
        try {
            // 2. Simpan Pesanan Utama
            $pesanan = Pesanan::create([
                'customer_id'       => $request->customer_id,
                'total_harga'       => $request->total_harga,
                'sisa_pembayaran'   => $request->total_harga,
                'status'            => 'Menunggu Pembayaran',
                'status_pembayaran' => 'Menunggu Verifikasi',
                'tanggal_selesai'   => null,
            ]);

            // Log status pesanan setelah pembuatan (sebelum transaksi di-commit)
            Log::info('Status Pesanan Setelah Create: ' . $pesanan->status . ' (ID Pesanan: ' . $pesanan->id . ')');

            // 3. Upload bukti bayar jika ada
            $buktiBayarPath = null;
            if ($file = $request->file('bukti_bayar')) {
                $buktiBayarPath = $file->store('bukti_bayar', 'public');
            }

            // 4. Simpan Pembayaran DP
            $pembayaran = Pembayaran::create([
                'pesanan_id'    => $pesanan->id,
                'jumlah'        => $request->jumlah_pembayaran,
                'metode'        => $request->metode,
                'status'        => 'Menunggu Konfirmasi',
                'bukti_bayar'   => $buktiBayarPath,
                'tanggal_bayar' => now(),
                'is_dp'         => true,
                'catatan'       => null,
            ]);
            // set foreign key pembayaran_id
            $pesanan->update(['pembayaran_id' => $pembayaran->id]);

            // Log status pesanan setelah update pembayaran_id (masih dalam transaksi)
            Log::info('Status Pesanan Setelah Update Pembayaran ID: ' . $pesanan->status . ' (ID Pesanan: ' . $pesanan->id . ')');


            // 5. Loop untuk setiap custom‐item
            foreach ($request->model as $i => $templateId) {
                $template = Template::findOrFail($templateId);
                $warnaObj = TemplateWarna::find($request->warna_id[$i]);

                // upload gambar desain per item
                $imgPath = null;
                if ($request->hasFile("img.$i")) {
                    $file = $request->file("img.$i");
                    $imgPath = $file->store('custom', 'public');
                }

                // a) Buat record Custom
                $custom = Custom::create([
                    'pesanan_id'       => $pesanan->id,
                    'customer_id'      => $request->customer_id,
                    'template_id'      => $template->id,
                    'model'            => $template->model,
                    'ukuran'           => $request->ukuran[$i],
                    'warna'            => $warnaObj?->warna,
                    'jumlah'           => $request->jumlah[$i],
                    'harga_estimasi'   => $request->harga_estimasi[$i],
                    'status'           => 'Belum Diproduksi', // Status untuk custom item, BUKAN status pesanan utama
                    'estimasi_selesai' => null,
                    'tanggal_mulai'    => null,
                    'catatan'          => $request->catatan[$i] ?? null,
                    'img'              => $imgPath,
                ]);

                // b) Buat record PesananDetail
                PesananDetail::create([
                    'pesanan_id' => $pesanan->id,
                    'custom_id'  => $custom->id,
                    'jumlah'     => $custom->jumlah,
                    'ukuran'     => $custom->ukuran,
                    'warna'      => $custom->warna,
                    'harga'      => $custom->harga_estimasi,
                    // sub_total dihitung otomatis oleh DB
                ]);

                // Log status pesanan di dalam loop custom item (masih dalam transaksi)
                Log::info("Status Pesanan dalam loop item $i: " . $pesanan->status . ' (ID Pesanan: ' . $pesanan->id . ')');
            }

            DB::commit();

            // Catat status pesanan setelah COMMIT dan mengambil ulang dari database
            // Ini adalah titik paling krusial untuk melihat apakah status berubah setelah transaksi selesai.
            $pesananSetelahCommit = Pesanan::find($pesanan->id);
            Log::info('Status Pesanan SETELAH COMMIT & Ambil Ulang: ' . $pesananSetelahCommit->status . ' (ID Pesanan: ' . $pesananSetelahCommit->id . ')');

            return redirect()
                ->route('public.pesanan.custom.thankyou')
                ->with('success', 'Pesanan custom berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error menyimpan pesanan custom: ' . $e->getMessage());
            return back()
                ->with('error', 'Gagal memproses pesanan: ' . $e->getMessage())
                ->withInput();
        }
    }
}