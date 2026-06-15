<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Inertia\Inertia;
use Exception;

class AuthController extends Controller
{
    
    public function loginForm()
    {
        // ADMIN LOGIN
        if (request()->is('admin/*')) {
            return Inertia::render('Auth/Login', ['isAdmin' => true]);
        }

        // USER LOGIN
        return Inertia::render('Auth/Login');
    }

    
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

       
        if ($request->is('admin/*')) {
            if (Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
                'is_admin' => 1,
            ])) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }

            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ]);
        }

        // ===== USER LOGIN =====
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // =======================
    // REGISTER
    // =======================
    public function registerForm()
    {
        // ADMIN REGISTER
        if (request()->is('admin/*')) {
            return Inertia::render('Auth/Register', ['isAdmin' => true]);
        }

        // USER REGISTER
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // ===== ADMIN REGISTER =====
        if ($request->is('admin/*')) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_admin' => 1, // Set as admin
            ]);

            Auth::login($user);
            return redirect()->route('admin.dashboard')->with('success', 'Akun admin berhasil dibuat!');
        }

        // ===== USER REGISTER =====
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        return redirect()->route('home')->with('success', 'Akun berhasil dibuat!');
    }

    // =======================
    // LOGOUT
    // =======================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // =======================
    // GOOGLE LOGIN
    // =======================
    
    /**
    
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     
     */
    public function handleGoogleCallback()
    {
        try {
            // Get user info from Google
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists by google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                
                Auth::login($user);
                session()->regenerate();
                return redirect()->route('home')->with('success', 'Berhasil login dengan Google!');
            }

            
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // Link Google account to existing user
                $existingUser->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);

                Auth::login($existingUser);
                session()->regenerate();
                return redirect()->route('home')->with('success', 'Akun Google berhasil dihubungkan!');
            }

            // Create new user
            $newUser = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'is_admin' => 0,
            ]);

            Auth::login($newUser);
            session()->regenerate();
            return redirect()->route('home')->with('success', 'Akun berhasil dibuat dengan Google!');

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            $data = json_decode($responseBody, true);
            
            Log::error('Google OAuth ClientException:', [
                'status' => $response->getStatusCode(),
                'body' => $responseBody
            ]);
            
            $errorMsg = $data['error_description'] ?? $data['error'] ?? 'Gagal terhubung ke Google';
            
            return redirect()->route('login')->withErrors([
                'google' => $errorMsg
            ]);
            
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Google OAuth Error: ' . $e->getMessage());
            Log::error('Exception Type: ' . get_class($e));
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('login')->withErrors([
                'google' => 'Kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
