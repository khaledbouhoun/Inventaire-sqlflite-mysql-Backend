<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel; // <-- use Facade here


class ProductController extends Controller
{
    // Get all products
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    // Import products from Excel
    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,csv',
    //     ]);

    //     Excel::import(new ProductsImport, $request->file('file'));

    //     return response()->json(['message' => 'Products imported successfully']);
    // }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        $import = new ProductsImport();

        try {
            Excel::import($import, $request->file('file')); 
            return response()->json([
                'data' => $import->results,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Import failed',
                'details' => $e->getMessage(),
            ], 500);
        }
    }



    // Get single product
    public function show($prd_no)
    {
        $product = Product::find($prd_no);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    // Create new product
    public function store(Request $request)
    {
        $product = Product::create([
            'prd_nom' => $request->prd_nom,
            'prd_qr' => $request->prd_qr,
        ]);
        return response()->json($product, 201);
    }

    // Update product
    public function update(Request $request, $prd_no)
    {
        $product = Product::find($prd_no);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update([
            // 'prd_nom' => $request->prd_nom,
            'prd_qr' => $request->prd_qr,
        ]);

        return response()->json($product,201);
    }

    // Delete product
    public function destroy($prd_no)
    {
        $product = Product::find($prd_no);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
