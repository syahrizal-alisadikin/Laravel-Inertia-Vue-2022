<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $findUser = User::where('email', $user->email)->first();
            if ($findUser) {
                Auth::login($findUser);
                return redirect()->route('dashboard');
            } else {

                return redirect()->route('login')->with('error', 'User ' . $user->name . ' Belum Terdaftar');
            }
        } catch (\Exception $e) {

            return redirect()->route('login')->with('error', 'Google Login Error ' . $e->getMessage());
        }
    }

    public function handleForgotPassword(Request $request)
    {
        // validasi email
        $this->validate($request, [
            'email' => 'required|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->sendPasswordResetNotification($user->getEmailForPasswordReset());
            return redirect()->route('login')->with('success', 'Email Reset Password Telah Dikirim');
        } else {
            return redirect('/forgot-password')->with('error', 'Email Tidak Terdaftar');
        }
    }
}
