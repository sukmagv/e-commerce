<?php

namespace App\Exports;

use App\Modules\Order\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class OrderExcelReport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithColumnWidths,
    WithStyles,
    WithChunkReading,
    WithColumnFormatting
{
    public function query()
    {
        return Order::with([
                'user',
                'payment.proof',
                'orderItem.product',
                'orderItem.discount'
            ])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->startDate, fn ($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('created_at', '<=', $this->endDate));
    }

    protected ?string $status;
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(?string $status = null, ?string $startDate = null, ?string $endDate = null)
    {
        $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return [
            'Order Code',
            'Customer Name',
            'Items',
            'Price',
            'Discount',
            'Grand Total',
            'Payment Status',
            'Created At',
        ];
    }

    public function map($order): array
    {
        return [
            $order->code,
            $order->user->name,
            $order->orderItem?->product?->name ?? '-',
            $order->orderItem->total_price,
            $order->orderItem->discount_price,
            $order->grand_total,
            $order->payment?->proof->status->value ?? 'N/A',
            $order->created_at->format('Y-m-d'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 30,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 25,
            'H' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
        $sheet->freezePane('A2');
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
