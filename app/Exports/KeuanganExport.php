<?php

// app/Exports/KeuanganExport.php

namespace App\Exports;

use App\Models\Keuangan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KeuanganExport implements FromCollection, WithHeadings
{
    protected $start;
    protected $end;

    public function __construct($start = null, $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        $query = Keuangan::query();

        if ($this->start) {
            $query->whereDate('tanggal', '>=', $this->start);
        }

        if ($this->end) {
            $query->whereDate('tanggal', '<=', $this->end);
        }

        return $query->get([
            'tanggal',
            'total_pemasukan',
            'total_pengeluaran',
            'saldo',
        ]);
    }

    public function headings(): array
    {
        return ['Tanggal', 'Total Pemasukan', 'Total Pengeluaran', 'Saldo'];
    }
}
