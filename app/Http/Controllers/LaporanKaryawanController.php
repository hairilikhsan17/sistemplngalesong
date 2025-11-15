<?php

namespace App\Http\Controllers;

use App\Models\LaporanKaryawan;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LaporanKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Base query for statistics (without filters)
        $baseQuery = LaporanKaryawan::query();
        
        // If user is karyawan, only show their group's reports
        if ($user->isKaryawan() && $user->kelompok_id) {
            $baseQuery->where('kelompok_id', $user->kelompok_id);
        }
        
        // Calculate statistics (without filters)
        $totalLaporan = (clone $baseQuery)->count();
        $laporanHariIni = (clone $baseQuery)->whereDate('tanggal', today())->count();
        $laporanBulanIni = (clone $baseQuery)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();
        
        // Query for paginated data (with filters)
        $query = LaporanKaryawan::with('kelompok');

        // If user is karyawan, only show their group's reports
        if ($user->isKaryawan() && $user->kelompok_id) {
            $query->where('kelompok_id', $user->kelompok_id);
        }

        // Apply filters
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        if ($request->filled('instansi')) {
            $query->where('instansi', 'like', '%' . $request->instansi . '%');
        }

        if ($request->filled('alamat_tujuan')) {
            $query->where('alamat_tujuan', 'like', '%' . $request->alamat_tujuan . '%');
        }

        $laporans = $query->orderBy('tanggal', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);

        $statistics = [
            'totalLaporan' => $totalLaporan,
            'laporanHariIni' => $laporanHariIni,
            'laporanBulanIni' => $laporanBulanIni,
        ];

        return view('dashboard.kelompok.laporan', compact('laporans', 'statistics'));
    }

    public function getLaporans()
    {
        $user = Auth::user();
        $query = LaporanKaryawan::with('kelompok');

        // If user is karyawan, only show their group's reports
        if ($user->isKaryawan() && $user->kelompok_id) {
            $query->where('kelompok_id', $user->kelompok_id);
        }

        $laporans = $query->orderBy('created_at', 'desc')->get();
        return response()->json($laporans);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string',
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'alamat_tujuan' => 'required|string|max:255',
            'dokumentasi' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        $user = Auth::user();

        if ($user->isKaryawan() && !$user->kelompok_id) {
            return response()->json([
                'success' => false,
                'error' => 'Anda belum terdaftar dalam kelompok'
            ], 400);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('laporan-dokumentasi', $fileName, 'public');
        }

        $laporan = LaporanKaryawan::create([
            'id' => Str::uuid(),
            'hari' => $request->hari,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'instansi' => $request->instansi,
            'jabatan' => $request->jabatan,
            'alamat_tujuan' => $request->alamat_tujuan,
            'dokumentasi' => $request->dokumentasi,
            'file_path' => $filePath,
            'kelompok_id' => $user->kelompok_id,
        ]);

        return response()->json($laporan->load('kelompok'));
    }

    public function show($id)
    {
        $laporan = LaporanKaryawan::with('kelompok')->findOrFail($id);
        return response()->json($laporan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'hari' => 'required|string',
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'alamat_tujuan' => 'required|string|max:255',
            'dokumentasi' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        $laporan = LaporanKaryawan::findOrFail($id);

        // Prepare update data
        $updateData = $request->only([
            'hari', 'tanggal', 'nama', 'instansi', 'jabatan', 'alamat_tujuan', 'dokumentasi'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
                Storage::disk('public')->delete($laporan->file_path);
            }

            // Upload new file
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('laporan-dokumentasi', $fileName, 'public');
            
            $updateData['file_path'] = $filePath;
        }

        // Update the laporan
        $laporan->update($updateData);

        return response()->json($laporan->load('kelompok'));
    }

    public function destroy($id)
    {
        $laporan = LaporanKaryawan::findOrFail($id);
        
        // Delete file if exists
        if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
            Storage::disk('public')->delete($laporan->file_path);
        }
        
        $laporan->delete();

        return response()->json(['success' => true]);
    }

    public function downloadFile($id)
    {
        $laporan = LaporanKaryawan::findOrFail($id);
        
        if (!$laporan->file_path || !Storage::disk('public')->exists($laporan->file_path)) {
            return response()->json(['error' => 'File tidak ditemukan'], 404);
        }
        
        return Storage::disk('public')->download($laporan->file_path);
    }
}



