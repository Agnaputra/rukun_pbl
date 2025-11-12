<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Warga;
use App\Models\Role;

class AuthController extends Controller
{
    /**
     * Registrasi User baru sekaligus data Warga.
     * Endpoint: [POST] /api/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:100',
            'nik' => 'required|string|size:16|unique:warga',
            'keluarga_id' => 'required|integer|exists:keluarga,keluarga_id',
            'username' => 'required|string|max:100|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $roleWarga = Role::where('nama_role', 'Warga')->first();
        if (!$roleWarga) {
            return response()->json(['message' => 'Role Warga tidak ditemukan. Harap setup database.'], 500);
        }


        try {
            DB::beginTransaction();


            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'role_id' => $roleWarga->role_id,
            ]);


            $warga = Warga::create([
                'user_id' => $user->user_id,
                'keluarga_id' => $request->keluarga_id,
                'nik' => $request->nik,
                'nama_lengkap' => $request->nama_lengkap,
                'telepon' => $request->telepon,

            ]);

            DB::commit();


            $token = $user->createToken('auth_token_' . $user->username)->plainTextToken;

            return response()->json([
                'message' => 'Registrasi berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->load('role'),
                'warga' => $warga,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registrasi Gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $credentials = [
            'username' => $request->login,
            'password' => $request->password
        ];

        if (!Auth::attempt($credentials)) {

            $credentials = [
                'email' => $request->login,
                'password' => $request->password
            ];

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Login Gagal. Username atau Password salah.'], 401);
            }
        }


        $user = Auth::user();


        $user->tokens()->delete();


        $token = $user->createToken('auth_token_' . $user->username)->plainTextToken;



        $user->load('role', 'warga');

        return response()->json([
            'message' => 'Login Berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Logout untuk user.
     * Endpoint: [POST] /api/logout (Wajib pakai token)
     */
    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Berhasil'
        ]);
    }
}
