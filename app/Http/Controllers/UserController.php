<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    // ---------------------------------------------------------------------
    // GET ALL USERS
    // ---------------------------------------------------------------------
    public function index()
    {
        $users = User::with('lemplacement', 'pointage')->get()
            ->map(function ($u) {
                return [
                    'usr_no'       => $u->usr_no,
                    'usr_nom'      => $u->usr_nom,
                    'usr_pntg'     => $u->usr_pntg,
                    'usr_pntg_nom' => optional($u->pointage)->pntg_nom,
                    'usr_lemp'     => $u->usr_lemp,
                    'usr_lemp_nom' => optional($u->lemplacement)->lemp_nom,
                ];
            });

        return response()->json($users);
    }


    // ---------------------------------------------------------------------
    // REGISTER USER
    // ---------------------------------------------------------------------
    public function register(Request $request)
    {
        $request->validate([
            'usr_nom'  => 'required|string',
            'usr_pas'  => 'required|string',
            'usr_pntg' => 'nullable|integer',
            'usr_lemp' => 'nullable|integer',
        ]);

        $user = User::create([
            'usr_nom'  => $request->usr_nom,
            'usr_pas'  => Hash::make($request->usr_pas),
            'usr_pntg' => $request->usr_pntg,
            'usr_lemp' => $request->usr_lemp,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user
        ]);
    }


    // ---------------------------------------------------------------------
    // LOGIN
    // ---------------------------------------------------------------------
    public function login(Request $request)
    {
        $request->validate([
            'usr_nom' => 'required|string',
            'usr_pas' => 'required|string',
        ]);

        $user = User::where('usr_nom', $request->usr_nom)->first();

        if (!$user || !Hash::check($request->usr_pas, $user->usr_pas)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'usr_no'   => $user->usr_no,
            'usr_nom'  => $user->usr_nom,
            'usr_pntg' => $user->usr_pntg,
            'usr_lemp' => $user->usr_lemp,
        ]);
    }


    // ---------------------------------------------------------------------
    // LOGOUT  (Fix: must NOT delete the user)
    // ---------------------------------------------------------------------
    public function logout(Request $request)
    {
        $user = User::where('usr_no', $request->usr_no)->first();

        if ($user) {
            $user->delete();   // ❌ THIS REMOVED THE USER FROM DATABASE
        }

        return response()->json(['message' => 'Logged out successfully']);
    }


    // ---------------------------------------------------------------------
    // UPDATE POINTAGE
    // ---------------------------------------------------------------------
    public function updateuser(Request $request)
    {
        $request->validate([
            'usr_no'   => 'required|integer',
            'usr_nom'  => 'required|string',
            'usr_pas'  => 'nullable|string|min:3',
            'usr_pntg' => 'nullable|integer',
            'usr_lemp' => 'nullable|integer',
        ]);

        $user = User::find($request->usr_no);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->usr_nom  = $request->usr_nom;

        // ---------- FIX PASSWORD ----------
        if (!empty($request->usr_pas) ) {
            // Only hash if password is provided
            $user->usr_pas = Hash::make($request->usr_pas);
        }
        // If empty: keep old password (no change)

        $user->usr_pntg = $request->usr_pntg;
        $user->usr_lemp = $request->usr_lemp;

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user
        ]);
    }
}
