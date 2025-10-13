<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class ManajemenController extends Controller
{
    public function index()
    {
        $kelompoks = Kelompok::with(['karyawan', 'users'])->orderBy('created_at', 'desc')->get();
        $karyawans = Karyawan::with('kelompok')->orderBy('created_at', 'desc')->get();
        
        return view('dashboard.atasan.manajemen', compact('kelompoks', 'karyawans'));
    }

    public function kelompok()
    {
        $kelompoks = Kelompok::with(['karyawan', 'users'])->orderBy('created_at', 'desc')->get();
        $karyawans = Karyawan::with('kelompok')->orderBy('created_at', 'desc')->get();
        
        return view('dashboard.atasan.kelompok', compact('kelompoks', 'karyawans'));
    }

    public function karyawan()
    {
        $kelompoks = Kelompok::with(['karyawan', 'users'])->orderBy('created_at', 'desc')->get();
        $karyawans = Karyawan::with('kelompok')->orderBy('created_at', 'desc')->get();
        
        return view('dashboard.atasan.karyawan', compact('kelompoks', 'karyawans'));
    }
}

