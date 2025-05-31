<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Enhanced validation with comprehensive rules
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/' // Only letters and spaces
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed'
            ],
            'password_confirmation' => [
                'required',
                'string'
            ]
        ], [
            // Custom error messages
            'name.required' => 'Full name is required.',
            'name.min' => 'Name must be at least 2 characters long.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'name.regex' => 'Name can only contain letters and spaces.',
            
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address cannot exceed 255 characters.',
            'email.unique' => 'This email address is already registered.',
            
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            
            'password_confirmation.required' => 'Please confirm your password.',
        ]);

        // Add custom password validation messages
        $validator->after(function ($validator) use ($request) {
            $password = $request->password;
            
            if ($password) {
                $errors = [];
                
                if (!preg_match('/[A-Z]/', $password)) {
                    $errors[] = 'Password must contain at least one uppercase letter.';
                }
                
                if (!preg_match('/[a-z]/', $password)) {
                    $errors[] = 'Password must contain at least one lowercase letter.';
                }
                
                if (!preg_match('/\d/', $password)) {
                    $errors[] = 'Password must contain at least one number.';
                }
                
                if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
                    $errors[] = 'Password must contain at least one special character.';
                }
                
                if (!empty($errors)) {
                    $validator->errors()->add('password', implode(' ', $errors));
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Check if user already exists (double-check)
            $existingUser = DB::table('users')->where('email', $request->email)->first();
            if ($existingUser) {
                return redirect()->back()
                        ->withErrors(['email' => 'This email address is already registered.'])
                        ->withInput($request->except('password', 'password_confirmation'));
            }

            // Create new user
            $userId = DB::table('users')->insertGetId([
                'name' => trim($request->name),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),
                'email_verified_at' => null, // Set to null initially, verify later
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($userId) {
                // Optional: Send verification email here
                // Mail::to($request->email)->send(new VerifyEmail($user));
                
                return redirect()->route('login', ['registered' => 'true'])
                        ->with('success', 'Registration successful! Please log in with your credentials.');
            } else {
                throw new \Exception('Failed to create user account.');
            }
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database-specific errors
            if ($e->getCode() == 23000) { // Duplicate entry
                return redirect()->back()
                        ->withErrors(['email' => 'This email address is already registered.'])
                        ->withInput($request->except('password', 'password_confirmation'));
            }
            
            return redirect()->back()
                    ->withErrors(['db_error' => 'Database error occurred. Please try again.'])
                    ->withInput($request->except('password', 'password_confirmation'));
                    
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Registration error: ' . $e->getMessage());
            
            return redirect()->back()
                    ->withErrors(['general_error' => 'Registration failed. Please try again later.'])
                    ->withInput($request->except('password', 'password_confirmation'));
        }
    }
}

// ===================================
// Additional Helper Methods (Optional)

trait AuthValidationHelpers 
{
    /**
     * Check password strength
     */
    protected function checkPasswordStrength($password)
    {
        $score = 0;
        $feedback = [];
        
        // Length check
        if (strlen($password) >= 8) {
            $score++;
        } else {
            $feedback[] = 'Use at least 8 characters';
        }
        
        // Uppercase check
        if (preg_match('/[A-Z]/', $password)) {
            $score++;
        } else {
            $feedback[] = 'Include uppercase letters';
        }
        
        // Lowercase check
        if (preg_match('/[a-z]/', $password)) {
            $score++;
        } else {
            $feedback[] = 'Include lowercase letters';
        }
        
        // Number check
        if (preg_match('/\d/', $password)) {
            $score++;
        } else {
            $feedback[] = 'Include numbers';
        }
        
        // Special character check
        if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $score++;
        } else {
            $feedback[] = 'Include special characters';
        }
        
        $strength = 'weak';
        if ($score >= 4) $strength = 'strong';
        elseif ($score >= 3) $strength = 'good';
        elseif ($score >= 2) $strength = 'fair';
        
        return [
            'score' => $score,
            'strength' => $strength,
            'feedback' => $feedback
        ];
    }

    /**
     * Sanitize user input
     */
    protected function sanitizeInput($input)
    {
        return trim(htmlspecialchars(strip_tags($input)));
    }

    /**
     * Check if email domain is valid
     */
    protected function isValidEmailDomain($email)
    {
        $domain = substr(strrchr($email, "@"), 1);
        return checkdnsrr($domain, "MX");
    }
}