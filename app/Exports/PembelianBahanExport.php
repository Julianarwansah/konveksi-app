<?php

namespace App\Exports;

use App\Models\PembelianBahan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PembelianBahanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $pembelianBahan;

    public function __construct($pembelianBahan)
    {
        $this->pembelianBahan = $pembelianBahan;
    }

    public function collection()
    {
        return $this->pembelianBahan;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Admin Purchasing',
            'Supplier',
            'Total Harga',
            'Tanggal Pembelian',
            'Status',
        ];
    }

    public function map($pembelian): array
    {
        return [
            $pembelian->id,
            $pembelian->adminPurchasing->nama,
            $pembelian->supplier->nama,
            number_format($pembelian->total_harga, 2),
            $pembelian->tanggal_pembelian,
            $pembelian->status,
        ];
    }
}