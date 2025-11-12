<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // Pastikan ini di-import

class WargaController extends Controller
{
    /**
     * Menampilkan daftar semua warga (Hanya untuk Admin/Pengurus).
     * [GET] /api/warga
     */
    public function index()
    {
        // Rute ini sudah dilindungi oleh middleware 'role' di api.php
        $warga = Warga::with('user', 'keluarga.rt.rw')->paginate(15);
        return response()->json($warga);
    }

    /**
     * Menyimpan data warga baru (Hanya untuk Admin/Pengurus).
     * [POST] /api/warga
     */
    public function store(Request $request)
    {
        // Rute ini sudah dilindungi oleh middleware 'role' di api.php
        $validator = Validator::make($request->all(), [
            'keluarga_id' => 'required|integer|exists:keluarga,keluarga_id',
            'nik' => 'required|string|size:16|unique:warga,nik',
            'nama_lengkap' => 'required|string|max:100',
            'user_id' => 'nullable|integer|exists:users,user_id|unique:warga,user_id',
            'telepon' => 'nullable|string|max:20',
            // Tambahkan validasi 'nullable' untuk data non-wajib lainnya jika perlu
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // $request->all() aman karena Model Warga punya $fillable
        $warga = Warga::create($request->all());
        return response()->json($warga, 201);
    }

    /**
     * Menampilkan detail satu warga.
     * [GET] /api/warga/{id}
     * Otorisasi: Admin/Pengurus bisa lihat semua. Warga hanya bisa lihat diri sendiri.
     */
    public function show($id)
    {
        $authedUser = Auth::user();
        $warga = Warga::with('user', 'keluarga.rt.rw', 'toko')->find($id); // Muat semua relasi

        if (!$warga) {
            return response()->json(['message' => 'Data warga tidak ditemukan'], 404);
        }

        // --- Logika Otorisasi Internal ---
        // Cek apakah user yang login adalah pemilik profil ini
        $isOwner = $authedUser->warga && $authedUser->warga->warga_id == $id;

        // Cek apakah user yang login adalah Admin/Pengurus
        $isPengurus = in_array($authedUser->role->nama_role, ['Admin', 'Ketua RW', 'Ketua RT']);

        if ($isOwner || $isPengurus) {
            return response()->json($warga); // Tampilkan data jika pemilik ATAU pengurus
        }
        // --- Akhir Otorisasi ---

        // Jika bukan keduanya, tolak akses
        return response()->json(['message' => 'Akses ditolak. Anda tidak berhak melihat data ini.'], 403);
    }

    /**
     * Mengupdate data warga.
     * [PUT/PATCH] /api/warga/{id}
     * Otorisasi: Admin/Pengurus bisa update semua. Warga hanya bisa update diri sendiri.
     */
    public function update(Request $request, $id)
    {
        $authedUser = Auth::user();
        $warga = Warga::find($id);

        if (!$warga) {
            return response()->json(['message' => 'Data warga tidak ditemukan'], 404);
        }

        // --- Logika Otorisasi Internal ---
        $isOwner = $authedUser->warga && $authedUser->warga->warga_id == $id;
        $isPengurus = in_array($authedUser->role->nama_role, ['Admin', 'Ketua RW', 'Ketua RT']);

        if (!$isOwner && !$isPengurus) {
            return response()->json(['message' => 'Akses ditolak. Anda tidak berhak mengubah data ini.'], 403);
        }
        // --- Akhir Otorisasi ---

        $isPatch = $request->isMethod('PATCH');
        $requiredRule = $isPatch ? 'sometimes' : 'required';

        // Tentukan aturan validasi dasar (yang boleh diubah semua orang)
        $rules = [
            'nama_lengkap' => $requiredRule . '|string|max:100',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:20',
            'status_perkawinan' => 'nullable|string|max:30',
            'pekerjaan' => 'nullable|string|max:50',
            'telepon' => 'nullable|string|max:20',
        ];

        // Hanya Pengurus yang boleh mengubah data kritis ini
        if ($isPengurus) {
            $rules['nik'] = $requiredRule . '|string|size:16|' . Rule::unique('warga', 'nik')->ignore($id, 'warga_id');
            $rules['keluarga_id'] = $requiredRule . '|integer|exists:keluarga,keluarga_id';
            $rules['user_id'] = 'nullable|integer|exists:users,user_id|' . Rule::unique('warga', 'user_id')->ignore($id, 'warga_id');
            $rules['status_warga'] = $requiredRule . '|in:Aktif,Pindah,Meninggal';
        } else {
            // Warga biasa dilarang (prohibited) mengubah data kritis ini
            $rules['nik'] = 'sometimes|prohibited';
            $rules['keluarga_id'] = 'sometimes|prohibited';
            $rules['user_id'] = 'sometimes|prohibited';
            $rules['status_warga'] = 'sometimes|prohibited';
        }

        // Perhatikan: 'foto_ktp', 'foto_wajah', dan 'is_verified_face'
        // SENGAJA TIDAK ADA di sini. Mereka hanya diatur oleh
        // WargaVerificationController.

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // $request->all() aman karena $fillable di Model
        // dan $rules di atas sudah memfilter input
        $warga->fill($request->all());
        $warga->save();

        return response()->json([
            'message' => 'Data warga berhasil diperbarui',
            'data' => $warga
        ]);
    }

    /**
     * Menghapus data warga (Hanya untuk Admin/Pengurus).
     * [DELETE] /api/warga/{id}
     */
    public function destroy($id)
    {
        // Rute ini sudah dilindungi oleh middleware 'role' di api.php
        $warga = Warga::find($id);
        if (!$warga) {
            return response()->json(['message' => 'Data warga tidak ditemukan'], 404);
        }

        // TODO: Pertimbangkan apa yang terjadi pada User-nya?
        // Haruskah $warga->user->delete() juga dipanggil?
        // Sesuai setup DB Anda, 'user_id' di 'warga' akan jadi NULL
        // tapi akun 'user' akan tetap ada.

        $warga->delete();

        return response()->json(['message' => 'Data warga berhasil dihapus'], 200);
    }
}
