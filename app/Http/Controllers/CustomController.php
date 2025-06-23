<?php

namespace App\Http\Controllers;

use App\Models\Custom;
use App\Models\Bahan;
use App\Models\Pesanan;
use App\Models\Customer;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomController extends Controller
{
    public function index(Request $request)
    {
        $query = Custom::with(['customer', 'template'])
            ->when($request->search, function($q, $search) {
                return $q->where('model', 'like', "%{$search}%")
                       ->orWhere('status', 'like', "%{$search}%")
                       ->orWhereHas('customer', function($q) use ($search) {
                           $q->where('nama', 'like', "%{$search}%");
                       });
            })
            ->when($request->status, function($q, $status) {
                return $q->where('status_produksi', $status);
            });


        $statusList = Custom::getStatuses();
        $customs = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('custom.index', compact('customs', 'statusList'));
    }

    public function create()
    {
        $statusList = Custom::getStatuses();
        $customers = Customer::all();
        $templates = Template::all();
        $bahans = Bahan::all();
        
        return view('custom.create', compact('statusList','customers', 'templates', 'bahans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'template_id' => 'nullable|exists:templates,id',
            'ukuran' => 'required|string|max:50',
            'warna' => 'required|string|max:50',
            'model' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'harga_estimasi' => 'required|numeric|min:0',
            'status' => 'required|in:' . implode(',', Custom::getStatuses()),
            'estimasi_selesai' => 'required|date',
            'tanggal_mulai' => 'nullable|date',
            'catatan' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('img')) {
            $validated['img'] = $request->file('img')->store('custom_images', 'public');
        }

        Custom::create($validated);

        return redirect()->route('custom.index')
            ->with('success', 'Custom produk berhasil dibuat.');
    }

    public function show(Custom $custom)
    {
        $custom->load(['customer', 'template']);
        return view('custom.show', compact('custom'));
    }

    public function edit(Custom $custom)
    {
        $customers = Customer::all();
        $templates = Template::all();
        $bahans = Bahan::all();
        
        return view('custom.edit', compact('custom', 'customers', 'templates', 'bahans'));
    }

    public function update(Request $request, Custom $custom)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'template_id' => 'nullable|exists:templates,id',
            'ukuran' => 'required|string|max:50',
            'warna' => 'required|string|max:50',
            'model' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'harga_estimasi' => 'required|numeric|min:0',
            'status' => 'required|in:' . implode(',', Custom::getStatuses()),
            'estimasi_selesai' => 'required|date',
            'tanggal_mulai' => 'nullable|date',
            'catatan' => 'nullable|string',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('img')) {
            if ($custom->img) {
                Storage::disk('public')->delete($custom->img);
            }
            $validated['img'] = $request->file('img')->store('custom_images', 'public');
        }

        $custom->update($validated);

        return redirect()->route('custom.index')
            ->with('success', 'Custom produk berhasil diperbarui.');
    }

    public function destroy(Custom $custom)
    {
        if ($custom->img) {
            Storage::disk('public')->delete($custom->img);
        }

        $custom->delete();

        return redirect()->route('custom.index')
            ->with('success', 'Custom produk berhasil dihapus.');
    }

    public function updateStatus(Request $request, Custom $custom)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', Custom::getStatuses()),
        ]);

        $custom->update(['status' => $request->status]);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function createPesanan(Custom $custom)
    {
        $custom->load(['customer', 'template']);
        return view('custom.create_pesanan', compact('custom'));
    }

    public function storePesanan(Request $request, Custom $custom)
    {
        $request->validate([
            'tanggal_pesanan' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $pesanan = Pesanan::create([
            'customer_id' => $custom->customer_id,
            'tanggal_pesanan' => $request->tanggal_pesanan,
            'status' => 'draft',
            'catatan' => $request->catatan,
        ]);

        $pesanan->details()->create([
            'custom_id' => $custom->id,
            'jumlah' => $custom->jumlah,
            'harga' => $custom->harga_estimasi,
            'subtotal' => $custom->harga_estimasi * $custom->jumlah,
        ]);

        $custom->update(['pesanan_id' => $pesanan->id]);

        return redirect()->route('pesanan.show', $pesanan)
            ->with('success', 'Pesanan berhasil dibuat dari custom produk.');
    }
}
