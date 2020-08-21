<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UnitDaftarReq;
use App\Unit;
use App\Kursus;
use App\KursusUnit;
use Illuminate\Support\Facades\Storage;
use App\Galeri;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    public function index()
    {

        return view('web.web_unit', ['kursus' => Kursus::all()]);
    }

    public function post(UnitDaftarReq $request)
    {
        $data = $request->all();

        // $file = $data['bukti_alumni'];
        // $file_name = 'Pendaftar Unit-' . $data['nama_unit'] . '.' . $file->getClientOriginalExtension();
        // $data['bukti_alumni'] =  $file->storeAs('public/uploads/bukti', $file_name);
        $data['slug'] = Str::slug($data['nama_unit'], '-');
        $data['bukti_alumni'] =  $request->file('bukti_alumni')->store('bukti', 'public');
        $data['status'] = '0';

        Unit::create($data);
        return redirect()->back()->with(['message' => 'Pendaftaran Unit Berhasil Dikirim']);
    }

    public function show($slug)
    {
        $unit = Unit::with(['kursus_unit', 'mentor', 'fasilitas'])
            ->where('slug', $slug)
            ->firstOrFail();

        $galeri = Galeri::with('unit')
            ->where('unit_id', $unit->id)
            ->latest()
            ->get();


        $kursus_unit = KursusUnit::with(['kursus', 'unit'])
            ->where('unit_id', $unit->id)
            ->get();

        // dd($kursus_check);

        return view('web.web_unit_kursus', [
            'unit' => $unit,
            'kursus_unit' => $kursus_unit,
            'galeri' => $galeri
        ]);
    }

    public function show_kursus($slug, $slug_kursus)
    {
        $unit = Unit::with(['kursus_unit', 'mentor', 'fasilitas'])
            ->where('slug', $slug)
            ->firstOrFail();

        $kursus = Kursus::where('slug', $slug_kursus)->firstOrFail();

        $nama_kursus = KursusUnit::with(['kursus', 'unit'])
            ->where('kursus_id', $kursus->id)
            ->where('unit_id', $unit->id)
            ->first();

        $kursus_lainya = KursusUnit::with(['kursus', 'unit'])
            ->where('unit_id', $unit->id)
            ->get();

        // dd($nama_kursus);

        return view('web.web_unit_kursus_detail', [
            'unit' => $unit,
            'kursus_unit' => $nama_kursus,
            'kursus_lainya' => $kursus_lainya
        ]);
    }
}
