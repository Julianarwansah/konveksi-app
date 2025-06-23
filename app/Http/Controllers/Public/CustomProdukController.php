<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\TemplateWarna;
use Illuminate\Http\Request;

class CustomProdukController extends Controller
{
    public function index(Request $request)
    {
        // Kode sebelumnya tetap sama
        $categories = Template::selectRaw('TRIM(kategori) as kategori')
                      ->whereNotNull('kategori')
                      ->distinct()
                      ->pluck('kategori');
        
        $query = Template::with(['gambar', 'warna']);
        
        if ($request->has('category') && $request->category != '') {
            $query->where('kategori', $request->category);
        }
        
        $templates = $query->get();
        $activeCategory = $request->category ?? '';
        
        return view('public.customprodukshop', compact('templates', 'categories', 'activeCategory'));
    }

    public function show($id)
    {
        $template = Template::with(['gambar', 'warna', 'details.bahan'])->findOrFail($id);
        
        $relatedTemplates = Template::where('id', '!=', $id)
            ->with(['gambar'])
            ->inRandomOrder()
            ->limit(4)
            ->get();
            
        // Tambahkan data yang akan dikirim ke view
        $selectedData = [
            'template_id' => $template->id,
            'warna_id' => null, // Default null, bisa diisi jika ada warna default
            'quantity' => 1 // Default quantity
        ];
            
        return view('public.customproduksdetailshop', compact(
            'template', 
            'relatedTemplates',
            'selectedData' // Kirim variabel ke view
        ));
    }
}