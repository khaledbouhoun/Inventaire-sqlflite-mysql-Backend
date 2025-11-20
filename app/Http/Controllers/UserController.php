<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with('lemplacement')->get()
            ->map(function ($u) {
                return [
                    'usr_no' => $u->usr_no,
                    'usr_nom' => $u->usr_nom,
                    'usr_pntg' => $u->usr_pntg,
                    'usr_lemp' => $u->usr_lemp,
                    'usr_lemp_nom' => $u->lemplacement->lemp_nom ?? null, // ← Show Emplacement name
                ];
            });

        return response()->json($users);
    }

    // ✅ Register
    public function register(Request $request)
    {
        $request->validate([
            'usr_nom' => 'required|string',
            'usr_pas' => 'required|string|min:3',
            'usr_pntg' => 'nullable|integer',
            'usr_lemp' => 'nullable|integer',
        ]);

        $user = User::create([
            'usr_nom' => $request->usr_nom,
            'usr_pas' => Hash::make($request->usr_pas),
            'usr_pntg' => $request->usr_pntg,
            'usr_lemp' => $request->usr_lemp,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ]);
    }

    // ✅ Login
    public function login(Request $request)
    {
        $request->validate([
            'usr_nom' => 'required|string',
            'usr_pas' => 'required|string',
            'remember' => 'boolean'
        ]);

        $user = User::where('usr_nom', $request->usr_nom)->first();

        if (!$user || !Hash::check($request->usr_pas, $user->usr_pas)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // remember token
        if ($request->remember) {
            $user->remember_token = bin2hex(random_bytes(50));
            $user->save();
        }

        return response()->json([
            'usr_no' => $user->usr_no,
            'usr_nom' => $user->usr_nom,
            'usr_pntg' => $user->usr_pntg,
            'usr_lemp' => $user->usr_lemp,
            'remember_token' => $user->remember_token,
        ]);
    }

    // ✅ Auto-login with remember_token
    public function autoLogin(Request $request)
    {
        $user = User::where('usr_nom', $request->usr_nom)
            ->where('remember_token', $request->remember_token)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        return response()->json([
            'usr_no' => $user->usr_no,
            'usr_nom' => $user->usr_nom,
            'usr_pntg' => $user->usr_pntg,
            'usr_lemp' => $user->usr_lemp,
        ]);
    }

    // ✅ Logout
    public function logout(Request $request)
    {
        $user = User::where('usr_nom', $request->usr_nom)
            ->where('remember_token', $request->remember_token)
            ->first();

        if ($user) {
            $user->remember_token = null;
            $user->save();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    // Update pointage
    public function updatePointage(Request $request, $usr_no)
    {
        $user = User::find($usr_no);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->usr_pntg = $request->usr_pntg; // new pointage value
        $user->save();

        return response()->json([
            'message' => 'Pointage updated',
            'usr_no' => $user->usr_no,
            'usr_pntg' => $user->usr_pntg
        ]);
    }

    // Update depot
    public function updateDepot(Request $request, $usr_no)
    {
        $user = User::find($usr_no);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->usr_lemp = $request->usr_lemp; // new depot value
        $user->save();

        return response()->json([
            'message' => 'Depot updated',
            'usr_no' => $user->usr_no,
            'usr_lemp' => $user->usr_lemp
        ]);
    }
}
