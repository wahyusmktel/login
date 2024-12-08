<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Cache;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        try {
            // Periksa apakah pengguna sudah login
            if (Auth::guard('admin')->check()) {
                Alert::info('Already Logged In', 'You are already logged in.');
                return redirect()->route('admin.dashboard');
            }

            // Jika belum login, tampilkan halaman login
            return view('admin.auth.login');
        } catch (\Exception $e) {
            Alert::error('Error', 'An unexpected error occurred. Please try again later.');
            return redirect()->back();
        }
    }

    public function login(Request $request)
    {
        try {
            // Validasi input dengan pesan kustom
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string|min:6',
            ], [
                'username.required' => 'Username wajib diisi.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password harus terdiri dari minimal 6 karakter.',
            ]);

            // Jika validasi gagal, tangkap error
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Ambil kredensial dari input
            $credentials = $request->only('username', 'password');

            // Proses autentikasi
            if (Auth::guard('admin')->attempt($credentials)) {
                $request->session()->regenerate();

                // Periksa apakah 2FA diaktifkan
                if (Auth::guard('admin')->user()->two_factor_enabled) {
                    Alert::info('Two-Factor Authentication', 'Please verify your account using the authentication code.');
                    return redirect()->route('admin.verify-2fa');
                }

                Alert::success('Login Successful', 'Welcome to the dashboard.');
                return redirect()->route('admin.dashboard');
            }

            // Jika autentikasi gagal
            Alert::error('Login Failed', 'Invalid username or password.');
            return redirect()->back()->withInput();
        } catch (ValidationException $e) {
            // Tampilkan pesan error validasi
            foreach ($e->errors() as $error) {
                Alert::error('Validation Error', implode(', ', $error));
            }

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Tangani error yang tidak terduga
            Alert::error('Error', 'An unexpected error occurred. Please try again later.');
            return redirect()->back();
        }
    }

    public function showVerifyTwoFactorForm()
    {
        try {
            // Pastikan pengguna sudah login
            if (!Auth::guard('admin')->check()) {
                Alert::error('Unauthorized', 'You must be logged in to access this page.');
                return redirect()->route('admin.login');
            }

            // Pastikan 2FA diaktifkan untuk pengguna
            if (!Auth::guard('admin')->user()->two_factor_enabled) {
                Alert::info('Two-Factor Authentication Disabled', 'Two-factor authentication is not enabled for your account.');
                return redirect()->route('admin.dashboard');
            }

            // Tampilkan halaman verifikasi 2FA
            return view('admin.auth.verify-2fa');
        } catch (\Exception $e) {
            // Tangani error yang tidak terduga
            Alert::error('Error', 'An unexpected error occurred. Please try again later.');
            return redirect()->back();
        }
    }

    public function logout(Request $request)
    {
        try {
            // Logout pengguna
            Auth::guard('admin')->logout();

            // Hapus data sesi
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Berikan notifikasi sukses
            Alert::success('Logged Out', 'You have been successfully logged out.');

            return redirect()->route('admin.login');
        } catch (\Exception $e) {
            // Tangani error yang tidak terduga
            Alert::error('Error', 'An unexpected error occurred during logout. Please try again.');
            return redirect()->route('admin.dashboard');
        }
    }

    public function enableTwoFactorAuthentication()
    {
        try {
            // Periksa apakah pengguna sudah login
            if (!Auth::guard('admin')->check()) {
                Alert::error('Unauthorized', 'You must be logged in to access this page.');
                return redirect()->route('admin.login');
            }

            $admin = Auth::guard('admin')->user();

            // Periksa apakah 2FA sudah diaktifkan
            if ($admin->two_factor_enabled) {
                Alert::info('Two-Factor Authentication', 'Two-factor authentication is already enabled for your account.');
                return redirect()->route('admin.dashboard');
            }

            $google2fa = new Google2FA();

            // Generate secret key
            $secretKey = $google2fa->generateSecretKey(16);

            // Generate QR Code URL
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                'Admin App', // Nama aplikasi
                $admin->email, // Email admin
                $secretKey
            );

            // Generate QR Code SVG
            $renderer = new ImageRenderer(
                new RendererStyle(400), // Ukuran QR Code
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd() // Format SVG
            );

            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrCodeUrl);

            // Simpan secret key dan aktifkan 2FA
            $admin->google2fa_secret = $secretKey;
            $admin->two_factor_enabled = true;
            $admin->save();

            Alert::success('Two-Factor Authentication Enabled', 'Please scan the QR code using your Google Authenticator app.');
            return view('admin.auth.enable-2fa', compact('qrCodeSvg'));

        } catch (\Exception $e) {
            // Tangani error yang tidak terduga
            Alert::error('Error', 'An unexpected error occurred. Please try again later.');
            return redirect()->back();
        }
    }

    public function verifyTwoFactor(Request $request)
    {
        try {
            $maxAttempts = 5; // Batas maksimal percobaan
            $decayMinutes = 1; // Waktu reset throttle dalam menit

            // Key untuk throttle berdasarkan IP atau pengguna
            $throttleKey = '2fa_attempts:' . $request->ip();

            // Periksa apakah pengguna telah mencapai batas percobaan
            if (Cache::get($throttleKey, 0) >= $maxAttempts) {
                Alert::error('Too Many Attempts', 'You have exceeded the maximum number of attempts. Please try again later.');
                return redirect()->back();
            }

            // Validasi input dengan pesan kustom
            $request->validate([
                'one_time_password' => 'required|numeric',
            ], [
                'one_time_password.required' => 'Kode autentikasi wajib diisi.',
                'one_time_password.numeric' => 'Kode autentikasi harus berupa angka.',
            ]);

            $google2fa = new Google2FA();
            $admin = Auth::guard('admin')->user();

            // Ambil secret key dari database
            $secretKey = $admin->google2fa_secret;

            // Verifikasi kode 2FA
            $isValid = $google2fa->verifyKey($secretKey, $request->input('one_time_password'));

            if ($isValid) {
                // Hapus throttle key jika verifikasi berhasil
                Cache::forget($throttleKey);

                // Tandai sesi sebagai 2FA diverifikasi
                session(['admin_2fa_verified' => true]);

                Alert::success('Verification Successful', 'Two-factor authentication verified successfully.');
                return redirect()->route('admin.dashboard');
            }

            // Increment jumlah percobaan jika kode tidak valid
            Cache::increment($throttleKey);
            Cache::put($throttleKey, Cache::get($throttleKey), now()->addMinutes($decayMinutes));

            // Jika kode 2FA tidak valid
            Alert::error('Verification Failed', 'Invalid authentication code. Please try again.');
            return redirect()->back();

        } catch (\Exception $e) {
            // Tangani error yang tidak terduga
            Alert::error('Error', 'An unexpected error occurred. Please try again later.');
            return redirect()->back();
        }
    }

    public function confirmTwoFactor(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();

            // Periksa apakah secret key tersedia
            if (!$admin->google2fa_secret) {
                Alert::error('Confirmation Failed', 'Please scan the QR code before confirming.');
                return redirect()->route('admin.enable-2fa');
            }

            // Cek apakah 2FA sudah aktif
            if ($admin->two_factor_enabled) {
                Alert::info('Already Enabled', 'Two-factor authentication is already enabled for your account.');
                return redirect()->route('admin.dashboard');
            }

            // Aktifkan 2FA
            $admin->two_factor_enabled = true;
            $admin->save();

            Alert::success('Enabled Successfully', 'Two-factor authentication has been successfully enabled.');
            return redirect()->route('admin.dashboard');

        } catch (\Exception $e) {
            // Tangani error yang tidak terduga
            Alert::error('Error', 'An unexpected error occurred. Please try again later.');
            return redirect()->back();
        }
    }

}
