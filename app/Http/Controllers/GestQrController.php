<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gestqr;
use Illuminate\Support\Facades\DB;
use Exception;

class GestqrController extends Controller
{
    // ----------------------------
    // 1️⃣ Get records (With Dynamic Filtering)
    // ----------------------------
    // Usage Examples:
    // All: GET /api/gestqr
    // Filter: GET /api/gestqr?usr_no=1&lemp_no=5
    // Range:  GET /api/gestqr?date_from=2025-01-01&date_to=2025-01-31
    public function index(Request $request)
    {
        // Eager-load related models so accessors don't N+1 when serializing
        $query = Gestqr::with(['lemplacement', 'user', 'product']);

        // Filter by Emplacement
        if ($request->has('lemp_no')) {
            $query->where('gqr_lemp_no', $request->lemp_no);
        }

        // Filter by User
        if ($request->has('usr_no')) {
            $query->where('gqr_usr_no', $request->usr_no);
        }

        // Filter by Product
        if ($request->has('prd_no')) {
            $query->where('gqr_prd_no', $request->prd_no);
        }

        // Filter by ID Range (gqr_no)
        // Note: This is usually only useful if lemp_no and usr_no are also specified
        if ($request->has('id_from') && $request->has('id_to')) {
            $query->whereBetween('gqr_no', [$request->id_from, $request->id_to]);
        }

        // Filter by Date Range
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('gqr_date', [$request->date_from, $request->date_to]);
        }

        // Order by date descending, fetch and map to only the requested fields
        $results = $query->latest('gqr_date')
            ->orderBy('gqr_no', 'asc')
            ->orderBy('gqr_lemp_no', 'asc')
            ->get()
            ->map(function ($g) {
                return [
                    'gqr_no'       => $g->gqr_no,
                    'gqr_lemp_no'  => $g->gqr_lemp_no,
                    'gqr_lemp_nom' => $g->gqr_lemp_nom,
                    'gqr_usr_no'   => $g->gqr_usr_no,
                    'gqr_usr_nom'  => $g->gqr_usr_nom,
                    'gqr_prd_no'   => $g->gqr_prd_no,
                    'gqr_prd_nom'  => $g->gqr_prd_nom,
                    'gqr_date'     => $g->gqr_date ? $g->gqr_date->toIso8601String() : null,
                ];
            });


        if ($results->isEmpty()) {
            return response()->json([], 404);
        }

        return response()->json($results->values());
    }

    // ----------------------------
    // 2️⃣ Store a new record
    // ----------------------------
    public function store(Request $request)
    {
        // 1. Validate
        $validated = $request->validate([
            'gqr_lemp_no' => 'required|integer|exists:lemplacement,lemp_no',
            'gqr_usr_no'  => 'required|integer|exists:users,usr_no',
            'gqr_prd_no'  => 'required|string|exists:products,prd_no|max:255',
            'gqr_date'    => 'nullable|date',
        ]);

        $lemp = $validated['gqr_lemp_no'];
        $usr  = $validated['gqr_usr_no'];
        $prd  = $validated['gqr_prd_no'];
        $date = $validated['gqr_date'] ?? now();

        try {
            $gestqr = DB::transaction(function () use ($lemp, $usr, $prd, $date) {

                // 2. Lock rows to calculate next gqr_no safely
                $maxNo = DB::table('gestqr')
                    ->where('gqr_lemp_no', $lemp)
                    ->where('gqr_usr_no', $usr)
                    ->lockForUpdate() // Critical for concurrency
                    ->max('gqr_no');

                $nextNo = $maxNo ? $maxNo + 1 : 1;

                // 3. Check for duplicates (same product, same user, same location)
                $exists = DB::table('gestqr')
                    ->where('gqr_lemp_no', $lemp)
                    ->where('gqr_usr_no', $usr)
                    ->where('gqr_prd_no', $prd)
                    ->exists();

                if ($exists) {
                    throw new Exception("This product already exists for this user and emplacement.", 409);
                }

                // 4. Create Record
                return Gestqr::create([
                    'gqr_no'      => $nextNo,
                    'gqr_lemp_no' => $lemp,
                    'gqr_usr_no'  => $usr,
                    'gqr_prd_no'  => $prd,
                    'gqr_date'    => $date,
                ]);
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Record created successfully',
                'data'    => $gestqr,
            ], 201);
        } catch (Exception $e) {
            // Return specific status code if set (like 409 conflict), else 400
            $status = $e->getCode() && is_int($e->getCode()) && $e->getCode() > 200 ? $e->getCode() : 400;

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], $status);
        }
    }

    // ----------------------------
    // 3️⃣ Show a single record
    // ----------------------------
    public function show($lemp, $usr, $no)
    {
        $gestqr = Gestqr::where('gqr_lemp_no', $lemp)
            ->where('gqr_usr_no', $usr)
            ->where('gqr_no', $no)
            ->with(['lemplacement', 'user', 'product'])
            ->first();

        if (!$gestqr) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        // Return only requested top-level fields (hide relation objects)
        $payload = [
            'gqr_no'       => $gestqr->gqr_no,
            'gqr_lemp_no'  => $gestqr->gqr_lemp_no,
            'gqr_lemp_nom' => $gestqr->gqr_lemp_nom,
            'gqr_usr_no'   => $gestqr->gqr_usr_no,
            'gqr_usr_nom'  => $gestqr->gqr_usr_nom,
            'gqr_prd_no'   => $gestqr->gqr_prd_no,
            'gqr_prd_nom'  => $gestqr->gqr_prd_nom,
            'gqr_date'     => $gestqr->gqr_date ? $gestqr->gqr_date->toIso8601String() : null,
        ];

        return response()->json($payload);
    }

    // ----------------------------
    // 4️⃣ Delete a record
    // ----------------------------
    public function destroy($lemp, $usr, $no)
    {
        $deleted = Gestqr::where('gqr_lemp_no', $lemp)
            ->where('gqr_usr_no', $usr)
            ->where('gqr_no', $no)
            ->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Record deleted successfully'
        ]);
    }
}
