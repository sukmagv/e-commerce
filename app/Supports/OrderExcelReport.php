<?php

namespace App\Supports;

use App\Modules\Order\Models\Order;
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
    protected ?string $status;
    protected ?string $startDate;
    protected ?string $endDate;

    /**
     * Export to excel
     *
     * @param string|null $status
     * @param string|null $startDate
     * @param string|null $endDate
     */
    public function __construct(?string $status = null, ?string $startDate = null, ?string $endDate = null)
    {
        $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $this->orders = $this->fetchOrders();
    }

    /**
     * Retrive data order with filter
     */
    protected function fetchOrders(): Collection
    {
        $query = Order::with([
            'user',
            'payment.latestProof',
            'orderItem.product',
            'orderItem.discount',
        ]);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query->get()->map(fn($order) => [
            'Order Code'    => $order->code,
            'Customer Name' => $order->user->name,
            'Items'         => $order->orderItem?->product?->name ?? '-',
            'Grand Total'   => $order->grand_total,
            'Payment Status'=> $order->payment?->latestProof->status->value ?? 'N/A',
            'Created At'    => $order->created_at,
        ]);
    }

    /**
     * Collection send to excel
     */
    public function collection()
    {
        return $this->orders;
    }

    /**
     * Column header
     */
    public function headings(): array
    {
        return ['Order Code', 'Customer Name', 'Items', 'Grand Total', 'Payment Status', 'Created At'];
    }

    /**
     * Row mapping
     */
    public function map($row): array
    {
        return [
            $row['Order Code'],
            $row['Customer Name'],
            $row['Items'],
            $row['Grand Total'],
            $row['Payment Status'],
            $row['Created At']->format('Y-m-d'),
        ];
    }

    /**
     * Column width
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Order Code
            'B' => 30, // Customer Name
            'C' => 30, // Items
            'D' => 15, // Grand Total
            'E' => 15, // Payment Status
            'F' => 25, // Created At
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
