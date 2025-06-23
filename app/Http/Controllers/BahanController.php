<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BahanController extends Controller
{
    /**
     * Menampilkan daftar bahan
     */
    public function index(Request $request)
    {
        $bahans = Bahan::when($request->search, function ($query, $search) {
            return $query->where('nama', 'like', "%{$search}%");
        })
        ->when($request->satuan, function ($query, $satuan) {
            return $query->where('satuan', $satuan);
        })
        ->orderBy('nama')
        ->paginate(10);

        // Ambil daftar satuan unik untuk dropdown
        $satuans = Bahan::select('satuan')->distinct()->pluck('satuan');

        return view('bahan.index', compact('bahans', 'satuans'));
    }

    /**
     * Menampilkan form untuk membuat bahan baru
     */
    public function create()
    {
        return view('bahan.create');
    }

    /**
     * Menyimpan bahan baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'satuan' => 'required|string|max:20',
            'stok' => 'required|numeric|min:0|decimal:0,2',
            'harga' => 'required|numeric|min:0|decimal:0,2',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('bahan_images', 'public');
            $validated['img'] = $path;
        }

        Bahan::create($validated);

        return redirect()->route('bahan.index')
            ->with('success', 'Bahan berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail bahan
     */
    public function show(Bahan $bahan)
    {
        return view('bahan.show', compact('bahan'));
    }

    /**
     * Menampilkan form untuk mengedit bahan
     */
    public function edit(Bahan $bahan)
    {
        return view('bahan.edit', compact('bahan'));
    }

    /**
     * Mengupdate bahan di database
     */
    public function update(Request $request, Bahan $bahan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'satuan' => 'required|string|max:20',
            'stok' => 'required|numeric|min:0|decimal:0,2',
            'harga' => 'required|numeric|min:0|decimal:0,2',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('img')) {
            // Delete old image if exists
            if ($bahan->img) {
                Storage::disk('public')->delete($bahan->img);
            }
            
            $path = $request->file('img')->store('bahan_images', 'public');
            $validated['img'] = $path;
        }

        $bahan->update($validated);

        return redirect()->route('bahan.index')
            ->with('success', 'Bahan berhasil diperbarui.');
    }

    /**
     * Menghapus bahan dari database
     */
    public function destroy(Bahan $bahan)
    {
        // Delete image if exists
        if ($bahan->img) {
            Storage::disk('public')->delete($bahan->img);
        }

        $bahan->delete();

        return redirect()->route('bahan.index')
            ->with('success', 'Bahan berhasil dihapus.');
    }

    /**
     * API untuk mendapatkan data bahan (jika diperlukan)
     */
    public function apiIndex()
    {
        $bahans = Bahan::select('id', 'nama', 'satuan', 'stok', 'harga')
            ->orderBy('nama')
            ->get();

        return response()->json($bahans);
    }

    /**
     * Cek stok bahan
     */
    public function checkStock(Bahan $bahan)
    {
        $alert = $bahan->stok <= $bahan->min_stok 
            ? ['type' => 'warning', 'message' => 'Stok bahan hampir habis!']
            : ['type' => 'success', 'message' => 'Stok bahan aman.'];

        return response()->json([
            'stok' => $bahan->stok,
            'min_stok' => $bahan->min_stok,
            'alert' => $alert
        ]);
    }
}