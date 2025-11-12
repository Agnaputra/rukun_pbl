<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\JenisSayur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    // Middleware 'hasToko' akan diterapkan pada rute

    /**
     * Menampilkan daftar produk milik toko user yang login.
     * [GET] /api/produk
     */
    public function index()
    {
        $toko = Auth::user()->warga->toko;
        $produk = Produk::where('toko_id', $toko->toko_id)
            ->with('jenisSayur')
            ->paginate(10);
        return response()->json($produk);
    }

    /**
     * Menyimpan produk baru, dengan klasifikasi gambar dari FastAPI.
     * [POST] /api/produk
     */
    public function store(Request $request)
    {
        $toko = Auth::user()->warga->toko;

        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'foto_produk' => 'required|image|mimes:jpeg,png,jpg|max:2048', // File upload
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // --- PANGGILAN FASTAPI (CV/ML) ---
        $jenisId = null;
        try {
            /*
            // --- KODE ASLI FASTAPI ---
            $fastApiResponse = Http::withHeaders(['X-API-KEY' => env('FASTAPI_SECRET_KEY')])
                ->attach(
                    'image', $request->file('foto_produk')->get(), $request->file('foto_produk')->getClientOriginalName()
                )
                ->post(env('FASTAPI_KLASIFIKASI_URL', 'http://url-fastapi-anda/klasifikasi-sayur'));

            if (!$fastApiResponse->successful()) {
                throw new \Exception('Servis klasifikasi gambar gagal.');
            }

            $namaJenis = $fastApiResponse->json('prediksi_jenis'); // misal: 'Bayam'
            */

            // --- SIMULASI FASTAPI SUKSES ---
            $namaJenis = 'Bayam'; // Hardcode untuk simulasi

            // Cari di DB
            $jenisSayur = JenisSayur::firstOrCreate(
                ['nama_jenis' => $namaJenis],
                ['deskripsi' => 'Jenis baru dari CV'] // Otomatis buat jika belum ada
            );
            $jenisId = $jenisSayur->jenis_id;
        } catch (\Exception $e) {
            // Jika FastAPI gagal, jangan hentikan proses, set jenis_id ke 'Lainnya'
            $jenisSayur = JenisSayur::firstOrCreate(['nama_jenis' => 'Lainnya']);
            $jenisId = $jenisSayur->jenis_id;
        }
        // --- AKHIR PANGGILAN FASTAPI ---

        // Simpan file foto produk
        $fotoPath = $request->file('foto_produk')->store("public/produk/toko_{$toko->toko_id}");

        $produk = Produk::create([
            'toko_id' => $toko->toko_id,
            'jenis_id' => $jenisId, // Hasil dari FastAPI
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'foto_produk_path' => $fotoPath,
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $produk->load('jenisSayur')
        ], 201);
    }

    /**
     * Menghapus produk.
     * [DELETE] /api/produk/{id}
     */
    public function destroy($id)
    {
        $toko = Auth::user()->warga->toko;
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        // Cek kepemilikan
        if ($produk->toko_id != $toko->toko_id) {
            return response()->json(['message' => 'Akses ditolak. Ini bukan produk Anda.'], 403);
        }

        Storage::delete($produk->foto_produk_path); // Hapus file foto
        $produk->delete();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    // TODO: Buat fungsi update() dan show() jika diperlukan
    // public function update(Request $request, $id) { ... }
    // public function show($id) { ... }
}
