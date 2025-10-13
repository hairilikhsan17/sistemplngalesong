<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelompok;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlnGalesongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Kelompok
        $kelompok1 = Kelompok::create([
            'id' => Str::uuid(),
            'nama_kelompok' => 'Kelompok 1',
            'shift' => 'Shift 1',
        ]);

        $kelompok2 = Kelompok::create([
            'id' => Str::uuid(),
            'nama_kelompok' => 'Kelompok 2',
            'shift' => 'Shift 2',
        ]);

        // Create Users
        User::create([
            'id' => Str::uuid(),
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'atasan',
        ]);

        User::create([
            'id' => Str::uuid(),
            'username' => 'kelompok1',
            'password' => Hash::make('kelompok1123'),
            'role' => 'karyawan',
            'kelompok_id' => $kelompok1->id,
        ]);

        User::create([
            'id' => Str::uuid(),
            'username' => 'kelompok2',
            'password' => Hash::make('kelompok2123'),
            'role' => 'karyawan',
            'kelompok_id' => $kelompok2->id,
        ]);

        // Create Karyawan
        Karyawan::create([
            'id' => Str::uuid(),
            'nama' => 'Fajar',
            'kelompok_id' => $kelompok1->id,
        ]);

        Karyawan::create([
            'id' => Str::uuid(),
            'nama' => 'Hairil',
            'kelompok_id' => $kelompok1->id,
        ]);

        Karyawan::create([
            'id' => Str::uuid(),
            'nama' => 'Andi',
            'kelompok_id' => $kelompok2->id,
        ]);

        Karyawan::create([
            'id' => Str::uuid(),
            'nama' => 'Budi',
            'kelompok_id' => $kelompok2->id,
        ]);
    }
}





