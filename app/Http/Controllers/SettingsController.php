<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsController extends Controller
{
    /**
     * Display admin settings page
     */
    public function adminIndex()
    {
        $kelompoks = Kelompok::with(['karyawan', 'users'])->get();
        $users = User::where('role', 'atasan')->get();
        
        // Get system statistics directly
        $systemStats = $this->getSystemStatsData();
        
        return view('dashboard.atasan.settings', compact('kelompoks', 'users', 'systemStats'));
    }

    /**
     * Display kelompok settings page
     */
    public function kelompokIndex()
    {
        $kelompok = auth()->user()->kelompok;
        $karyawans = $kelompok ? $kelompok->karyawan : collect();
        
        return view('dashboard.kelompok.settings', compact('kelompok', 'karyawans'));
    }

    /**
     * Update admin settings
     */
    public function updateAdminSettings(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
            'system_description' => 'nullable|string',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'default_shift' => 'required|in:Shift 1,Shift 2',
            'max_upload_size' => 'required|integer|min:1|max:50',
            'auto_backup' => 'boolean',
            'notification_email' => 'nullable|email',
            'maintenance_mode' => 'boolean'
        ]);

        try {
            // Update system settings in database or config
            $settings = [
                'system_name' => $request->system_name,
                'system_description' => $request->system_description,
                'timezone' => $request->timezone,
                'date_format' => $request->date_format,
                'default_shift' => $request->default_shift,
                'max_upload_size' => $request->max_upload_size,
                'auto_backup' => $request->auto_backup ?? false,
                'notification_email' => $request->notification_email,
                'maintenance_mode' => $request->maintenance_mode ?? false,
                'updated_at' => now()
            ];

            // Store settings in database or file
            $this->storeSettings('admin', $settings);

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan admin berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pengaturan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update kelompok settings
     */
    public function updateKelompokSettings(Request $request)
    {
        $kelompok = auth()->user()->kelompok;
        
        if (!$kelompok) {
            return response()->json([
                'success' => false,
                'message' => 'Kelompok tidak ditemukan!'
            ], 404);
        }

        $request->validate([
            'nama_kelompok' => 'required|string|max:255',
            'shift' => 'required|in:Shift 1,Shift 2',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'password' => 'nullable|string|min:6',
            'notification_settings' => 'array',
            'work_schedule' => 'array'
        ]);

        try {
            DB::beginTransaction();

            // Update kelompok data
            $kelompok->update([
                'nama_kelompok' => $request->nama_kelompok,
                'shift' => $request->shift,
                'deskripsi' => $request->deskripsi,
                'lokasi' => $request->lokasi,
                'telepon' => $request->telepon,
                'email' => $request->email,
            ]);

            // Update password if provided
            if ($request->password) {
                $kelompok->user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // Store additional settings
            $settings = [
                'notification_settings' => $request->notification_settings ?? [],
                'work_schedule' => $request->work_schedule ?? [],
                'updated_at' => now()
            ];

            $this->storeSettings('kelompok_' . $kelompok->id, $settings);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan kelompok berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pengaturan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system statistics for admin
     */
    public function getSystemStats()
    {
        try {
            $stats = [
                'total_kelompok' => Kelompok::count(),
                'total_karyawan' => DB::table('karyawan')->count(),
                'total_laporan' => DB::table('laporan_karyawan')->count(),
                'total_job' => DB::table('job_pekerjaan')->count(),
                'active_users' => User::where('role', 'kelompok')->count(),
                'disk_usage' => $this->getDiskUsage(),
                'last_backup' => $this->getLastBackupDate(),
                'system_uptime' => $this->getSystemUptime()
            ];

            return response()->json(['stats' => $stats]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get system statistics data (for direct use in controller)
     */
    public function getSystemStatsData()
    {
        try {
            return [
                'total_kelompok' => Kelompok::count(),
                'total_karyawan' => DB::table('karyawan')->count(),
                'total_laporan' => DB::table('laporan_karyawan')->count(),
                'total_job' => DB::table('job_pekerjaan')->count(),
                'active_users' => User::where('role', 'kelompok')->count(),
                'disk_usage' => $this->getDiskUsage(),
                'last_backup' => $this->getLastBackupDate(),
                'system_uptime' => $this->getSystemUptime()
            ];

        } catch (\Exception $e) {
            return [
                'total_kelompok' => 0,
                'total_karyawan' => 0,
                'total_laporan' => 0,
                'total_job' => 0,
                'active_users' => 0,
                'disk_usage' => ['percentage' => 0, 'used' => '0 B', 'total' => '0 B'],
                'last_backup' => 'Belum pernah',
                'system_uptime' => '0 hari'
            ];
        }
    }

    /**
     * Backup system data
     */
    public function backupSystem()
    {
        try {
            $backupData = [
                'kelompoks' => Kelompok::with(['karyawan', 'users'])->get(),
                'karyawans' => DB::table('karyawans')->get(),
                'laporan_karyawans' => DB::table('laporan_karyawans')->get(),
                'job_pekerjaans' => DB::table('job_pekerjaans')->get(),
                'users' => DB::table('users')->get(),
                'backup_date' => now()->toISOString()
            ];

            $fileName = 'backup_' . now()->format('Y-m-d_H-i-s') . '.json';
            $filePath = storage_path('app/backups/' . $fileName);
            
            // Ensure directory exists
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            file_put_contents($filePath, json_encode($backupData, JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => 'Backup berhasil dibuat!',
                'file_name' => $fileName
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore system from backup
     */
    public function restoreSystem(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json'
        ]);

        try {
            $file = $request->file('backup_file');
            $content = file_get_contents($file->getPathname());
            $backupData = json_decode($content, true);

            if (!$backupData) {
                throw new \Exception('File backup tidak valid!');
            }

            DB::beginTransaction();

            // Clear existing data
            DB::table('laporan_karyawans')->truncate();
            DB::table('job_pekerjaans')->truncate();
            DB::table('karyawans')->truncate();
            DB::table('kelompoks')->truncate();
            DB::table('users')->where('role', '!=', 'admin')->delete();

            // Restore data
            if (isset($backupData['kelompoks'])) {
                foreach ($backupData['kelompoks'] as $kelompok) {
                    Kelompok::create($kelompok);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di-restore dari backup!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal restore data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update notifications settings
     */
    public function updateNotifications(Request $request)
    {
        $kelompok = auth()->user()->kelompok;
        
        if (!$kelompok) {
            return response()->json([
                'success' => false,
                'message' => 'Kelompok tidak ditemukan!'
            ], 404);
        }

        try {
            $settings = [
                'notification_settings' => $request->all(),
                'updated_at' => now()
            ];

            $this->storeSettings('kelompok_notifications_' . $kelompok->id, $settings);

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan notifikasi berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pengaturan notifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update work schedule
     */
    public function updateWorkSchedule(Request $request)
    {
        $kelompok = auth()->user()->kelompok;
        
        if (!$kelompok) {
            return response()->json([
                'success' => false,
                'message' => 'Kelompok tidak ditemukan!'
            ], 404);
        }

        try {
            $settings = [
                'work_schedule' => $request->all(),
                'updated_at' => now()
            ];

            $this->storeSettings('kelompok_work_schedule_' . $kelompok->id, $settings);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal kerja berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jadwal kerja: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly reports count
     */
    public function getMonthlyReports()
    {
        try {
            $kelompok = auth()->user()->kelompok;
            
            if (!$kelompok) {
                return response()->json(['count' => 0]);
            }

            $count = \App\Models\LaporanKaryawan::whereHas('karyawan', function($query) use ($kelompok) {
                $query->where('kelompok_id', $kelompok->id);
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

            return response()->json(['count' => $count]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Verify current password if changing password
            if ($request->new_password && !Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak sesuai!'
                ], 400);
            }

            $updateData = [
                'name' => $request->name,
                'email' => $request->email
            ];

            // Update password if provided
            if ($request->new_password) {
                $updateData['password'] = Hash::make($request->new_password);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                $avatar->storeAs('avatars', $fileName, 'public');
                $updateData['avatar'] = $fileName;
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store settings in database or file
     */
    private function storeSettings($key, $settings)
    {
        // Store in database settings table or config file
        // This is a simplified implementation
        $filePath = storage_path('app/settings/' . $key . '.json');
        
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        file_put_contents($filePath, json_encode($settings, JSON_PRETTY_PRINT));
    }

    /**
     * Get settings from database or file
     */
    private function getSettings($key)
    {
        $filePath = storage_path('app/settings/' . $key . '.json');
        
        if (file_exists($filePath)) {
            return json_decode(file_get_contents($filePath), true);
        }

        return [];
    }

    /**
     * Get disk usage statistics
     */
    private function getDiskUsage()
    {
        $totalBytes = disk_total_space(storage_path());
        $freeBytes = disk_free_space(storage_path());
        $usedBytes = $totalBytes - $freeBytes;
        
        return [
            'total' => $this->formatBytes($totalBytes),
            'used' => $this->formatBytes($usedBytes),
            'free' => $this->formatBytes($freeBytes),
            'percentage' => round(($usedBytes / $totalBytes) * 100, 2)
        ];
    }

    /**
     * Get last backup date
     */
    private function getLastBackupDate()
    {
        $backupDir = storage_path('app/backups');
        
        if (!is_dir($backupDir)) {
            return 'Belum pernah backup';
        }

        $files = glob($backupDir . '/*.json');
        
        if (empty($files)) {
            return 'Belum pernah backup';
        }

        $latestFile = max($files);
        return date('Y-m-d H:i:s', filemtime($latestFile));
    }

    /**
     * Get system uptime
     */
    private function getSystemUptime()
    {
        // Simplified uptime calculation
        $uptime = time() - filemtime(storage_path('app/settings'));
        return $this->formatUptime($uptime);
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Format uptime to human readable
     */
    private function formatUptime($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return "{$days} hari, {$hours} jam, {$minutes} menit";
    }
}
