<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Enhanced validation with custom messages
        $request->validate([
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255'
            ],
            'password' => [
                'required',
                'string',
                'min:1'
            ],
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address is too long.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password cannot be empty.',
        ]);

        // Rate limiting (optional - requires RateLimiter)
        $email = $request->email;
        $key = 'login-attempts:' . $email;
        
        try {
            $user = DB::table('users')->where('email', $email)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            if (!Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'password' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Successful login
            Auth::loginUsingId($user->id);
            
            // Clear any previous login attempts
            // Cache::forget($key);
            
            // Redirect with success message
            return redirect()->route('dashboard')->with('success', 'Welcome back!');
            
        } catch (ValidationException $e) {
            // Increment login attempts (optional)
            // $attempts = Cache::get($key, 0) + 1;
            // Cache::put($key, $attempts, now()->addMinutes(15));
            
            throw $e;
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Login failed. Please try again later.',
            ])->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
