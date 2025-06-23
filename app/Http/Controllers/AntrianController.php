<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AntrianController extends Controller
{
    public function index(Request $request)
    {
        $query = Antrian::with(['custom' => function($query) {
            $query->with(['customer', 'template', 'pesanan']);
        }]);

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $antrians = $query->orderBy('tanggal', 'asc')->get();

        return view('antrian.index', [
            'antrians' => $antrians,
            'statuses' => Antrian::getStatusOptions()
        ]);
    }

    public function create()
    {
        $customs = Custom::whereDoesntHave('antrian')
            ->where('status', Custom::STATUS_NOT_PRODUCED)
            ->whereHas('pesanan', function($query) {
                $query->whereIn('status_pembayaran', ['DP', 'Lunas']);
            })
            ->with(['customer', 'template', 'pesanan'])
            ->get();

        return view('antrian.create', [
            'customs' => $customs,
            'today' => now()->format('Y-m-d')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'custom_id' => 'required|exists:custom,id',
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
        ]);

        try {
            $antrian = Antrian::create([
                'custom_id' => $validated['custom_id'],
                'tanggal' => $validated['tanggal'],
                'jumlah' => $validated['jumlah'],
                'status' => Antrian::STATUS_DALAM_ANTRIAN_PRODUKSI,
            ]);

            Custom::where('id', $validated['custom_id'])
                ->update(['status' => Custom::STATUS_IN_QUEUE]);

            return redirect()->route('antrian.index')
                ->with('success', 'Antrian produksi berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal menambahkan antrian: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $antrian = Antrian::with(['custom.customer', 'custom.template', 'custom.pesanan'])
            ->findOrFail($id);

        return view('antrian.show', [
            'antrian' => $antrian,
            'statuses' => Antrian::getStatusOptions()
        ]);
    }

    public function edit($id)
    {
        $antrian = Antrian::with(['custom'])->findOrFail($id);
        
        $customs = Custom::where(function($query) use ($antrian) {
            // Custom yang tidak memiliki antrian dengan status tertentu
            $query->whereDoesntHave('antrian')
                ->whereIn('status', [Custom::STATUS_NOT_PRODUCED, Custom::STATUS_IN_QUEUE]);
        })
        ->orWhere('id', $antrian->custom_id) // Selalu sertakan custom yang sedang diedit
        ->whereHas('pesanan', function($query) {
            $query->whereIn('status_pembayaran', ['DP', 'Lunas']);
        })
        ->with(['customer', 'template', 'pesanan'])
        ->get();

        return view('antrian.edit', [
            'antrian' => $antrian,
            'customs' => $customs,
            'statuses' => Antrian::getStatusOptions()
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'custom_id' => 'required|exists:custom,id',
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'status' => ['required', Rule::in(array_keys(Antrian::getStatusOptions()))]
        ]);

        try {
            DB::transaction(function() use ($id, $validated) {
                $antrian = Antrian::findOrFail($id);
                $newStatus = $validated['status'];
                
                // Dapatkan mapping status Custom yang sesuai
                $customStatus = Antrian::getCustomStatusMapping()[$newStatus] ?? Custom::STATUS_IN_QUEUE;

                if ($antrian->custom_id != $validated['custom_id']) {
                    // Reset status custom lama
                    Custom::where('id', $antrian->custom_id)
                        ->update(['status' => Custom::STATUS_NOT_PRODUCED]);
                    
                    // Update custom baru
                    Custom::where('id', $validated['custom_id'])
                        ->update(['status' => $customStatus]);
                } else {
                    // Update status custom yang sama
                    Custom::where('id', $validated['custom_id'])
                        ->update(['status' => $customStatus]);
                }

                $antrian->update($validated);
            });

            return redirect()->route('antrian.index')
                ->with('success', 'Antrian berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal memperbarui antrian: ' . $e->getMessage());
        }
    }

    public function startProduction($id)
    {
        try {
            DB::transaction(function() use ($id) {
                $antrian = Antrian::findOrFail($id);
                $antrian->status = Antrian::STATUS_DALAM_PRODUKSI;
                $antrian->save();

                $antrian->custom->update([
                    'status' => Custom::STATUS_IN_PRODUCTION,
                    'tanggal_mulai' => now()
                ]);
            });

            return back()->with('success', 'Produksi telah dimulai');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memulai produksi: ' . $e->getMessage());
        }
    }

    public function completeProduction($id)
    {
        try {
            DB::transaction(function() use ($id) {
                $antrian = Antrian::findOrFail($id);
                $antrian->status = Antrian::STATUS_SELESAI_PRODUKSI;
                $antrian->save();

                $antrian->custom->update([
                    'status' => Custom::STATUS_PRODUCTION_COMPLETE,
                    'estimasi_selesai' => now()
                ]);
            });

            return back()->with('success', 'Produksi telah selesai');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyelesaikan produksi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function() use ($id) {
                $antrian = Antrian::findOrFail($id);
                $antrian->custom->update(['status' => Custom::STATUS_NOT_PRODUCED]);
                $antrian->delete();
            });

            return redirect()->route('antrian.index')
                ->with('success', 'Antrian berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus antrian: ' . $e->getMessage());
        }
    }
}
