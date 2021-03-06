<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kursus;
use App\Unit;
use App\Komentar;

class DashboardController extends Controller
{

    public function index()
    {
        $kursus = Kursus::all()->count();
        $unit = Unit::where('status', '1')->count();
        $pendaftar = Unit::where('status', '0')->count();
        $komentar = Komentar::all()->count();

        return view(
            'admin.dashboard.index',
            [
                'kursus' => $kursus,
                'unit' => $unit,
                'pendaftar' => $pendaftar,
                'komentar' => $komentar,
            ]
        );
    }
}
