<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MahasiswaDataController extends Controller
{
    public function index()
    {
        $mahasiswas = Mahasiswa::find(1);
        dump($mahasiswas);
        return view('home', compact('mahasiswas'));
    }

    public function show($nim)
    {
        $mhs = DB::table('mahasiswas')->where('nim', $nim)->first();

        if ($mhs) {
            return response()->json($mhs); // kirim data dalam bentuk JSON
        } else {
            return response()->json(['message' => 'Mahasiswa tidak ditemukan'], 404);
        }
    }
}
