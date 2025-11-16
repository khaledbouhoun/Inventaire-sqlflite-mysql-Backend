<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Throwable;

class ProductsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    public $results = [];

    public function model(array $row)
    {
        try {
            // Check for null/empty values
            if (empty($row['id']) || empty($row['nom'])) {
                $this->results[] = [
                    'prd_no' => (string)($row['id'] ?? ''),
                    'prd_nom' => (string)($row['nom'] ?? ''),
                    'reason' => 'Missing ID or name',
                    'status' => 'failed'
                ];
                return null;
            }

            // Check if product already exists with same id and name
            $existing = Product::where('prd_no', $row['id'])
                ->where('prd_nom', $row['nom'])
                ->first();

            if ($existing) {
                $this->results[] = [
                    'prd_no' => (string)$row['id'],
                    'prd_nom' => (string)$row['nom'],
                    'reason' => 'Already exists',
                    'status' => 'skipped'
                ];
                return null;
            }

            // Create new product
            $product = new Product([
                'prd_no' => (string)$row['id'],
                'prd_nom' => (string)$row['nom'],
            ]);

            $this->results[] = [
                'prd_no' => (string)$row['id'],
                'prd_nom' => (string)$row['nom'],
                'reason' => 'Created successfully',
                'status' => 'success'
            ];

            return $product;
        } catch (Throwable $e) {
            $this->results[] = [
                'prd_no' => (string)($row['id'] ?? ''),
                'prd_nom' => (string)($row['nom'] ?? ''),
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
}
