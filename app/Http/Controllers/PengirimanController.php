<?php

namespace App\Http\Controllers;

use App\Models\Pengiriman;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengirimanController extends Controller
{
    /**
     * Menampilkan daftar pengiriman beserta data pesanan dengan pagination dan filter search.
     */
    public function index(Request $request)
    {
        $query = Pengiriman::with('pesanan.customer')->latest();

        if ($search = $request->get('search')) {
            $query->where('pesanan_id', $search)
                  ->orWhere('kurir', 'like', "%{$search}%")
                  ->orWhere('resi', 'like', "%{$search}%");
        }

        $pengirimans = $query->paginate(10)->withQueryString();

        return view('pengiriman.index', compact('pengirimans'));
    }

    /**
     * Form untuk membuat pengiriman baru.
     * Hanya tampilkan pesanan yang statusnya "Siap Dikirim".
     */
    public function create()
    {
        $originalPesanans = Pesanan::with('customer')
    ->where('status', 'Siap Dikirim')
    ->where('status_pembayaran', 'Lunas')
    ->orderBy('id')
    ->get();

$alamatCustomer = $originalPesanans->mapWithKeys(function ($pesanan) {
    return [$pesanan->id => $pesanan->customer->alamat ?? ''];
});

return view('pengiriman.create', [
    'pesanans' => $originalPesanans,
    'alamatCustomer' => $alamatCustomer
]);

    }


    /**
     * Simpan pengiriman baru.
     * Validasi pesanan_id harus ada dan statusnya "Siap Dikirim".
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id' => [
                'required',
                'exists:pesanan,id',
                function($attribute, $value, $fail) {
                    $pesanan = Pesanan::find($value);
                    if (!$pesanan || $pesanan->status !== 'Siap Dikirim') {
                        $fail("Pesanan yang dipilih belum berstatus Siap Dikirim.");
                    }
                },
            ],
            'alamat'     => 'required|string|max:255',
            'kurir'      => 'required|string|max:100',
            'resi'       => 'nullable|string|max:100',
            'foto_resi'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'biaya'      => 'required|numeric|min:0',
            'status'     => 'required|string|in:Dalam Pengiriman,Selesai Pengiriman',
        ]);

        DB::transaction(function () use ($request, $validated) {
            // Simpan data pengiriman
            if ($request->hasFile('foto_resi')) {
                $validated['foto_resi'] = $request->file('foto_resi')
                                                ->store('resi', 'public');
            }
            
            $pengiriman = Pengiriman::create($validated);
            
            // Update status pesanan menjadi "Dalam Pengiriman"
            $pesanan = Pesanan::find($validated['pesanan_id']);
            $pesanan->status = 'Dalam Pengiriman';
            $pesanan->save();
        });

        return redirect()
            ->route('pengiriman.index')
            ->with('success', 'Pengiriman berhasil ditambahkan dan status pesanan diperbarui.');
    }

    /**
     * Detail sebuah pengiriman.
     */
    public function show(Pengiriman $pengiriman)
    {
        $pengiriman->load('pesanan.customer');
        return view('pengiriman.show', compact('pengiriman'));
    }

    /**
     * Form edit pengiriman.
     * Pesanan yang bisa dipilih tetap yang sudah "Siap Dikirim".
     */
    public function edit(Pengiriman $pengiriman)
    {
        $pesanans = Pesanan::with('customer')
            ->where('status', 'Siap Dikirim')
            ->orderBy('id')
            ->get();

        return view('pengiriman.edit', compact('pengiriman', 'pesanans'));
    }

    /**
     * Update pengiriman.
     * Validasi sama seperti di store().
     */
    public function update(Request $request, Pengiriman $pengiriman)
    {
        $validated = $request->validate([
            'pesanan_id' => [
                'required',
                'exists:pesanan,id',
                function($attribute, $value, $fail) {
                    $pesanan = Pesanan::find($value);
                    if (!$pesanan || $pesanan->status !== 'Siap Dikirim') {
                        $fail("Pesanan yang dipilih belum berstatus Siap Dikirim.");
                    }
                },
            ],
            'alamat'     => 'required|string|max:255',
            'kurir'      => 'required|string|max:100',
            'resi'       => 'nullable|string|max:100',
            'foto_resi'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'biaya'      => 'required|numeric|min:0',
            'status'     => 'required|string|in:Dalam Pengiriman,Selesai Pengiriman',
        ]);

        DB::transaction(function () use ($request, $validated, $pengiriman) {
            if ($request->hasFile('foto_resi')) {
                if ($pengiriman->foto_resi) {
                    Storage::disk('public')->delete($pengiriman->foto_resi);
                }
                $validated['foto_resi'] = $request->file('foto_resi')
                                                ->store('resi', 'public');
            }
            
            $pengiriman->update($validated);
            
            // Update status pesanan jika status pengiriman berubah
            $pesanan = Pesanan::find($validated['pesanan_id']);
            $pesanan->status = $validated['status'] == 'Selesai Pengiriman' 
                ? 'Selesai Pengiriman' 
                : 'Dalam Pengiriman';
            $pesanan->save();
        });

        return redirect()
            ->route('pengiriman.index')
            ->with('success', 'Pengiriman berhasil diperbarui dan status pesanan diperbarui.');
    }

    /**
     * Hapus pengiriman & file resi.
     */
    public function destroy(Pengiriman $pengiriman)
    {
        DB::transaction(function () use ($pengiriman) {
            if ($pengiriman->foto_resi) {
                Storage::disk('public')->delete($pengiriman->foto_resi);
            }
            $pengiriman->delete();
        });

        return redirect()
            ->route('pengiriman.index')
            ->with('success', 'Pengiriman berhasil dihapus.');
    }
}
