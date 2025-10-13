<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Karyawan::with('kelompok');

        // If user is karyawan, only show their group's karyawans
        if ($user->isKaryawan() && $user->kelompok_id) {
            $query->where('kelompok_id', $user->kelompok_id);
        }

        $karyawans = $query->orderBy('nama', 'asc')->get();
        return response()->json($karyawans);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kelompok_id' => 'required|exists:kelompok,id',
        ]);

        $karyawan = Karyawan::create([
            'id' => Str::uuid(),
            'nama' => $request->nama,
            'kelompok_id' => $request->kelompok_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil ditambahkan',
            'data' => $karyawan->load('kelompok')
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kelompok_id' => 'required|exists:kelompok,id',
        ]);

        $karyawan = Karyawan::findOrFail($id);
        $karyawan->update($request->only(['nama', 'kelompok_id']));

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil diperbarui',
            'data' => $karyawan->load('kelompok')
        ]);
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil dihapus'
        ]);
    }
}


