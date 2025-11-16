<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
    // ✅ Register
    public function register(Request $request)
    {
        $request->validate([
            'usr_nom' => 'required|string',
            'usr_pas' => 'required|string|min:3',
        ]);

        $user = User::create([
            'usr_nom' => $request->usr_nom,
            'usr_pas' => Hash::make($request->usr_pas),
            'usr_pntg' => $request->usr_pntg,
            'usr_dpot' => $request->usr_dpot,
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
            'usr_id' => $user->usr_id,
            'usr_nom' => $user->usr_nom,
            'usr_pntg' => $user->usr_pntg,
            'usr_dpot' => $user->usr_dpot,
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
            'usr_id' => $user->usr_id,
            'usr_nom' => $user->usr_nom,
            'usr_pntg' => $user->usr_pntg,
            'usr_dpot' => $user->usr_dpot,
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
    public function updatePointage(Request $request, $usr_id)
    {
        $user = User::find($usr_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->usr_pntg = $request->usr_pntg; // new pointage value
        $user->save();

        return response()->json([
            'message' => 'Pointage updated',
            'usr_id' => $user->usr_id,
            'usr_pntg' => $user->usr_pntg
        ]);
    }

    // Update depot
    public function updateDepot(Request $request, $usr_id)
    {
        $user = User::find($usr_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->usr_dpot = $request->usr_dpot; // new depot value
        $user->save();

        return response()->json([
            'message' => 'Depot updated',
            'usr_id' => $user->usr_id,
            'usr_dpot' => $user->usr_dpot
        ]);
    }
}
