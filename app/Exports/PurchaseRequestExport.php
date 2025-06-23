<?php

namespace App\Exports;

use App\Models\PurchaseRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchaseRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $purchaseRequests;

    public function __construct($purchaseRequests)
    {
        $this->purchaseRequests = $purchaseRequests;
    }

    public function collection()
    {
        return $this->purchaseRequests;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Dibuat Oleh',
            'Tanggal Permintaan',
            'Status',
            'Catatan',
            'Jumlah Item',
            'Dibuat Pada',
        ];
    }

    public function map($purchaseRequest): array
    {
        return [
            $purchaseRequest->id,
            $purchaseRequest->createdBy->nama,
            $purchaseRequest->tanggal_permintaan,
            $purchaseRequest->status,
            $purchaseRequest->catatan,
            $purchaseRequest->details->count(),
            $purchaseRequest->created_at,
        ];
    }
}