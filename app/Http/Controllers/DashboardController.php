<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Karyawan;
use App\Models\LaporanKaryawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function adminIndex()
    {
        // Get statistics directly in the controller
        $stats = $this->getAdminStatsData();
        $chartData = $this->getAdminChartData();
        
        return view('dashboard.atasan.index', compact('stats', 'chartData'));
    }
    
    /**
     * Get admin dashboard statistics data
     */
    public function getAdminStatsData()
    {
        try {
            // Calculate total reports
            $totalLaporan = LaporanKaryawan::count();
            
            // Calculate today's reports
            $laporanHariIni = LaporanKaryawan::whereDate('created_at', today())->count();
            
            // Calculate pending review (reports older than 1 day without review)
            $pendingReview = LaporanKaryawan::where('created_at', '<', now()->subDay())->count();
            
            // Calculate average reports per day (last 30 days)
            $avgPerHari = $this->calculateAvgPerHari();
            
            $stats = [
                // Basic counts
                'total_kelompok' => Kelompok::count(),
                'total_karyawan' => Karyawan::count(),
                'total_users' => User::where('role', 'kelompok')->count(),
                
                // Reports statistics
                'total_laporan' => $totalLaporan,
                'laporan_hari_ini' => $laporanHariIni,
                'laporan_bulan_ini' => LaporanKaryawan::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'laporan_pending' => $pendingReview,
                
                // Performance metrics
                'avg_waktu_penyelesaian' => 0,
                'best_performing_group' => $this->getBestPerformingGroup(),
                'monthly_trend' => $this->getMonthlyTrend(),
                
                // System health
                'system_health' => $this->getSystemHealth(),
                'recent_activities' => $this->getRecentActivities()
            ];

            return $stats;

        } catch (\Exception $e) {
            return [
                'total_kelompok' => 0,
                'total_karyawan' => 0,
                'total_users' => 0,
                'total_laporan' => 0,
                'laporan_hari_ini' => 0,
                'laporan_bulan_ini' => 0,
                'laporan_pending' => 0,
                'avg_waktu_penyelesaian' => 0,
                'best_performing_group' => 'Error',
                'monthly_trend' => [],
                'system_health' => ['status' => 'error', 'pending_rate' => 0, 'completion_rate' => 0],
                'recent_activities' => []
            ];
        }
    }

    /**
     * Display kelompok dashboard
     */
    public function kelompokIndex()
    {
        // Get performance data for all groups
        $performanceData = $this->getAllKelompokPerformance();
        
        // Get user's kelompok data for karyawan dashboard
        $user = auth()->user();
        $kelompok = $user->kelompok;
        
        // Calculate current user's ranking from performanceData
        $userRanking = null;
        if ($kelompok && !empty($performanceData)) {
            $rank = 1;
            $userScore = 0;
            $totalGroups = count($performanceData);
            
            foreach ($performanceData as $index => $perf) {
                if ($perf['nama'] === $kelompok->nama_kelompok) {
                    $rank = $index + 1;
                    $userScore = $perf['skor'];
                    break;
                }
            }
            
            $userRanking = [
                'rank' => $rank,
                'total_groups' => $totalGroups,
                'score' => $userScore,
                'skor' => $userScore
            ];
        }
        
        if ($kelompok) {
            $laporanCount = LaporanKaryawan::where('kelompok_id', $kelompok->id)->count();
            $recentLaporan = LaporanKaryawan::where('kelompok_id', $kelompok->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Get prediksi if exists
            $prediksis = collect([]); // You can add prediksi logic here if needed
        } else {
            $laporanCount = 0;
            $recentLaporan = collect([]);
            $prediksis = collect([]);
        }
        
        return view('dashboard.kelompok.index', compact('performanceData', 'kelompok', 'laporanCount', 'recentLaporan', 'prediksis', 'userRanking'));
    }
    
    /**
     * Get performance data for all kelompok
     * Calculate based on: Laporan (40%), Rata-rata waktu penyelesaian (30%), Konsistensi (30%)
     */
    private function getAllKelompokPerformance()
    {
        try {
            $kelompoks = Kelompok::all();
            $performanceData = [];
            
            // Get total laporan per kelompok (bulan ini)
            $totalLaporanPerKelompok = LaporanKaryawan::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->select('kelompok_id', DB::raw('COUNT(*) as total'))
                ->groupBy('kelompok_id')
                ->pluck('total', 'kelompok_id')
                ->toArray();
            
            // Get max values for normalization
            $maxLaporan = !empty($totalLaporanPerKelompok) ? max($totalLaporanPerKelompok) : 1;
            
            // Get konsistensi (laporan hari ini dan minggu ini)
            $laporanHariIniPerKelompok = LaporanKaryawan::whereDate('created_at', today())
                ->select('kelompok_id', DB::raw('COUNT(*) as total'))
                ->groupBy('kelompok_id')
                ->pluck('total', 'kelompok_id')
                ->toArray();
            
            $laporanMingguIniPerKelompok = LaporanKaryawan::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->select('kelompok_id', DB::raw('COUNT(*) as total'))
                ->groupBy('kelompok_id')
                ->pluck('total', 'kelompok_id')
                ->toArray();
            
            $maxHariIni = !empty($laporanHariIniPerKelompok) ? max($laporanHariIniPerKelompok) : 1;
            $maxMingguIni = !empty($laporanMingguIniPerKelompok) ? max($laporanMingguIniPerKelompok) : 1;
            
            foreach ($kelompoks as $kelompok) {
                $score = 0;
                
                // 1. Laporan (70% weight) - total laporan bulan ini
                $totalLaporan = $totalLaporanPerKelompok[$kelompok->id] ?? 0;
                $laporanScore = ($totalLaporan / $maxLaporan) * 100;
                $score += $laporanScore * 0.7;
                
                // 2. Konsistensi (30% weight) - laporan hari ini dan minggu ini
                $laporanHariIni = $laporanHariIniPerKelompok[$kelompok->id] ?? 0;
                $laporanMingguIni = $laporanMingguIniPerKelompok[$kelompok->id] ?? 0;
                
                $hariScore = ($maxHariIni > 0) ? ($laporanHariIni / $maxHariIni) * 50 : 0;
                $mingguScore = ($maxMingguIni > 0) ? ($laporanMingguIni / $maxMingguIni) * 50 : 0;
                $consistencyScore = $hariScore + $mingguScore;
                $score += $consistencyScore * 0.3;
                
                // Cap score at 0-100
                $finalScore = min(100, max(0, $score));
                
                $performanceData[] = [
                    'nama' => $kelompok->nama_kelompok,
                    'skor' => round($finalScore, 2)
                ];
            }
            
            // Sort by score descending
            usort($performanceData, function($a, $b) {
                return $b['skor'] <=> $a['skor'];
            });
            
            return $performanceData;
            
        } catch (\Exception $e) {
            // Log error and return empty array
            \Log::error('Error calculating kelompok performance: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get admin dashboard statistics
     */
    public function getAdminStats()
    {
        try {
            // Calculate total reports
            $totalLaporan = LaporanKaryawan::count();
            
            // Calculate today's reports
            $laporanHariIni = LaporanKaryawan::whereDate('created_at', today())->count();
            
            // Calculate pending review (reports older than 1 day without review)
            $pendingReview = LaporanKaryawan::where('created_at', '<', now()->subDay())->count();
            
            // Calculate average reports per day (last 30 days)
            $avgPerHari = $this->calculateAvgPerHari();
            
            $stats = [
                // Basic counts
                'total_kelompok' => Kelompok::count(),
                'total_karyawan' => Karyawan::count(),
                'total_users' => User::where('role', 'kelompok')->count(),
                
                // Reports statistics - Updated with proper calculations
                'total_laporan' => $totalLaporan,
                'laporan_hari_ini' => $laporanHariIni,
                'laporan_bulan_ini' => LaporanKaryawan::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'laporan_pending' => $pendingReview,
                
                // New statistics for the widget
                'totalLaporan' => $totalLaporan,
                'laporanHariIni' => $laporanHariIni,
                'pendingReview' => $pendingReview,
                'avgPerHari' => $avgPerHari,
                
                // Job statistics
                'total_job' => 0, // Will be implemented when JobPekerjaan structure is clear
                'job_selesai' => 0, // Will be implemented when JobPekerjaan structure is clear
                'job_progress' => 0, // Will be implemented when JobPekerjaan structure is clear
                
                // Performance metrics
                'avg_waktu_penyelesaian' => 0, // Will be calculated when waktu_penyelesaian column is added
                'best_performing_group' => $this->getBestPerformingGroup(),
                'monthly_trend' => $this->getMonthlyTrend(),
                
                // System health
                'system_health' => $this->getSystemHealth(),
                'recent_activities' => $this->getRecentActivities()
            ];

            return response()->json(['stats' => $stats]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get kelompok dashboard statistics
     */
    public function getKelompokStats()
    {
        try {
            $kelompok = auth()->user()->kelompok;
            
            if (!$kelompok) {
                return response()->json([
                    'stats' => [
                        'nama_kelompok' => 'Kelompok Tidak Ditemukan',
                        'shift' => 'N/A',
                        'total_anggota' => 0,
                        'laporan_hari_ini' => 0,
                        'laporan_bulan_ini' => 0,
                        'laporan_pending' => 0,
                        'avg_waktu_penyelesaian' => 0,
                        'performance_ranking' => null,
                        'monthly_performance' => [],
                        'recent_laporan' => [],
                        'upcoming_tasks' => [],
                    ]
                ]);
            }

            // Get performance ranking - ensure it always returns data
            $performanceRanking = $this->getGroupPerformanceRanking($kelompok->id);
            
            // Ensure ranking_data is always present and not empty
            if (empty($performanceRanking['ranking_data'])) {
                // Fallback: create basic ranking data from all kelompok
                $allKelompoks = Kelompok::all();
                $performanceRanking['ranking_data'] = [];
                foreach ($allKelompoks as $k) {
                    $score = $this->calculatePerformanceScore($k->id);
                    $performanceRanking['ranking_data'][] = [
                        'id' => $k->id,
                        'nama' => $k->nama_kelompok,
                        'score' => round($score, 2)
                    ];
                }
                // Sort by score
                usort($performanceRanking['ranking_data'], function($a, $b) {
                    return $b['score'] <=> $a['score'];
                });
            }

            $stats = [
                // Group info
                'nama_kelompok' => $kelompok->nama_kelompok,
                'shift' => $kelompok->shift,
                'total_anggota' => $kelompok->karyawan->count(),
                
                // Reports for this group
                'laporan_hari_ini' => LaporanKaryawan::where('kelompok_id', $kelompok->id)
                    ->whereDate('created_at', today())->count(),
                
                'laporan_bulan_ini' => LaporanKaryawan::where('kelompok_id', $kelompok->id)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                
                'laporan_pending' => LaporanKaryawan::where('kelompok_id', $kelompok->id)
                    ->whereDate('created_at', '<', now()->subDays(1))->count(),
                
                // Performance metrics
                'avg_waktu_penyelesaian' => $this->calculateAvgWaktuPenyelesaian($kelompok->id),
                
                'performance_ranking' => $performanceRanking,
                'monthly_performance' => $this->getGroupMonthlyPerformance($kelompok->id),
                
                // Recent activities
                'recent_laporan' => $this->getGroupRecentLaporan($kelompok->id),
                'upcoming_tasks' => $this->getUpcomingTasks($kelompok->id),
                
            ];

            return response()->json(['stats' => $stats]);

        } catch (\Exception $e) {
            \Log::error('Error in getKelompokStats: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Get chart data for kelompok dashboard
     */
    public function getKelompokChartData()
    {
        try {
            $kelompok = auth()->user()->kelompok;
            
            if (!$kelompok) {
                return response()->json(['error' => 'Kelompok tidak ditemukan'], 404);
            }

            $data = [
                // Daily performance
                'daily_performance' => $this->getGroupDailyPerformance($kelompok->id),
                
                // Weekly trend
                'weekly_trend' => $this->getGroupWeeklyTrend($kelompok->id),
                
                // Member performance
                'member_performance' => $this->getMemberPerformanceChart($kelompok->id),
                
                // Job type distribution
                'job_distribution' => $this->getGroupJobDistribution($kelompok->id)
            ];

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get best performing group
     */
    private function getBestPerformingGroup()
    {
        // For now, return first kelompok since waktu_penyelesaian column doesn't exist
        $kelompok = Kelompok::first();
        return $kelompok ? $kelompok->nama_kelompok : 'Belum ada data';
    }

    /**
     * Get monthly trend
     */
    private function getMonthlyTrend()
    {
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = LaporanKaryawan::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $trend[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }
        return $trend;
    }


    /**
     * Get system health
     */
    private function getSystemHealth()
    {
        $totalReports = LaporanKaryawan::count();
        $pendingReports = LaporanKaryawan::whereDate('created_at', '<', now()->subDays(1))->count();
        $completedReports = $totalReports - $pendingReports;
        
        $health = 'excellent';
        if ($pendingReports > $totalReports * 0.3) {
            $health = 'warning';
        }
        if ($pendingReports > $totalReports * 0.5) {
            $health = 'critical';
        }

        return [
            'status' => $health,
            'pending_rate' => $totalReports > 0 ? round(($pendingReports / $totalReports) * 100, 2) : 0,
            'completion_rate' => $totalReports > 0 ? round(($completedReports / $totalReports) * 100, 2) : 0
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities()
    {
        $activities = [];
        
        // Recent reports
        $recentReports = LaporanKaryawan::with(['kelompok'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentReports as $report) {
            $activities[] = [
                'type' => 'laporan',
                'message' => "Laporan baru dari {$report->nama} ({$report->kelompok->nama_kelompok})",
                'time' => $report->created_at->diffForHumans(),
                'status' => 'completed'
            ];
        }

        return collect($activities)->sortByDesc('time')->take(8)->values();
    }

    /**
     * Get group performance ranking based on real performance metrics
     */
    private function getGroupPerformanceRanking($kelompokId)
    {
        try {
            // Get all kelompok with their performance scores
            $kelompoks = Kelompok::with(['karyawan'])->get();
            
            $performances = [];
            
            foreach ($kelompoks as $kelompok) {
                // Calculate performance score (0-100)
                $score = $this->calculatePerformanceScore($kelompok->id);
                
                $performances[] = [
                    'id' => $kelompok->id,
                    'nama' => $kelompok->nama_kelompok,
                    'score' => $score
                ];
            }
            
            // Sort by score descending (higher score = better)
            usort($performances, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });
            
            // Find current kelompok rank
            $rank = 1;
            foreach ($performances as $index => $perf) {
                if ($perf['id'] == $kelompokId) {
                    $rank = $index + 1;
                    break;
                }
            }
            
            // Calculate average time for this kelompok
            $avgTime = $this->calculateAvgWaktuPenyelesaian($kelompokId);
            
            // Find current kelompok score
            $currentIndex = array_search($kelompokId, array_column($performances, 'id'));
            $currentScore = $currentIndex !== false ? $performances[$currentIndex]['score'] : 0;
            
            return [
                'rank' => $rank,
                'total_groups' => count($performances),
                'avg_time' => round($avgTime, 1),
                'score' => round($currentScore, 2),
                'id' => $kelompokId, // Current kelompok ID for highlighting
                'ranking_data' => $performances // For chart data
            ];
            
        } catch (\Exception $e) {
            // Get all kelompok for fallback data
            $kelompoks = Kelompok::all();
            $performances = [];
            
            foreach ($kelompoks as $kelompok) {
                $performances[] = [
                    'id' => $kelompok->id,
                    'nama' => $kelompok->nama_kelompok,
                    'score' => 0
                ];
            }
            
            return [
                'rank' => 1,
                'total_groups' => count($performances) ?: Kelompok::count(),
                'avg_time' => 0,
                'score' => 0,
                'id' => $kelompokId,
                'ranking_data' => $performances
            ];
        }
    }
    
    /**
     * Calculate performance score for a kelompok (0-100)
     * Based on: laporan count (40%), waktu penyelesaian (30%), consistency (30%)
     */
    private function calculatePerformanceScore($kelompokId)
    {
        $score = 0;
        
        // 1. Laporan count (40% weight) - bulan ini
        $laporanBulanIni = LaporanKaryawan::where('kelompok_id', $kelompokId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Get max laporan count for normalization
        $maxLaporan = LaporanKaryawan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->select('kelompok_id', DB::raw('COUNT(*) as total'))
            ->groupBy('kelompok_id')
            ->max('total') ?: 1;
        
        $laporanScore = ($laporanBulanIni / $maxLaporan) * 100;
        $score += $laporanScore * 0.4;
        
        // 2. Average waktu penyelesaian (30% weight) - lower is better (in days)
        $avgWaktu = $this->calculateAvgWaktuPenyelesaian($kelompokId);
        if ($avgWaktu > 0) {
            // Invert: lower time = higher score
            // Assume max time is 7 days, so 0 days = 100, 7+ days = 0
            // Scale: 0 hari = 100, 7 hari = 0
            $waktuScore = max(0, 100 - (($avgWaktu / 7) * 100));
            $score += $waktuScore * 0.3;
        } else {
            // No data, give neutral score (50% of 30% = 15%)
            $score += 50 * 0.3;
        }
        
        // 3. Consistency (30% weight) - laporan hari ini dan minggu ini
        $laporanHariIni = LaporanKaryawan::where('kelompok_id', $kelompokId)
            ->whereDate('created_at', today())
            ->count();
        
        $laporanMingguIni = LaporanKaryawan::where('kelompok_id', $kelompokId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        
        // Max for normalization
        $maxHariIni = LaporanKaryawan::whereDate('created_at', today())
            ->select('kelompok_id', DB::raw('COUNT(*) as total'))
            ->groupBy('kelompok_id')
            ->max('total') ?: 1;
            
        $maxMingguIni = LaporanKaryawan::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->select('kelompok_id', DB::raw('COUNT(*) as total'))
            ->groupBy('kelompok_id')
            ->max('total') ?: 1;
        
        $hariScore = ($laporanHariIni / $maxHariIni) * 50;
        $mingguScore = ($laporanMingguIni / $maxMingguIni) * 50;
        $consistencyScore = $hariScore + $mingguScore;
        $score += $consistencyScore * 0.3;
        
        return min(100, max(0, $score)); // Cap at 0-100
    }
    
    /**
     * Calculate average waktu penyelesaian for a kelompok
     * Based on laporan data
     */
    private function calculateAvgWaktuPenyelesaian($kelompokId)
    {
        // Return 0 since job pekerjaan is removed
        return 0;
    }

    /**
     * Get group monthly performance
     */
    private function getGroupMonthlyPerformance($kelompokId)
    {
        $performance = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = LaporanKaryawan::where('kelompok_id', $kelompokId)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->count();

            $performance[] = [
                'month' => $date->format('M Y'),
                'avg_time' => $count // Use count instead of avg_time for now
            ];
        }
        return $performance;
    }

    /**
     * Get group recent laporan
     */
    private function getGroupRecentLaporan($kelompokId)
    {
        return LaporanKaryawan::where('kelompok_id', $kelompokId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
        ->get()
        ->map(function($laporan) {
            return [
                'id' => $laporan->id,
                'karyawan' => $laporan->nama,
                'waktu_penyelesaian' => 0, // Will be calculated when waktu_penyelesaian column is added
                'status' => 'completed',
                'created_at' => $laporan->created_at->format('d/m/Y H:i')
            ];
        });
    }

    /**
     * Get upcoming tasks
     */
    private function getUpcomingTasks($kelompokId)
    {
        return collect([]);
    }


    /**
     * Get monthly reports chart data
     */
    private function getMonthlyReportsChart()
    {
        $data = [];
        $labels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M');
            $data[] = LaporanKaryawan::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Laporan Bulanan',
                'data' => $data,
                'borderColor' => 'rgb(59, 130, 246)',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'tension' => 0.4
            ]]
        ];
    }

    /**
     * Get group performance chart data
     */
    private function getGroupPerformanceChart()
    {
        $groups = Kelompok::withCount('karyawan')->get();

        $labels = [];
        $data = [];

        foreach ($groups as $group) {
            $labels[] = $group->nama_kelompok;
            
            // Use karyawan count as performance metric for now
            $data[] = $group->karyawan_count;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Jumlah Anggota',
                'data' => $data,
                'backgroundColor' => [
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(168, 85, 247, 0.8)'
                ]
            ]]
        ];
    }

    /**
     * Get job completion chart data
     */
    private function getJobCompletionChart()
    {
        return [
            'labels' => ['Selesai', 'Progress', 'Pending'],
            'datasets' => [[
                'data' => [0, 0, 0],
                'backgroundColor' => [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ]
            ]]
        ];
    }


    /**
     * Get group daily performance
     */
    private function getGroupDailyPerformance($kelompokId)
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('D');
            
            $count = LaporanKaryawan::where('kelompok_id', $kelompokId)
            ->whereDate('created_at', $date->toDateString())
            ->count();
            
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Jumlah Laporan',
                'data' => $data,
                'borderColor' => 'rgb(59, 130, 246)',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'tension' => 0.4
            ]]
        ];
    }

    /**
     * Get group weekly trend
     */
    private function getGroupWeeklyTrend($kelompokId)
    {
        $data = [];
        $labels = [];
        
        for ($i = 3; $i >= 0; $i--) {
            $startWeek = now()->subWeeks($i)->startOfWeek();
            $endWeek = now()->subWeeks($i)->endOfWeek();
            $labels[] = 'Week ' . (4 - $i);
            
            $count = LaporanKaryawan::where('kelompok_id', $kelompokId)
            ->whereBetween('created_at', [$startWeek, $endWeek])
            ->count();
            
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Jumlah Laporan',
                'data' => $data,
                'borderColor' => 'rgb(16, 185, 129)',
                'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                'tension' => 0.4
            ]]
        ];
    }

    /**
     * Get member performance chart
     */
    private function getMemberPerformanceChart($kelompokId)
    {
        // Get unique names from laporan_karyawan for this kelompok
        $laporanData = LaporanKaryawan::where('kelompok_id', $kelompokId)
            ->select('nama', DB::raw('COUNT(*) as count'))
            ->groupBy('nama')
            ->get();

        $labels = [];
        $data = [];

        foreach ($laporanData as $item) {
            $labels[] = $item->nama;
            $data[] = $item->count;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Jumlah Laporan',
                'data' => $data,
                'backgroundColor' => 'rgba(245, 158, 11, 0.8)'
            ]]
        ];
    }

    /**
     * Get group job distribution
     */
    private function getGroupJobDistribution($kelompokId)
    {
        return [
            'labels' => ['Maintenance', 'Installation', 'Repair'],
            'datasets' => [[
                'data' => [0, 0, 0],
                'backgroundColor' => [
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(168, 85, 247, 0.8)'
                ]
            ]]
        ];
    }

    /**
     * Calculate average reports per day for the last 30 days
     */
    private function calculateAvgPerHari()
    {
        try {
            // Get reports from the last 30 days
            $thirtyDaysAgo = now()->subDays(30);
            $totalReports = LaporanKaryawan::where('created_at', '>=', $thirtyDaysAgo)->count();
            
            // Calculate average per day
            $avgPerHari = $totalReports / 30;
            
            // Round to 1 decimal place
            return round($avgPerHari, 1);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get admin chart data
     */
    public function getAdminChartData()
    {
        try {
            $data = [
                // Monthly reports trend
                'monthly_reports' => $this->getMonthlyReportsChart(),
                
                // Performance by group
                'group_performance' => $this->getGroupPerformanceChart(),
                
                // Job completion trend
                'job_completion' => $this->getJobCompletionChart()
            ];

            return $data;

        } catch (\Exception $e) {
            return [
                'monthly_reports' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    'datasets' => [[
                        'label' => 'Laporan Bulanan',
                        'data' => [0, 0, 0, 0, 0, 0],
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'tension' => 0.4
                    ]]
                ],
                'group_performance' => [
                    'labels' => ['Kelompok 1', 'Kelompok 2', 'Kelompok 3'],
                    'datasets' => [[
                        'label' => 'Jumlah Anggota',
                        'data' => [0, 0, 0],
                        'backgroundColor' => [
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)'
                        ]
                    ]]
                ],
                'job_completion' => [
                    'labels' => ['Selesai', 'Progress', 'Pending'],
                    'datasets' => [[
                        'data' => [0, 0, 0],
                        'backgroundColor' => [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ]
                    ]]
                ],
            ];
        }
    }
}