<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    /**
     * Menampilkan daftar pengeluaran.
     */
     public function index(Request $request) // Tambahkan Request sebagai parameter
    {
        $pengeluaran = Pengeluaran::with('user')
                ->when($request->filled('start_date'), function ($query) use ($request) {
                    $query->whereDate('tanggal', '>=', $request->start_date);
                })
                ->when($request->filled('end_date'), function ($query) use ($request) {
                    $query->whereDate('tanggal', '<=', $request->end_date);
                })
                ->latest()
                ->paginate(10)
                ->withQueryString();
        return view('pengeluaran.index', compact('pengeluaran'));
    }

    /**
     * Menampilkan form untuk menambahkan data pengeluaran.
     */
    public function create()
    {
        return view('pengeluaran.create');
    }

    /**
     * Menyimpan data pengeluaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);

        Pengeluaran::create([
            'user_id' => Auth::id(),
            'kategori' => $request->kategori,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('pengeluaran.index')->with('success', 'Data pengeluaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail 1 data pengeluaran.
     */
    public function show(Pengeluaran $pengeluaran)
    {
        return view('pengeluaran.show', compact('pengeluaran'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(Pengeluaran $pengeluaran)
    {
        return view('pengeluaran.edit', compact('pengeluaran'));
    }

    /**
     * Proses update pengeluaran.
     */
    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);

        $pengeluaran->update($request->only([
            'kategori',
            'jumlah',
            'keterangan',
            'tanggal',
        ]));

        return redirect()->route('pengeluaran.index')->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    /**
     * Menghapus data pengeluaran.
     */
    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();

        return redirect()->route('pengeluaran.index')->with('success', 'Data pengeluaran berhasil dihapus.');
    }
}
