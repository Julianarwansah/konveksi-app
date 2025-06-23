<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use Illuminate\Http\Request;
use App\Exports\KeuanganExport;
use Maatwebsite\Excel\Facades\Excel;


class KeuanganController extends Controller
{
    /**
     * Menampilkan semua data keuangan.
     */
    public function index(Request $request)
    {
        $query = Keuangan::query();

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $keuangan = $query->orderBy('tanggal', 'desc')->get();

        return view('keuangan.index', compact('keuangan'));
    }


    /**
     * Menampilkan detail satu data keuangan.
     */
    public function show(Keuangan $keuangan)
    {
        return view('keuangan.show', compact('keuangan'));
    }

    public function export(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;

        return Excel::download(new KeuanganExport($start, $end), 'laporan-keuangan.xlsx');
    }
}
