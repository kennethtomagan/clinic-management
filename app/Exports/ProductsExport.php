<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Product::query();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Barcode Number',
            'Price',
            'Stock',
            'is Available',
            'Image',
        ];
    }

    // Define the data mapping for each row
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->barcode,
            $product->price,
            $product->qty,
            $product->is_visible,
            $product->getMedia('product-images')->first()->getUrl(),
        ];
    }
}
