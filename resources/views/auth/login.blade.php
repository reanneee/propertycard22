<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSU Property Stock Card - Authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --psu-primary: #1e3a8a;
            --psu-secondary: #3b82f6;
            --psu-accent: #fbbf24;
            --psu-light: #f8fafc;
            --psu-dark: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--psu-primary) 0%, var(--psu-secondary) 50%, #6366f1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        .bg-decoration {
            position: absolute;
            opacity: 0.1;
            pointer-events: none;
        }

        .bg-decoration-1 {
            top: 10%;
            left: 10%;
            width: 200px;
            height: 200px;
            background: linear-gradient(45deg, var(--psu-accent), transparent);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .bg-decoration-2 {
            bottom: 15%;
            right: 15%;
            width: 150px;
            height: 150px;
            background: linear-gradient(45deg, white, transparent);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .bg-decoration-3 {
            top: 50%;
            left: 5%;
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, var(--psu-accent), transparent);
            border-radius: 20px;
            animation: float 10s ease-in-out infinite;
            transform: rotate(45deg);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Main Container */
        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 480px;
            margin: 2rem;
            overflow: hidden;
            position: relative;
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header Section */
        .auth-header {
            background: linear-gradient(135deg, var(--psu-primary) 0%, var(--psu-secondary) 100%);
            color: white;
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            position: relative;
        }

        .auth-header::before {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(135deg, var(--psu-primary) 0%, var(--psu-secondary) 100%);
            border-radius: 0 0 24px 24px;
        }

        .university-logo {
            width: 80px;
            height: 80px;
            background: var(--psu-accent);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 32px rgba(251, 191, 36, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .university-logo i {
            font-size: 2.5rem;
            color: var(--psu-primary);
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .auth-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 400;
        }

        /* Form Section */
        .auth-form {
            padding: 2.5rem 2rem;
        }

        .form-tabs {
            display: flex;
            margin-bottom: 2rem;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 4px;
        }

        .tab-btn {
            flex: 1;
            padding: 12px 20px;
            background: transparent;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            color: #64748b;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .tab-btn.active {
            background: white;
            color: var(--psu-primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--psu-dark);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--psu-secondary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-1px);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
        }

        .error-message i {
            margin-right: 0.5rem;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }

        .submit-btn {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--psu-primary) 0%, var(--psu-secondary) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(30, 58, 138, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .auth-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #64748b;
        }

        .auth-link a {
            color: var(--psu-secondary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-link a:hover {
            color: var(--psu-primary);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .auth-container {
                margin: 1rem;
                border-radius: 20px;
            }

            .auth-header {
                padding: 2rem 1.5rem 1.5rem;
            }

            .auth-form {
                padding: 2rem 1.5rem;
            }

            .auth-title {
                font-size: 1.5rem;
            }

            .university-logo {
                width: 70px;
                height: 70px;
            }

            .university-logo i {
                font-size: 2rem;
            }
        }

        /* Loading Animation */
        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Background Decorations -->
    <div class="bg-decoration bg-decoration-1"></div>
    <div class="bg-decoration bg-decoration-2"></div>
    <div class="bg-decoration bg-decoration-3"></div>

    <div class="auth-container">
        <!-- Header -->
        <div class="auth-header">
            <div class="university-logo">
                <i class="fas fa-university"></i>
            </div>
            <h1 class="auth-title">PSU Property Stock Card</h1>
            <p class="auth-subtitle">Pangasinan State University</p>
        </div>

        <!-- Form Section -->
        <div class="auth-form">
            <!-- Tab Navigation -->
            <div class="form-tabs">
                <button class="tab-btn active" id="loginTab">Login</button>
                <button class="tab-btn" id="registerTab">Register</button>
            </div>

            <!-- Login Form -->
            <div class="form-section active" id="loginForm">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Error Messages -->
                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fas fa-times-circle"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="loginEmail" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="loginEmail" 
                            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                            value="{{ old('email') }}" 
                            placeholder="Enter your email address"
                            required
                        >
                        @error('email')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="loginPassword" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="loginPassword" 
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                            placeholder="Enter your password"
                            required
                        >
                        @error('password')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn" id="loginBtn">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>

                    <!-- Register Link -->
                    <div class="auth-link">
                        <p>Don't have an account? <a href="#" id="showRegister">Create one here</a></p>
                    </div>
                </form>
            </div>

            <!-- Register Form -->
            <div class="form-section" id="registerForm">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name Field -->
                    <div class="form-group">
                        <label for="registerName" class="form-label">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="registerName" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name') }}" 
                            placeholder="Enter your full name"
                            required
                        >
                        @error('name')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="registerEmail" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="registerEmail" 
                            class="form-control @error('email') is-invalid @enderror" 
                            value="{{ old('email') }}" 
                            placeholder="Enter your email address"
                            required
                        >
                        @error('email')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="registerPassword" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="registerPassword" 
                            class="form-control @error('password') is-invalid @enderror" 
                            placeholder="Create a strong password"
                            required
                        >
                        @error('password')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <label for="registerPasswordConfirm" class="form-label">
                            <i class="fas fa-lock"></i> Confirm Password
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="registerPasswordConfirm" 
                            class="form-control @error('password_confirmation') is-invalid @enderror" 
                            placeholder="Confirm your password"
                            required
                        >
                        @error('password_confirmation')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn" id="registerBtn">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>

                    <!-- Login Link -->
                    <div class="auth-link">
                        <p>Already have an account? <a href="#" id="showLogin">Sign in here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginTab = document.getElementById('loginTab');
            const registerTab = document.getElementById('registerTab');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const showRegisterLink = document.getElementById('showRegister');
            const showLoginLink = document.getElementById('showLogin');

            // Tab switching functionality
            function switchToLogin() {
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
                loginForm.classList.add('active');
                registerForm.classList.remove('active');
            }

            function switchToRegister() {
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
                registerForm.classList.add('active');
                loginForm.classList.remove('active');
            }

            // Event listeners
            loginTab.addEventListener('click', switchToLogin);
            registerTab.addEventListener('click', switchToRegister);
            showRegisterLink.addEventListener('click', function(e) {
                e.preventDefault();
                switchToRegister();
            });
            showLoginLink.addEventListener('click', function(e) {
                e.preventDefault();
                switchToLogin();
            });

            // Form submission with loading states
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('.submit-btn');
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                });
            });

            // Enhanced form validation
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '' && this.hasAttribute('required')) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim() !== '') {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Password confirmation validation
            const passwordConfirm = document.getElementById('registerPasswordConfirm');
            const password = document.getElementById('registerPassword');
            
            if (passwordConfirm && password) {
                passwordConfirm.addEventListener('input', function() {
                    if (this.value !== password.value) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            }
        });
    </script>
</body>
</html>