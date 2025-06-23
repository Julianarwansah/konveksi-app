<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Bahan;
use App\Models\ProdukGambar;
use App\Models\ProdukBahan;
use App\Models\ProdukUkuran;
use App\Models\ProdukWarna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Tambahkan ini

class ProdukController extends Controller
{
    /**
     * Menampilkan daftar produk
     */
    public function index(Request $request)
    {
        $query = Produk::with(['gambarDetails', 'produkBahan.bahan', 'warna.ukuran'])
            ->when($request->search, function($q, $search) {
                return $q->where('nama', 'like', "%{$search}%")
                         ->orWhere('kategori', 'like', "%{$search}%");
            })
            ->when($request->kategori, function($q, $kategori) {
                return $q->where('kategori', $kategori);
            });

        $kategoriList = Produk::select('kategori')->distinct()->pluck('kategori');
        $produks = $query->paginate(10);

        return view('produk.index', compact('produks', 'kategoriList'));
    }

    /**
     * Menampilkan form untuk membuat produk baru
     */
    public function create()
    {
        Log::info('Memuat halaman pembuatan produk baru.'); // Log di method create
        $bahans = Bahan::all();
        return view('produk.create', compact('bahans'));
    }

    /**
     * Menyimpan produk baru ke database
     */
    public function store(Request $request)
    {
        Log::info('Memulai proses penyimpanan produk baru.', ['request_data' => $request->all()]); // Log data request

        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:100',
                'kategori' => 'required|string|max:50',
                'is_custom' => 'boolean',
                'harga' => 'required|numeric|min:0',
                'deskripsi' => 'nullable|string',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'bahan.*.bahan_id' => 'required|exists:bahan,id',
                'bahan.*.jumlah' => 'required|numeric|min:0.01',
                'warna.*.warna' => 'required|string|max:255',
                'warna.*.ukuran.*.ukuran' => 'required|string|max:10',
                'warna.*.ukuran.*.stok' => 'required|integer|min:0',
            ]);

            Log::info('Data request berhasil divalidasi.', ['validated_data' => $validated]);

            // Upload gambar utama
            if ($request->hasFile('img')) {
                $validated['img'] = $request->file('img')->store('produk_images', 'public');
                Log::info('Gambar utama berhasil diunggah.', ['path' => $validated['img']]);
            }

            // Buat produk
            $produk = Produk::create($validated);
            Log::info('Produk berhasil dibuat.', ['produk_id' => $produk->id, 'produk_nama' => $produk->nama]);

            // Simpan bahan produk
            if ($request->has('bahan')) {
                foreach ($request->bahan as $bahanData) {
                    $bahan = Bahan::find($bahanData['bahan_id']);
                    if ($bahan) {
                        ProdukBahan::create([
                            'produk_id' => $produk->id,
                            'bahan_id' => $bahan->id,
                            'jumlah' => $bahanData['jumlah'],
                            'harga' => $bahan->harga,
                        ]);
                        Log::info('Bahan produk berhasil disimpan.', ['produk_id' => $produk->id, 'bahan_id' => $bahan->id, 'jumlah' => $bahanData['jumlah']]);
                    } else {
                        Log::warning('Bahan dengan ID tidak ditemukan saat menyimpan produk.', ['bahan_id' => $bahanData['bahan_id']]);
                    }
                }
            }

            // Simpan gambar detail
            if ($request->hasFile('gambar')) {
                foreach ($request->file('gambar') as $gambar) {
                    $path = $gambar->store('produk_gambar', 'public');
                    ProdukGambar::create([
                        'produk_id' => $produk->id,
                        'gambar' => $path,
                    ]);
                    Log::info('Gambar detail berhasil diunggah.', ['produk_id' => $produk->id, 'path' => $path]);
                }
            }

            // Simpan warna dan ukuran
            $totalStok = 0;
            if ($request->has('warna')) {
                foreach ($request->warna as $warnaData) {
                    $warna = ProdukWarna::create([
                        'produk_id' => $produk->id,
                        'warna' => $warnaData['warna'],
                    ]);
                    Log::info('Warna produk berhasil disimpan.', ['produk_id' => $produk->id, 'warna' => $warnaData['warna'], 'warna_id' => $warna->id]);

                    if (isset($warnaData['ukuran'])) {
                        foreach ($warnaData['ukuran'] as $ukuranData) {
                            ProdukUkuran::create([
                                'produk_id' => $produk->id,
                                'warna_id' => $warna->id,
                                'ukuran' => $ukuranData['ukuran'],
                                'stok' => $ukuranData['stok'],
                            ]);
                            $totalStok += $ukuranData['stok'];
                            Log::info('Ukuran produk berhasil disimpan.', ['warna_id' => $warna->id, 'ukuran' => $ukuranData['ukuran'], 'stok' => $ukuranData['stok']]);
                        }
                    } else {
                        Log::warning('Data ukuran tidak ditemukan untuk warna.', ['warna' => $warnaData['warna']]);
                    }
                }
            }

            // Update total stok
            $produk->update(['total_stok' => $totalStok]);
            Log::info('Total stok produk berhasil diperbarui.', ['produk_id' => $produk->id, 'total_stok' => $totalStok]);

            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil dibuat.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validasi gagal saat menyimpan produk.', ['errors' => $e->errors(), 'request_data' => $request->all()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat menyimpan produk.', ['error_message' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'request_data' => $request->all()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan produk. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menampilkan detail produk
     */
    public function show(Produk $produk)
    {
        $produk->load(['gambarDetails', 'produkBahan.bahan', 'warna.ukuran']);
        return view('produk.show', compact('produk'));
    }

    /**
     * Menampilkan form untuk mengedit produk
     */
    public function edit(Produk $produk)
    {
        $produk->load(['gambarDetails', 'produkBahan.bahan', 'warna.ukuran']);
        $bahans = Bahan::all();
        return view('produk.edit', compact('produk', 'bahans'));
    }

    /**
     * Mengupdate produk di database
     */
    public function update(Request $request, Produk $produk)
    {
        // ... (kode update tidak diubah, Anda bisa tambahkan log di sini juga jika diperlukan)
        // ... (kode update sama seperti sebelumnya)
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'is_custom' => 'boolean',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hapus_gambar' => 'nullable|array',
            'hapus_gambar.*' => 'exists:produk_gambar,id',
            'bahan.*.bahan_id' => 'required|exists:bahan,id',
            'bahan.*.jumlah' => 'required|numeric|min:0.01',
            'warna.*.warna' => 'required|string|max:255',
            'warna.*.ukuran.*.ukuran' => 'required|string|max:10',
            'warna.*.ukuran.*.stok' => 'required|integer|min:0',
            'hapus_warna' => 'nullable|array',
            'hapus_warna.*' => 'exists:warna,id',
        ]);

        // Upload gambar utama baru
        if ($request->hasFile('img')) {
            // Hapus gambar lama jika ada
            if ($produk->img) {
                Storage::disk('public')->delete($produk->img);
            }
            $validated['img'] = $request->file('img')->store('produk_images', 'public');
        }

        // Update produk
        $produk->update($validated);

        // Hapus gambar yang dipilih
        if ($request->has('hapus_gambar')) {
            foreach ($request->hapus_gambar as $gambarId) {
                $gambar = ProdukGambar::find($gambarId);
                Storage::disk('public')->delete($gambar->gambar);
                $gambar->delete();
            }
        }

        // Tambah gambar baru
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $gambar) {
                $path = $gambar->store('produk_gambar', 'public');
                ProdukGambar::create([
                    'produk_id' => $produk->id,
                    'gambar' => $path,
                ]);
            }
        }

        // Update bahan produk
        $produk->produkBahan()->delete();
        if ($request->has('bahan')) {
            foreach ($request->bahan as $bahanData) {
                $bahan = Bahan::find($bahanData['bahan_id']);
                
                ProdukBahan::create([
                    'produk_id' => $produk->id,
                    'bahan_id' => $bahan->id,
                    'jumlah' => $bahanData['jumlah'],
                    'harga' => $bahan->harga,
                ]);
            }
        }

        // Hapus warna yang dipilih bersama ukuran terkait
        if ($request->has('hapus_warna')) {
            foreach ($request->hapus_warna as $warnaId) {
                $warna = ProdukWarna::find($warnaId);
                if ($warna) {
                    $warna->ukuran()->delete();
                    $warna->delete();
                }
            }
        }

        // Update warna dan ukuran
        $totalStok = 0;
        if ($request->has('warna')) {
            foreach ($request->warna as $warnaData) {
                if (isset($warnaData['id'])) {
                    $warna = ProdukWarna::find($warnaData['id']);
                    $warna->update(['warna' => $warnaData['warna']]);
                } else {
                    $warna = ProdukWarna::create([
                        'produk_id' => $produk->id,
                        'warna' => $warnaData['warna'],
                    ]);
                }

                // Update ukuran
                if (isset($warnaData['ukuran'])) {
                    foreach ($warnaData['ukuran'] as $ukuranData) {
                        if (isset($ukuranData['id'])) {
                            $ukuran = ProdukUkuran::find($ukuranData['id']);
                            $ukuran->update([
                                'ukuran' => $ukuranData['ukuran'],
                                'stok' => $ukuranData['stok'],
                            ]);
                        } else {
                            ProdukUkuran::create([
                                'produk_id' => $produk->id,
                                'warna_id' => $warna->id,
                                'ukuran' => $ukuranData['ukuran'],
                                'stok' => $ukuranData['stok'],
                            ]);
                        }
                        $totalStok += $ukuranData['stok'];
                    }
                }
            }
        }

        // Update total stok
        $produk->update(['total_stok' => $totalStok]);

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk dari database
     */
    public function destroy(Produk $produk)
    {
        // Hapus gambar utama
        if ($produk->img) {
            Storage::disk('public')->delete($produk->img);
        }

        // Hapus gambar detail
        foreach ($produk->gambarDetails as $gambar) {
            Storage::disk('public')->delete($gambar->gambar);
            $gambar->delete();
        }

        // Hapus relasi lainnya
        $produk->produkBahan()->delete();
        $produk->warna()->delete();

        // Hapus produk
        $produk->delete();

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * API untuk mendapatkan warna berdasarkan produk
     */
    public function getWarnaByProduk(Produk $produk)
    {
        $warna = $produk->warna;
        return response()->json($warna);
    }

    /**
     * API untuk mendapatkan ukuran berdasarkan warna
     */
    public function getUkuranByWarna(ProdukWarna $warna)
    {
        $ukuran = $warna->ukuran;
        return response()->json($ukuran);
    }
}