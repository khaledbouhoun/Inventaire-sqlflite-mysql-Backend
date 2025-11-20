<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lemplacement;

class LemplacementController extends Controller
{
    // ----------------------------
    // 1️⃣ Get all Lemplacements
    // ----------------------------
    public function index()
    {
        $lemplacements = Lemplacement::get();
        return response()->json($lemplacements);
    }

    // ----------------------------
    // 2️⃣ Store a new Lemplacement
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'lemp_nom' => 'required|string|max:255',
        ]);

        $lemplacement = Lemplacement::create([
            'lemp_nom' => $request->lemp_nom,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Lemplacement created successfully',
            'data' => $lemplacement
        ]);
    }

    // ----------------------------
    // 3️⃣ Show a single Lemplacement
    // ----------------------------
    public function show($id)
    {
        $lemplacement = Lemplacement::with('gestqrs')->find($id);

        if (!$lemplacement) {
            return response()->json(['message' => 'Lemplacement not found'], 404);
        }

        return response()->json($lemplacement);
    }

    // ----------------------------
    // 4️⃣ Update a Lemplacement
    // ----------------------------
    public function update(Request $request, $id)
    {
        $lemplacement = Lemplacement::find($id);

        if (!$lemplacement) {
            return response()->json(['message' => 'Lemplacement not found'], 404);
        }

        $request->validate([
            'lemp_nom' => 'required|string|max:255',
        ]);

        $lemplacement->update([
            'lemp_nom' => $request->lemp_nom,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Lemplacement updated successfully',
            'data' => $lemplacement
        ]);
    }

    // ----------------------------
    // 5️⃣ Delete a Lemplacement
    // ----------------------------
    public function destroy($id)
    {
        $lemplacement = Lemplacement::find($id);

        if (!$lemplacement) {
            return response()->json(['message' => 'Lemplacement not found'], 404);
        }

        $lemplacement->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Lemplacement deleted successfully'
        ]);
    }
}
