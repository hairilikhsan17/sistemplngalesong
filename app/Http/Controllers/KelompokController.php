<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KelompokController extends Controller
{
    public function index()
    {
        $kelompoks = Kelompok::with(['karyawan', 'users'])->orderBy('created_at', 'desc')->get();
        return response()->json($kelompoks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:255',
            'shift' => 'required|in:Shift 1,Shift 2',
            'password' => 'required|string|min:6',
        ]);

        $kelompok = Kelompok::create([
            'id' => Str::uuid(),
            'nama_kelompok' => $request->nama_kelompok,
            'shift' => $request->shift,
        ]);

        // Create user account for the kelompok
        $username = strtolower(str_replace(' ', '', $kelompok->nama_kelompok));

        User::create([
            'id' => Str::uuid(),
            'username' => $username,
            'password' => Hash::make($request->password),
            'role' => 'karyawan',
            'kelompok_id' => $kelompok->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kelompok berhasil dibuat',
            'data' => $kelompok->load(['karyawan', 'users'])
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:255',
            'shift' => 'required|in:Shift 1,Shift 2',
            'password' => 'nullable|string|min:6',
        ]);

        $kelompok = Kelompok::findOrFail($id);
        $kelompok->update($request->only(['nama_kelompok', 'shift']));

        // Update user password if provided
        if ($request->password) {
            $user = User::where('kelompok_id', $kelompok->id)->first();
            if ($user) {
                $user->update(['password' => Hash::make($request->password)]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Kelompok berhasil diperbarui',
            'data' => $kelompok->load(['karyawan', 'users'])
        ]);
    }

    public function destroy($id)
    {
        $kelompok = Kelompok::findOrFail($id);
        $kelompok->delete();

        return response()->json(['success' => true]);
    }
}
