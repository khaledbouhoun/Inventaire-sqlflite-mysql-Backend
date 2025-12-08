<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invontaie;

class InvontaieController extends Controller
{
    // ----------------------------
    // 1️⃣ Get all Invontaies
    // ----------------------------
    public function index(Request $request)
    {
        $query = Invontaie::with('lemplacement', 'pointage', 'user', 'product');

        if ($request->filled('lemp_no')) {
            $query->where('inv_lemp_no', $request->lemp_no);
        }
        if ($request->filled('usr_no')) {
            $query->where('inv_usr_no', $request->usr_no);
        }
        if ($request->filled('pntg_no')) {
            $query->where('inv_pntg_no', $request->pntg_no);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('inv_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('inv_date', '<=', $request->date_to);
        }

        $invontaies = $query->get()
            ->map(function (Invontaie $invontaie) {
                return [
                    'inv_no' => $invontaie->inv_no,
                    'inv_lemp_no' => optional($invontaie->lemplacement)->lemp_no,
                    'inv_lemp_nom' => optional($invontaie->lemplacement)->lemp_nom,
                    'inv_pntg_no' => optional($invontaie->pointage)->pntg_no,
                    'inv_pntg_nom' => optional($invontaie->pointage)->pntg_nom,
                    'inv_usr_no' => optional($invontaie->user)->usr_no,
                    'inv_usr_nom' => optional($invontaie->user)->usr_nom,
                    'inv_prd_no' => optional($invontaie->product)->prd_no,
                    'inv_prd_nom' => optional($invontaie->product)->prd_nom,
                    'inv_exp' => $invontaie->inv_exp,
                    'inv_qte' => $invontaie->inv_qte,
                    'inv_date' => $invontaie->inv_date,
                ];
            });
        return response()->json($invontaies);
    }


    public function filtering(Request $request)
    {
        $query = Invontaie::with('lemplacement', 'pointage', 'user', 'product');

        if ($request->filled('lemp_no')) {
            $query->where('inv_lemp_no', $request->lemp_no);
        }


        $invontaies = $query->get()
            ->map(function (Invontaie $invontaie) {
                return [
                    'inv_no' => $invontaie->inv_no,
                    'inv_lemp_no' => optional($invontaie->lemplacement)->lemp_no,
                    'inv_lemp_nom' => optional($invontaie->lemplacement)->lemp_nom,
                    'inv_pntg_no' => optional($invontaie->pointage)->pntg_no,
                    'inv_pntg_nom' => optional($invontaie->pointage)->pntg_nom,
                    'inv_usr_no' => optional($invontaie->user)->usr_no,
                    'inv_usr_nom' => optional($invontaie->user)->usr_nom,
                    'inv_prd_no' => optional($invontaie->product)->prd_no,
                    'inv_prd_nom' => optional($invontaie->product)->prd_nom,
                    'inv_exp' => $invontaie->inv_exp,
                    'inv_qte' => $invontaie->inv_qte,
                    'inv_date' => $invontaie->inv_date,
                ];
            });
        return response()->json($invontaies);
    }

    // ----------------------------
    // 2️⃣ Store a new Invontaie
    // ----------------------------
    public function store(Request $request)
    {
        $request->validate([
            'inv_lemp_no' => 'required|integer',
            'inv_pntg_no' => 'required|integer',
            'inv_usr_no' => 'required|integer',
            'inv_prd_no' => 'required|string|max:255',
            'inv_exp' => 'required|string|max:255',
            'inv_qte' => 'required|numeric',
            'inv_date' => 'nullable|date',
        ]);

        $data = $request->all();

        // Set default date if not provided
        if (!isset($data['inv_date'])) {
            $data['inv_date'] = now();
        }

        // Define unique keys to check if record exists
        $uniqueKeys = [
            'inv_lemp_no' => $data['inv_lemp_no'],
            'inv_pntg_no' => $data['inv_pntg_no'],
            'inv_usr_no' => $data['inv_usr_no'],
            'inv_prd_no' => $data['inv_prd_no'],
        ];

        // Create or update the inventory
        $invontaie = Invontaie::updateOrCreate($uniqueKeys, $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Invontaie created or updated successfully',
            'data' => $invontaie
        ]);
    }


    // ----------------------------
    // 3️⃣ Show a single Invontaie
    // ----------------------------
    public function show($id)
    {
        $invontaie = Invontaie::find($id);

        if (!$invontaie) {
            return response()->json(['message' => 'Invontaie not found'], 404);
        }

        return response()->json($invontaie);
    }

    // ----------------------------
    // 4️⃣ Update a Invontaie
    // ----------------------------
    // الكود المعدّل مع استجابة النجاح
    public function update(Request $request, Invontaie $invontaie)
    {
        $validatedData = $request->validate([
            'inv_exp' => 'required|string|max:255',
        ]);

        $invontaie->update($validatedData);

        // إرجاع استجابة JSON ناجحة
        return response()->json([
            'message' => 'Invontaie exp updated successfully',
            'data' => $invontaie
        ], 200);
    }

    // ----------------------------
    // 5️⃣ Delete a Invontaie
    // ----------------------------
    public function destroy($id)
    {
        $invontaie = Invontaie::find($id);

        if (!$invontaie) {
            return response()->json(['message' => 'Invontaie not found'], 404);
        }

        $invontaie->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Invontaie deleted successfully'
        ]);
    }
}
