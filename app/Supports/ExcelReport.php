<?php

namespace App\Supports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExcelReport implements FromCollection, WithHeadings
{
    protected Collection $data;
    protected array $headings;

    /**
     * @param iterable|Collection $data       Collection of models or arrays
     * @param array $headings                Column headings
     */
    public function __construct(iterable $data, array $headings)
    {
        // pastikan selalu Collection
        $this->data = collect($data);
        $this->headings = $headings;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
