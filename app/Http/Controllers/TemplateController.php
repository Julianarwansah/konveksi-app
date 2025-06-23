<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Template;
use App\Models\TemplateDetail;
use App\Models\TemplateWarna;
use App\Models\TemplateGambar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = Template::with('details.bahan', 'gambar', 'warna');

        if ($request->has('search') && $request->search != '') {
            $query->where('model', 'like', '%' . $request->search . '%');
        }

        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori', $request->kategori);
        }

        $kategoriList = Template::select('kategori')->distinct()->pluck('kategori');
        $templates = $query->paginate(10)->withQueryString();

        return view('template.index', compact('templates', 'kategoriList'));
    }

    public function create()
    {
        $bahan = Bahan::all();
        $kategoriList = ['Baju', 'Celana', 'Jaket', 'Dress', 'Aksesoris']; // Contoh kategori
        return view('template.create', compact('bahan', 'kategoriList'));
    }

    public function store(Request $request)
    {
        //@dd($request->all());
        $request->validate([
            'model' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string|max:50',
            'harga_estimasi' => 'nullable|numeric',
            'details' => 'required|array',
            'details.*.bahan_id' => 'required|exists:bahan,id',
            'details.*.jumlah' => 'required|numeric|min:0',
            'details.*.harga' => 'required|numeric|min:0',
            'details.*.subtotal' => 'required|numeric|min:0',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'warna' => 'nullable|array',
            'warna.*' => 'string',
        ]);

        $template = Template::create([
            'model' => $request->model,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'harga_estimasi' => $request->harga_estimasi
        ]);

        foreach ($request->details as $detail) {
            TemplateDetail::create([
                'template_id' => $template->id,
                'bahan_id' => $detail['bahan_id'],
                'jumlah' => $detail['jumlah'],
                'harga' => $detail['harga'],
                'subtotal' => $detail['subtotal'],
            ]);
        }

        if ($request->has('warna')) {
            foreach ($request->warna as $warna) {
                $template->warna()->create([
                    'warna' => $warna,
                ]);
            }
        }

        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $gambar) {
                $path = $gambar->store('template', 'public');
                TemplateGambar::create([
                    'template_id' => $template->id,
                    'gambar' => $path,
                ]);
            }
        }

        return redirect()->route('template.index')->with('success', 'Template berhasil dibuat!');
    }

    public function show($id)
    {
        $template = Template::with('details.bahan', 'gambar', 'warna')->findOrFail($id);
        return view('template.show', compact('template'));
    }

    public function edit($id)
    {
        $template = Template::with('details', 'gambar', 'warna')->findOrFail($id);
        $bahan = Bahan::all();
        $kategoriList = ['Baju', 'Celana', 'Jaket', 'Dress', 'Aksesoris'];
        return view('template.edit', compact('template', 'bahan', 'kategoriList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'model' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string|max:50',
            'harga_estimasi' => 'nullable|numeric',
            'details' => 'required|array',
            'details.*.bahan_id' => 'required|exists:bahan,id',
            'details.*.jumlah' => 'required|numeric|min:0',
            'details.*.harga' => 'required|numeric|min:0',
            'details.*.subtotal' => 'required|numeric|min:0',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'warna' => 'nullable|array',
            'warna.*' => 'string',
        ]);

        $template = Template::findOrFail($id);
        $template->update([
            'model' => $request->model,
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
            'harga_estimasi' => $request->harga_estimasi
        ]);

        $template->details()->delete();

        foreach ($request->details as $detail) {
            TemplateDetail::create([
                'template_id' => $template->id,
                'bahan_id' => $detail['bahan_id'],
                'jumlah' => $detail['jumlah'],
                'harga' => $detail['harga'],
                'subtotal' => $detail['subtotal'],
            ]);
        }

        $template->warna()->delete();
        if ($request->has('warna')) {
            foreach ($request->warna as $warna) {
                $template->warna()->create([
                    'warna' => $warna,
                ]);
            }
        }

        if ($request->has('hapus_gambar')) {
            foreach ($request->hapus_gambar as $gambarId) {
                $gambar = TemplateGambar::findOrFail($gambarId);
                Storage::disk('public')->delete($gambar->gambar);
                $gambar->delete();
            }
        }

        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $gambar) {
                $path = $gambar->store('template', 'public');
                TemplateGambar::create([
                    'template_id' => $template->id,
                    'gambar' => $path,
                ]);
            }
        }

        return redirect()->route('template.index')->with('success', 'Template berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $template = Template::findOrFail($id);

        foreach ($template->gambar as $gambar) {
            Storage::disk('public')->delete($gambar->gambar);
        }

        $template->details()->delete();
        $template->warna()->delete();
        $template->gambar()->delete();
        $template->delete();

        return redirect()->route('template.index')->with('success', 'Template berhasil dihapus!');
    }
}