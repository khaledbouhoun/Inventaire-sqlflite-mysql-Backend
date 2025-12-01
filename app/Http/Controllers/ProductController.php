<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Facades\Excel; // <-- use Facade here


class ProductController extends Controller
{
    // Get all products
    
     public function index(){
        $products = Product::all();
        return response()->json($products);
     }
    
     public function limit(Request $request)
    {
        $limit = (int) $request->query('limit', 100);
        $offset = (int) $request->query('offset', 0);

        $maxLimit = 1000;
        if ($limit < 1) {
            $limit = 1;
        } elseif ($limit > $maxLimit) {
            $limit = $maxLimit;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        $total = Product::count();
        $products = Product::skip($offset)->take($limit)->get();

        return response()->json([
            'data' => $products,
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
        ]);
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
        ini_set('max_execution_time', 3600); // 60 minutes
        ini_set('memory_limit', '1024M');   // increase memory if needed

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

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
    
public function search($searchQuery)
    {
        // 1. Récupérer la chaîne de recherche en utilisant la clé 'searchQuery'
        // Utilisation de trim() pour nettoyer les espaces superflus, y compris les guillemets.
        $searchString = trim($searchQuery, '"');

        $query = Product::query();

        // 2. Vérifier si la chaîne de recherche est présente et non vide
        if (!empty($searchString)) {

            // Nettoyer et diviser la chaîne en fragments (ex: 'ca 35 vte' -> ['ca', '35', 'vte'])
            // array_filter supprime les entrées vides résultant d'espaces multiples
            $fragments = array_filter(explode(' ', $searchString));

            // ...
            if (!empty($fragments)) {

                // Utilisation d'un groupe WHERE pour gérer la logique AND entre les fragments
                 $query->where(function ($parent) use ($fragments) {

                    foreach ($fragments as $fragment) {

                        $pattern = '%' . $fragment . '%';

                        // each fragment must match -> AND
                        $parent->where(function ($q) use ($pattern) {
                            $q->where('prd_no', 'LIKE', $pattern)
                                ->orWhere('prd_nom', 'LIKE', $pattern);
                        });
                    }
                });
            }
            // ...
        }

        // 3. Exécution et retour des résultats
        $products = $query->get();

        return response()->json([
            'org' => $searchQuery,
            'triming' => $searchString,
            'data' => $products,
            'count' => $products->count(),
        ]);
    }
}
