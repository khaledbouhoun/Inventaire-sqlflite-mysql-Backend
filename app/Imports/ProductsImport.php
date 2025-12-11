<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Throwable;

class ProductsImport extends DefaultValueBinder implements ToModel, WithHeadingRow, WithChunkReading, SkipsOnError
{
    public $results = [];
    // public $rawRows = [];   // NEW: store all raw rows Laravel reads

    public function bindValue(Cell $cell, $value)
    {
        // Force "code" column to string
        if ($cell->getColumn() === 'A') { // Adjust A to your code column
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function model(array $row)
    {
        // Save raw row exactly as Laravel read it (very important for debugging leading zeros)
        // $this->rawRows[] = $row;
        $productCode = $row['code'] ?? null;
        $productName = $row['designation'] ?? null;

        try {
            if (empty($productCode) || empty($productName)) {
                $this->results[] = [
                    'prd_no' => (string) $productCode,
                    'prd_nom' => (string) $productName,
                    'reason' => 'Missing Code or Designation',
                    'status' => 'failed'
                ];
                return null;
            }

            $existing = Product::where('prd_no', $productCode)->first();

            if ($existing) {

                // SAME NAME → SKIP
                if (trim($existing->prd_nom) === trim($productName)) {

                    $this->results[] = [
                        'prd_no' => (string) $productCode,
                        'prd_nom' => (string) $productName,
                        'reason' => 'Already exists (same name)',
                        'status' => 'skipped'
                    ];
                    return null;
                }

                // DIFFERENT NAME → UPDATE
                $existing->prd_nom = $productName;
                $existing->save();

                $this->results[] = [
                    'prd_no' => (string) $productCode,
                    'prd_nom' => (string) $productName,
                    'reason' => 'Updated (name changed)',
                    'status' => 'updated'
                ];
                return null;
            }

            // CREATE NEW
            $product = new Product([
                'prd_no' => (string) $productCode,
                'prd_nom' => (string) $productName,
            ]);

            $this->results[] = [
                'prd_no' => (string) $productCode,
                'prd_nom' => (string) $productName,
                'reason' => 'Created successfully',
                'status' => 'success'
            ];

            return $product;

        } catch (Throwable $e) {
            $this->results[] = [
                'prd_no' => (string) $productCode,
                'prd_nom' => (string) $productName,
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
