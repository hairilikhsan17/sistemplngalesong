<?php

namespace App\Http\Controllers;

use App\Models\LaporanKaryawan;
use App\Models\Kelompok;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LaporanKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Base query
        $query = LaporanKaryawan::with('kelompok');
        
        // If user is karyawan, only show their group's reports
        if ($user->isKaryawan() && $user->kelompok_id) {
            $query->where('kelompok_id', $user->kelompok_id);
        }
        
        // Calculate statistics
        $totalLaporan = (clone $query)->count();
        $laporanHariIni = (clone $query)->whereDate('tanggal', today())->count();
        $laporanBulanIni = (clone $query)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();
        
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
        
        $laporans = $query->orderBy('tanggal', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
        
        $statistics = [
            'totalLaporan' => $totalLaporan,
            'laporanHariIni' => $laporanHariIni,
            'laporanBulanIni' => $laporanBulanIni,
        ];
        
        // Get karyawan from the same kelompok
        $karyawans = collect([]);
        if ($user->isKaryawan() && $user->kelompok_id) {
            $karyawans = Karyawan::where('kelompok_id', $user->kelompok_id)
                ->orderBy('nama', 'asc')
                ->get();
        }
        
        return view('dashboard.kelompok.laporan', compact('laporans', 'statistics', 'karyawans'));
    }
    
    public function getLaporans()
    {
        $user = Auth::user();
        $query = LaporanKaryawan::with('kelompok');
        
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
            'alamat_tujuan' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:Perbaikan KWH,Pemeliharaan Pengkabelan,Pengecekan Gardu,Penanganan Gangguan',
            'deskripsi_kegiatan' => 'nullable|string|required_if:jenis_kegiatan,Penanganan Gangguan',
            'waktu_mulai_kegiatan' => 'nullable|date_format:H:i',
            'waktu_selesai_kegiatan' => 'nullable|date_format:H:i',
            'lokasi' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'deskripsi_kegiatan.required_if' => 'Deskripsi Penanganan Gangguan wajib diisi ketika jenis kegiatan adalah Penanganan Gangguan.',
        ]);
        
        $user = Auth::user();
        
        if ($user->isKaryawan() && !$user->kelompok_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar dalam kelompok'
            ], 400);
        }
        
        // Hitung durasi waktu otomatis
        $durasiWaktu = 0;
        if ($request->waktu_mulai_kegiatan && $request->waktu_selesai_kegiatan) {
            try {
                $waktuMulai = \Carbon\Carbon::createFromFormat('H:i', $request->waktu_mulai_kegiatan);
                $waktuSelesai = \Carbon\Carbon::createFromFormat('H:i', $request->waktu_selesai_kegiatan);
                
                // Jika waktu selesai lebih kecil dari waktu mulai, berarti melewati tengah malam
                if ($waktuSelesai->lt($waktuMulai)) {
                    $waktuSelesai->addDay();
                }
                
                $diffInMinutes = $waktuMulai->diffInMinutes($waktuSelesai);
                $durasiWaktu = round($diffInMinutes / 60, 2);
            } catch (\Exception $e) {
                $durasiWaktu = 0;
            }
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
            'alamat_tujuan' => $request->alamat_tujuan,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'waktu_mulai_kegiatan' => $request->waktu_mulai_kegiatan,
            'waktu_selesai_kegiatan' => $request->waktu_selesai_kegiatan,
            'durasi_waktu' => $durasiWaktu,
            'lokasi' => $request->lokasi,
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
            'alamat_tujuan' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:Perbaikan KWH,Pemeliharaan Pengkabelan,Pengecekan Gardu,Penanganan Gangguan',
            'deskripsi_kegiatan' => 'nullable|string',
            'waktu_mulai_kegiatan' => 'nullable|date_format:H:i',
            'waktu_selesai_kegiatan' => 'nullable|date_format:H:i',
            'lokasi' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        
        // Validasi khusus: deskripsi wajib jika jenis kegiatan adalah Penanganan Gangguan
        if ($request->jenis_kegiatan === 'Penanganan Gangguan' && empty($request->deskripsi_kegiatan)) {
            return response()->json([
                'success' => false,
                'message' => 'Deskripsi Penanganan Gangguan wajib diisi ketika jenis kegiatan adalah Penanganan Gangguan.',
                'errors' => [
                    'deskripsi_kegiatan' => ['Deskripsi Penanganan Gangguan wajib diisi.']
                ]
            ], 422);
        }
        
        $laporan = LaporanKaryawan::findOrFail($id);
        
        // Hitung durasi waktu otomatis
        $durasiWaktu = 0;
        if ($request->waktu_mulai_kegiatan && $request->waktu_selesai_kegiatan) {
            try {
                $waktuMulai = \Carbon\Carbon::createFromFormat('H:i', $request->waktu_mulai_kegiatan);
                $waktuSelesai = \Carbon\Carbon::createFromFormat('H:i', $request->waktu_selesai_kegiatan);
                
                // Jika waktu selesai lebih kecil dari waktu mulai, berarti melewati tengah malam
                if ($waktuSelesai->lt($waktuMulai)) {
                    $waktuSelesai->addDay();
                }
                
                $diffInMinutes = $waktuMulai->diffInMinutes($waktuSelesai);
                $durasiWaktu = round($diffInMinutes / 60, 2);
            } catch (\Exception $e) {
                $durasiWaktu = 0;
            }
        }
        
        $updateData = [
            'hari' => $request->hari,
            'tanggal' => $request->tanggal,
            'nama' => $request->nama,
            'instansi' => $request->instansi,
            'alamat_tujuan' => $request->alamat_tujuan,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
            'waktu_mulai_kegiatan' => $request->waktu_mulai_kegiatan,
            'waktu_selesai_kegiatan' => $request->waktu_selesai_kegiatan,
            'durasi_waktu' => $durasiWaktu,
            'lokasi' => $request->lokasi,
        ];
        
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
        
        $laporan->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui',
            'data' => $laporan->load('kelompok')
        ]);
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
