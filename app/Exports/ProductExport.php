<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromQuery, WithCustomChunkSize, WithHeadings, WithMapping
{
    private int $index = 0;

    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        $query = Product::query();

        // Example: Apply simple filters if they exist
        if (isset($this->filters['search'])) {
            $query->where('name', 'like', '%'.$this->filters['search'].'%');
        }

        // Add sorting to ensure consistent results during chunking
        $query->orderBy('id');

        return $query;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Name',
            'Description',
            'Price',
            'Created At',
        ];
    }

    /**
     * @param  mixed  $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        $this->index++;

        return [
            $this->index,
            $row->name,
            $row->description,
            $row->price,
            $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
