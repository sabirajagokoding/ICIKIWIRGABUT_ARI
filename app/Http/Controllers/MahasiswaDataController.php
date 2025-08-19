<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function PHPUnit\Framework\returnArgument;

class MahasiswaDataController extends Controller
{
    public function index()
    {   
        $allAttended = Mahasiswa::all()->map(function ($m) {
            $m->updated_at_ = Carbon::parse($m->updated_at)
                ->setTimezone('Asia/Jakarta')
                ->format('H:i'); // hanya jam:menit
            return $m;
        });
        
        return response()->json([
            'allAttended' => $allAttended,
        ]);
        // return view('home', ['allAttended' => $allAttended, 'attended' => $attended, 'notAttended' => $notAttended]);
    }
    public function status()
    {   
        $allAttended = Mahasiswa::all()->count();
        $attended = Mahasiswa::where('status', 1)->get()->count();
        $notAttended = Mahasiswa::where('status', 0)->get()->count();

        return response()->json([
            'allAttended' => $allAttended,
            'attended' => $attended,
            'notAttended' => $notAttended,
        ]);
        // return view('home', ['allAttended' => $allAttended, 'attended' => $attended, 'notAttended' => $notAttended]);
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

    public function update($nim)
    {
        // update langsung status = 1 berdasarkan nim
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        if ($mahasiswa) {
            $mahasiswa->update(['status' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui!',
                'data' => $mahasiswa
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.'
        ], 404);
    }



}
