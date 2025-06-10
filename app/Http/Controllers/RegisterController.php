<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\YourOtpMailable;
use App\Models\User;

class RegisterController extends Controller
{
    // Optional: Show registration form
    public function showRegistrationForm()
    {
        return view('register');
    }

    // Handle registration and send OTP
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $otp = rand(100000, 999999);

        Session::put('register_data', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Session::put('otp_code', $otp);
        Session::put('otp_generated_at', now());
        Session::put('otp_last_sent_time', now());

        Mail::to($request->email)->send(new YourOtpMailable($otp));

        return redirect()->route('otp.verify')->with('success', 'OTP sent to your email!');
    }

    // Resend OTP with cooldown
    public function sendotp(Request $request)
    {
        $lastSentTime = Session::get('otp_last_sent_time');
        $now = now();

        if ($lastSentTime && $now->diffInSeconds($lastSentTime) < 90) {
            return back()->with('error', 'Please wait before requesting a new OTP.');
        }

        $otp = rand(100000, 999999);
        Session::put('otp_code', $otp);
        Session::put('otp_generated_at', $now);
        Session::put('otp_last_sent_time', $now);

        $registerData = Session::get('register_data');

        if (!$registerData || !isset($registerData['email'])) {
            return redirect('/')->with('error', 'Session expired. Please register again.');
        }

        Mail::to($registerData['email'])->send(new YourOtpMailable($otp));

        return back()->with('success', 'New OTP has been sent to your email.');
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $enteredOtp = $request->otp;
        $storedOtp = Session::get('otp_code');
        $generatedAt = Session::get('otp_generated_at');
        $expirySeconds = 90;

        if (!$storedOtp || !$generatedAt || now()->diffInSeconds($generatedAt) > $expirySeconds) {
            Session::forget(['otp_code', 'otp_generated_at', 'register_data']);
            return redirect('/')->with('error', 'OTP expired. Please register again.');
        }

        if ($enteredOtp == $storedOtp) {
            $userData = Session::get('register_data');

            if (!$userData) {
                return redirect('/')->with('error', 'Session expired. Please register again.');
            }

            User::create($userData);

            Session::forget(['otp_code', 'otp_generated_at', 'otp_last_sent_time', 'register_data']);

            return redirect('/')->with('success', 'Registration successful! You can now log in.');
        } else {
            return back()->with('error', 'Invalid OTP. Please try again.');
        }
    }
}
