<?php

namespace App\Exports;

use App\Models\PenerimaanBahanBaku;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PenerimaanBahanBakuExport implements FromCollection, WithHeadings, WithMapping
{
    protected $penerimaanBahanBaku;

    public function __construct($penerimaanBahanBaku)
    {
        $this->penerimaanBahanBaku = $penerimaanBahanBaku;
    }

    public function collection()
    {
        return $this->penerimaanBahanBaku;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nomor Penerimaan',
            'Supplier',
            'Tanggal Penerimaan',
            'Catatan',
            'Jumlah Item',
            'Dibuat Pada',
        ];
    }

    public function map($penerimaan): array
    {
        return [
            $penerimaan->id,
            $penerimaan->nomor_penerimaan,
            $penerimaan->supplier->nama,
            $penerimaan->tanggal_penerimaan,
            $penerimaan->catatan ?? '-',
            $penerimaan->details->count(),
            $penerimaan->created_at,
        ];
    }
}
