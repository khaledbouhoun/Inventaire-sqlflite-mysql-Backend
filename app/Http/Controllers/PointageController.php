<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pointage;

class PointageController extends Controller
{
    // ----------------------------
    // 1️⃣ Get all Pointages
    // ----------------------------
    public function index()
    {
        $pointages = Pointage::get();
        return response()->json($pointages);
    }

    // ----------------------------
    // 2️⃣ Store a new Pointage
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'pntg_nom' => 'required|string|max:255',
        ]);

        $pointage = Pointage::create([
            'pntg_nom' => $request->pntg_nom,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pointage created successfully',
            'data' => $pointage
        ]);
    }

    // ----------------------------
    // 3️⃣ Show a single Pointage
    // ----------------------------
    public function show($id)
    {
        $pointage = Pointage::with('users')->find($id);

        if (!$pointage) {
            return response()->json(['message' => 'Pointage not found'], 404);
        }

        return response()->json($pointage);
    }

    // ----------------------------
    // 4️⃣ Update a Pointage
    // ----------------------------
    public function update(Request $request, $id)
    {
        $pointage = Pointage::find($id);

        if (!$pointage) {
            return response()->json(['message' => 'Pointage not found'], 404);
        }

        $request->validate([
            'pntg_nom' => 'required|string|max:255',
        ]);

        $pointage->update([
            'pntg_nom' => $request->pntg_nom,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pointage updated successfully',
            'data' => $pointage
        ]);
    }

    // ----------------------------
    // 5️⃣ Delete a Pointage
    // ----------------------------
    public function destroy($id)
    {
        $pointage = Pointage::find($id);

        if (!$pointage) {
            return response()->json(['message' => 'Pointage not found'], 404);
        }

        $pointage->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pointage deleted successfully'
        ]);
    }
}
