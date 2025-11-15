<?php

namespace App\Http\Controllers;

use App\Models\JobPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JobPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if request wants JSON (API call) or HTML (page view)
        if ($request->wantsJson() || $request->expectsJson()) {
            // API response for JavaScript
            $query = JobPekerjaan::with('kelompok');

            // If user is karyawan, only show their group's jobs
            if ($user->isKaryawan() && $user->kelompok_id) {
                $query->where('kelompok_id', $user->kelompok_id);
            }

            // Filter by day if provided
            if ($request->has('day') && $request->day) {
                $query->where('hari', $request->day);
            }

            // Search functionality
            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('lokasi', 'like', '%' . $searchTerm . '%')
                      ->orWhere('hari', 'like', '%' . $searchTerm . '%')
                      ->orWhere('perbaikan_kwh', 'like', '%' . $searchTerm . '%')
                      ->orWhere('pemeliharaan_pengkabelan', 'like', '%' . $searchTerm . '%')
                      ->orWhere('pengecekan_gardu', 'like', '%' . $searchTerm . '%')
                      ->orWhere('penanganan_gangguan', 'like', '%' . $searchTerm . '%');
                });
            }

            $jobs = $query->orderBy('tanggal', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 10));
            
            return response()->json([
                'data' => $jobs->items(),
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
                'from' => $jobs->firstItem(),
                'to' => $jobs->lastItem(),
                'prev_page_url' => $jobs->previousPageUrl(),
                'next_page_url' => $jobs->nextPageUrl()
            ]);
        }
        
        // Base query for statistics (without filters)
        $baseQuery = JobPekerjaan::query();
        
        // If user is karyawan, only show their group's jobs
        if ($user->isKaryawan() && $user->kelompok_id) {
            $baseQuery->where('kelompok_id', $user->kelompok_id);
        }
        
        // Calculate statistics (without filters)
        $totalJob = (clone $baseQuery)->count();
        $totalWaktu = (clone $baseQuery)->sum('waktu_penyelesaian');
        $hariIni = (clone $baseQuery)->whereDate('tanggal', today())->count();
        $lokasiBerbeda = (clone $baseQuery)->distinct('lokasi')->count('lokasi');
        
        // Query for paginated data (with filters)
        $query = JobPekerjaan::with('kelompok');

        // If user is karyawan, only show their group's jobs
        if ($user->isKaryawan() && $user->kelompok_id) {
            $query->where('kelompok_id', $user->kelompok_id);
        }

        // Filter by day if provided
        if ($request->filled('day')) {
            $query->where('hari', $request->day);
        }

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('lokasi', 'like', '%' . $searchTerm . '%')
                  ->orWhere('hari', 'like', '%' . $searchTerm . '%')
                  ->orWhere('perbaikan_kwh', 'like', '%' . $searchTerm . '%')
                  ->orWhere('pemeliharaan_pengkabelan', 'like', '%' . $searchTerm . '%')
                  ->orWhere('pengecekan_gardu', 'like', '%' . $searchTerm . '%')
                  ->orWhere('penanganan_gangguan', 'like', '%' . $searchTerm . '%');
            });
        }

        $jobPekerjaans = $query->orderBy('tanggal', 'desc')
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);

        $statistics = [
            'totalJob' => $totalJob,
            'totalWaktu' => $totalWaktu ?? 0,
            'hariIni' => $hariIni,
            'lokasiBerbeda' => $lokasiBerbeda,
        ];

        return view('dashboard.job-pekerjaan', compact('jobPekerjaans', 'statistics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'perbaikan_kwh' => 'required|string|max:1000',
            'pemeliharaan_pengkabelan' => 'required|string|max:1000',
            'pengecekan_gardu' => 'required|string|max:1000',
            'penanganan_gangguan' => 'required|string|max:1000',
            'lokasi' => 'required|string|max:255',
            'hari' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_penyelesaian' => 'required|integer|min:0',
        ]);

        $user = Auth::user();

        if ($user->isKaryawan() && !$user->kelompok_id) {
            return response()->json([
                'success' => false,
                'error' => 'Anda belum terdaftar dalam kelompok'
            ], 400);
        }

        $job = JobPekerjaan::create([
            'id' => Str::uuid(),
            'perbaikan_kwh' => $request->perbaikan_kwh,
            'pemeliharaan_pengkabelan' => $request->pemeliharaan_pengkabelan,
            'pengecekan_gardu' => $request->pengecekan_gardu,
            'penanganan_gangguan' => $request->penanganan_gangguan,
            'lokasi' => $request->lokasi,
            'kelompok_id' => $user->kelompok_id,
            'hari' => $request->hari,
            'tanggal' => $request->tanggal,
            'waktu_penyelesaian' => $request->waktu_penyelesaian,
        ]);

        return response()->json($job->load('kelompok'));
    }

    public function show($id)
    {
        $job = JobPekerjaan::with('kelompok')->findOrFail($id);
        return response()->json($job);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'perbaikan_kwh' => 'required|string|max:1000',
            'pemeliharaan_pengkabelan' => 'required|string|max:1000',
            'pengecekan_gardu' => 'required|string|max:1000',
            'penanganan_gangguan' => 'required|string|max:1000',
            'lokasi' => 'required|string|max:255',
            'hari' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_penyelesaian' => 'required|integer|min:0',
        ]);

        $job = JobPekerjaan::findOrFail($id);
        $job->update($request->only([
            'perbaikan_kwh', 'pemeliharaan_pengkabelan', 'pengecekan_gardu', 
            'penanganan_gangguan', 'lokasi', 'hari', 'tanggal', 'waktu_penyelesaian'
        ]));

        return response()->json($job->load('kelompok'));
    }

    public function destroy($id)
    {
        try {
            $job = JobPekerjaan::findOrFail($id);
            $job->delete();

            return response()->json([
                'success' => true,
                'message' => 'Job pekerjaan berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error deleting job pekerjaan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus job pekerjaan: ' . $e->getMessage()
            ], 500);
        }
    }
}





