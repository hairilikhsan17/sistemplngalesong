<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Karyawan;
use App\Models\LaporanKaryawan;
use App\Models\JobPekerjaan;
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
        return view('dashboard.kelompok.index');
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
                'avg_waktu_penyelesaian' => 0, // Will be calculated when waktu_penyelesaian column is added
                
                'performance_ranking' => $this->getGroupPerformanceRanking($kelompok->id),
                'monthly_performance' => $this->getGroupMonthlyPerformance($kelompok->id),
                
                // Job statistics
                'total_job' => 0, // Will be implemented when JobPekerjaan structure is clear
                'job_selesai' => 0, // Will be implemented when JobPekerjaan structure is clear
                
                // Recent activities
                'recent_laporan' => $this->getGroupRecentLaporan($kelompok->id),
                'upcoming_tasks' => $this->getUpcomingTasks($kelompok->id),
                
            ];

            return response()->json(['stats' => $stats]);

        } catch (\Exception $e) {
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
     * Get group performance ranking
     */
    private function getGroupPerformanceRanking($kelompokId)
    {
        // For now, return default ranking since waktu_penyelesaian column doesn't exist
        return [
            'rank' => 1,
            'total_groups' => Kelompok::count(),
            'avg_time' => 0
        ];
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
        // For now, return empty array since JobPekerjaan structure is not clear
        // This can be implemented later when JobPekerjaan structure is defined
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
        // For now, return sample data since JobPekerjaan structure is not clear
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
        // For now, return empty data since JobPekerjaan structure is not clear
        // This can be implemented later when JobPekerjaan structure is defined
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