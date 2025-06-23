<?php

namespace App\Exports;

use App\Models\MaterialUsageRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialUsageRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $materialUsageRequests;

    public function __construct($materialUsageRequests)
    {
        $this->materialUsageRequests = $materialUsageRequests;
    }

    public function collection()
    {
        return $this->materialUsageRequests;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kode Produksi',
            'Diminta Oleh',
            'Diproses Oleh',
            'Status',
            'Jumlah Item',
            'Tanggal Permintaan',
        ];
    }

    public function map($materialUsageRequest): array
    {
        return [
            $materialUsageRequest->id,
            $materialUsageRequest->produksi->kode_produksi ?? '-',
            $materialUsageRequest->requestedBy->nama,
            $materialUsageRequest->processedBy->nama ?? '-',
            $materialUsageRequest->status,
            $materialUsageRequest->details->count(),
            $materialUsageRequest->created_at->format('d/m/Y H:i'),
        ];
    }
}