<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\MailSend;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function register()
    {
        return view('register');
    }

    public function actionregister(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|min:6|confirmed'
        ], [
            'email.unique' => 'Email sudah digunakan, silakan gunakan email lain.',
            'username.unique' => 'Username sudah digunakan, silakan pilih username lain.',
        ]);

        $str = Str::random(100);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'verify_key' => $str,
            'active' => 0,
        ]);

        $details = [
            'username' => $request->username,
            'website' => 'www.ayongoding.com',
            'datetime' => date('Y-m-d H:i:s'),
            'url' => request()->getHttpHost() . '/register/verify/' . $str
        ];

        try {
            Mail::to($user->email)->send(new MailSend($details));
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
            return redirect('register')->with('error', 'Registrasi berhasil, tetapi email verifikasi gagal dikirim.');
        }

        return redirect('register')->with('message', 'Link verifikasi telah dikirim ke Email Anda. Silakan cek email untuk mengaktifkan Akun.');
    }


    public function verify($verify_key)
    {
        // Query #1: Langsung cari dan ambil user
        $user = User::where('verify_key', $verify_key)->first();

        if ($user) {
            // Tidak perlu query lagi, langsung update
            $user->update([
                'active' => 1,
                'verify_key' => null, // Membersihkan key
            ]);

            return redirect()->route('login')->with('message', 'Akun Anda telah berhasil diaktifkan. Silakan login.');
        }

        return "Key verifikasi tidak valid atau akun Anda sudah aktif.";
    }
}