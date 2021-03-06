<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GaleriKursus;
use App\Kursus;
use App\Http\Requests\GalleryRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gallery = GaleriKursus::with(['kursus'])
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        return view('admin.gallery.index', [
            'gallery' => $gallery
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kursus = Kursus::all();
        return view('admin.gallery.create', [
            'kursus' => $kursus
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GalleryRequest $request)
    {
        $input = $request->all();
        $images = array();

        if ($files = $request->file('gambar')) {
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                $file->move('storage/image', $name);
                $images[] = $name;
            }
        }

        GaleriKursus::insert([
            'kursus_id' => $input['kursus_id'],
            'gambar' =>  implode("|", $images),
        ]);

        return redirect()->route('gallery.index')
            ->with(['status' => 'Data Gallery Berhasil Ditambahkan']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $gallery = GaleriKursus::findOrFail($id);
        $kursus = Kursus::all();

        return view('admin.gallery.edit', [
            'gallery' => $gallery,
            'kursus' => $kursus
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GalleryRequest $request, $id)
    {
        $gallery = GaleriKursus::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('gambar')) {
            if ($gallery->gambar && file_exists(storage_path('app/public/' . $gallery->gambar))) {
                Storage::delete('public/' . $gallery->gambar);
                $data['gambar'] =  $request->file('gambar')->store('gallery', 'public');
            } else {
                $data['gambar'] =  $request->file('gambar')->store('gallery', 'public');
            }
        }

        $gallery->update($data);
        return redirect()->route('gallery.index')
            ->with(['status' => 'Data Gallery Berhasil Diubah']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gallery = GaleriKursus::findOrFail($id);
        $gallery->delete();
        Storage::delete('public/' . $gallery->gambar);

        return redirect()->route('gallery.index')
            ->with(['status'  => 'Data Gallery Berhasil Dihapus']);
    }
}
