<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemasukanController extends Controller
{
    /**
     * Menampilkan semua data pemasukan.
     */
    public function index(Request $request)
    {
        $pemasukan = Pemasukan::with('user')
            ->when($request->filled('start_date'), function ($query) use ($request) {
                $query->whereDate('tanggal', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($query) use ($request) {
                $query->whereDate('tanggal', '<=', $request->end_date);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pemasukan.index', compact('pemasukan'));
    }

    /**
     * Menampilkan form tambah pemasukan.
     */
    public function create()
    {
        return view('pemasukan.create');
    }

    /**
     * Simpan data pemasukan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sumber' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);

        Pemasukan::create([
            'user_id' => Auth::id(),
            'sumber' => $request->sumber,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('pemasukan.index')->with('success', 'Data pemasukan berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail pemasukan tertentu
     */
    public function show(Pemasukan $pemasukan)
    {
        return view('pemasukan.show', compact('pemasukan'));
    }

    /**
     * Menampilkan form edit pemasukan.
     */
    public function edit(Pemasukan $pemasukan)
    {
        return view('pemasukan.edit', compact('pemasukan'));
    }

    /**
     * Update data pemasukan.
     */
    public function update(Request $request, Pemasukan $pemasukan)
    {
        $request->validate([
            'sumber' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);

        $pemasukan->update($request->only([
            'sumber',
            'jumlah',
            'keterangan',
            'tanggal',
        ]));

        return redirect()->route('pemasukan.index')->with('success', 'Data pemasukan berhasil diperbarui.');
    }

    /**
     * Hapus data pemasukan.
     */
    public function destroy(Pemasukan $pemasukan)
    {
        $pemasukan->delete();

        return redirect()->route('pemasukan.index')->with('success', 'Data pemasukan berhasil dihapus.');
    }
}
