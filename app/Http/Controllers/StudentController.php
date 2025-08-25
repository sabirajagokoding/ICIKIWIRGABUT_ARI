<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;

class StudentController extends Controller
{
    public function index()
    {
        return view('csv.upload'); // form upload
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');

        // Baca isi CSV
        $data = array_map('str_getcsv', file($file->getRealPath()));

        // Hilangkan header (nim;nama;kelas)
        $header = array_shift($data);

        foreach ($data as $row) {
            // Karena delimiter ; bukan koma, kita pecah manual
            $row = explode(";", $row[0]);

            Mahasiswa::updateOrCreate(
                ['nim' => $row[0]],
                [
                    'nama' => $row[1],
                    'kelas' => $row[2],
                ]
            );
        }

        return back()->with('success', 'Data CSV berhasil diimport!');
    }

    // app/Http/Controllers/StudentController.php
    public function updateStatus(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'status' => 'required|integer',
        ]);

        $student = Mahasiswa::where('nim', $request->nim)->first();
        if ($student) {
            $student->status = $request->status;
            $student->save();
            return response()->json(['success' => true, 'message' => 'Status updated']);
        }

        return response()->json(['success' => false, 'message' => 'Student not found'], 404);
    }

}
