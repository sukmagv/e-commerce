<?php

namespace App\Supports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExcelReport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected Collection $orders;

    /**
     * @param Collection $orders
     */
    public function __construct(Collection $orders)
    {
        $this->orders = $orders;
    }

    /**
     * Data source for excel
     */
    public function collection(): Collection
    {
        return $this->orders;
    }

    /**
     * Column header
     */
    public function headings(): array
    {
        return [
            'Order Code',
            'Customer Name',
            'Items',
            'Grand Total',
            'Payment Status',
            'Created At',
        ];
    }

    /**
     * Row mapping
     */
    public function map($order): array
    {
        return [
            $order->code,
            $order->user->name,
            $order->orderItem?->product?->name ?? '-',
            $order->grand_total,
            $order->payment?->latestProof->status->value ?? 'N/A',
            $order->created_at->format('Y-m-d'),
        ];
    }

    /**
     * Column width
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 30,
            'D' => 15,
            'E' => 15,
            'F' => 25,
        ];
    }

    /**
     * Styling header
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
        $sheet->freezePane('A2');
    }
}
