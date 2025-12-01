<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Concerns\SkipsOnError;
use Throwable;

class ProductsImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsOnError
{
    public $results = [];

    public function model(array $row)
    {
        $productCode = $row['code'] ?? null;
        $productName = $row['designation'] ?? null;

        try {
            if (empty($productCode) || empty($productName)) {
                $this->results[] = [
                    'prd_no' => (string)($productCode ?? ''),
                    'prd_nom' => (string)($productName ?? ''),
                    'reason' => 'Missing Code or Designation',
                    'status' => 'failed'
                ];
                return null;
            }

            $existing = Product::where('prd_no', $productCode)
                ->where('prd_nom', $productName)
                ->first();

            if ($existing) {
                $this->results[] = [
                    'prd_no' => (string)$productCode,
                    'prd_nom' => (string)$productName,
                    'reason' => 'Already exists',
                    'status' => 'skipped'
                ];
                return null;
            }

            $product = new Product([
                'prd_no' => (string)$productCode,
                'prd_nom' => (string)$productName,
            ]);

            $this->results[] = [
                'prd_no' => (string)$productCode,
                'prd_nom' => (string)$productName,
                'reason' => 'Created successfully',
                'status' => 'success'
            ];

            return $product;
        } catch (Throwable $e) {
            $this->results[] = [
                'prd_no' => (string)($productCode ?? ''),
                'prd_nom' => (string)($productName ?? ''),
                'reason' => $e->getMessage(),
                'status' => 'failed'
            ];
            return null;
        }
    }

    public function onError(Throwable $e)
    {
        // Optional: log global errors
    }

    // Read 1000 rows at a time
    public function chunkSize(): int
    {
        return 1000;
    }

    // Insert 100 records at once
    public function batchSize(): int
    {
        return 100;
    }
}
