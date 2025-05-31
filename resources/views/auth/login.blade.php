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
            --psu-success: #10b981;
            --psu-danger: #ef4444;
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
            padding: 1rem;
        }

        /* Animated Background Elements */
        .bg-decoration {
            position: absolute;
            opacity: 0.1;
            pointer-events: none;
            z-index: 0;
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

        /* Main Container - Better Responsive */
        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 450px;
            max-height: 95vh;
            overflow-y: auto;
            position: relative;
            z-index: 1;
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

        /* Header Section - Compact */
        .auth-header {
            background: linear-gradient(135deg, var(--psu-primary) 0%, var(--psu-secondary) 100%);
            color: white;
            padding: 1.5rem 1.5rem 1rem;
            text-align: center;
            position: relative;
        }

        .auth-header::before {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 15px;
            background: linear-gradient(135deg, var(--psu-primary) 0%, var(--psu-secondary) 100%);
            border-radius: 0 0 24px 24px;
        }

        .university-logo {
            width: 60px;
            height: 60px;
            background: var(--psu-accent);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 32px rgba(251, 191, 36, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .university-logo i {
            font-size: 1.8rem;
            color: var(--psu-primary);
        }

        .auth-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .auth-subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
            font-weight: 400;
        }

        /* Form Section - Compact */
        .auth-form {
            padding: 1.5rem;
        }

        .form-tabs {
            display: flex;
            margin-bottom: 1.5rem;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 3px;
        }

        .tab-btn {
            flex: 1;
            padding: 10px 16px;
            background: transparent;
            border: none;
            border-radius: 9px;
            font-weight: 600;
            font-size: 0.9rem;
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
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 600;
            color: var(--psu-dark);
            font-size: 0.85rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
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
            border-color: var(--psu-danger);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-control.is-valid {
            border-color: var(--psu-success);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .error-message {
            color: var(--psu-danger);
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            animation: shake 0.5s ease-out;
        }

        .success-message {
            color: var(--psu-success);
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
        }

        .error-message i,
        .success-message i {
            margin-right: 0.25rem;
            font-size: 0.7rem;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            border-left: 3px solid #ef4444;
        }

        .alert-success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #16a34a;
            border-left: 3px solid #10b981;
        }

        .submit-btn {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, var(--psu-primary) 0%, var(--psu-secondary) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(30, 58, 138, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
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
            margin-top: 1rem;
            color: #64748b;
            font-size: 0.85rem;
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

        /* Password Strength Indicator */
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.75rem;
        }

        .strength-bar {
            width: 100%;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            margin: 0.25rem 0;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: #ef4444; width: 25%; }
        .strength-fair { background: #f59e0b; width: 50%; }
        .strength-good { background: #eab308; width: 75%; }
        .strength-strong { background: #10b981; width: 100%; }

        /* Loading Animation */
        .loading {
            position: relative;
        }

        .loading .loading-text {
            opacity: 0;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Mobile Optimizations */
        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
            }

            .auth-container {
                max-width: 100%;
                border-radius: 20px;
                max-height: 100vh;
            }

            .auth-header {
                padding: 1.25rem 1.25rem 0.75rem;
            }

            .auth-form {
                padding: 1.25rem;
            }

            .auth-title {
                font-size: 1.25rem;
            }

            .university-logo {
                width: 50px;
                height: 50px;
            }

            .university-logo i {
                font-size: 1.5rem;
            }

            .form-group {
                margin-bottom: 0.75rem;
            }
        }

        @media (max-height: 600px) {
            .auth-header {
                padding: 1rem 1.25rem 0.5rem;
            }

            .university-logo {
                width: 45px;
                height: 45px;
                margin-bottom: 0.5rem;
            }

            .university-logo i {
                font-size: 1.25rem;
            }

            .auth-title {
                font-size: 1.1rem;
                margin-bottom: 0.1rem;
            }

            .auth-subtitle {
                font-size: 0.75rem;
            }

            .form-group {
                margin-bottom: 0.5rem;
            }
        }

        /* Custom Scrollbar */
        .auth-container::-webkit-scrollbar {
            width: 4px;
        }

        .auth-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .auth-container::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.3);
            border-radius: 2px;
        }

        .auth-container::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.5);
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
                <form id="loginFormElement" novalidate>
                    <!-- Success Messages -->
                    <div class="alert alert-success d-none" id="loginSuccess">
                        <i class="fas fa-check-circle"></i>
                        <span id="loginSuccessMessage"></span>
                    </div>

                    <!-- Error Messages -->
                    <div class="alert alert-danger d-none" id="loginError">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="loginErrorMessage"></span>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="loginEmail" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="loginEmail" 
                            class="form-control" 
                            placeholder="Enter your email address"
                            required
                        >
                        <div class="error-message d-none" id="loginEmailError">
                            <i class="fas fa-exclamation-circle"></i>
                            <span></span>
                        </div>
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
                            class="form-control" 
                            placeholder="Enter your password"
                            required
                        >
                        <div class="error-message d-none" id="loginPasswordError">
                            <i class="fas fa-exclamation-circle"></i>
                            <span></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn" id="loginBtn">
                        <span class="loading-text">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </span>
                    </button>

                    <!-- Register Link -->
                    <div class="auth-link">
                        <p>Don't have an account? <a href="#" id="showRegister">Create one here</a></p>
                    </div>
                </form>
            </div>

            <!-- Register Form -->
            <div class="form-section" id="registerForm">
                <form id="registerFormElement" novalidate>
                    <!-- Success Messages -->
                    <div class="alert alert-success d-none" id="registerSuccess">
                        <i class="fas fa-check-circle"></i>
                        <span id="registerSuccessMessage"></span>
                    </div>

                    <!-- Error Messages -->
                    <div class="alert alert-danger d-none" id="registerError">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="registerErrorMessage"></span>
                    </div>

                    <!-- Name Field -->
                    <div class="form-group">
                        <label for="registerName" class="form-label">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="registerName" 
                            class="form-control" 
                            placeholder="Enter your full name"
                            required
                        >
                        <div class="error-message d-none" id="registerNameError">
                            <i class="fas fa-exclamation-circle"></i>
                            <span></span>
                        </div>
                        <div class="success-message d-none" id="registerNameSuccess">
                            <i class="fas fa-check-circle"></i>
                            <span>Valid name</span>
                        </div>
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
                            class="form-control" 
                            placeholder="Enter your email address"
                            required
                        >
                        <div class="error-message d-none" id="registerEmailError">
                            <i class="fas fa-exclamation-circle"></i>
                            <span></span>
                        </div>
                        <div class="success-message d-none" id="registerEmailSuccess">
                            <i class="fas fa-check-circle"></i>
                            <span>Valid email</span>
                        </div>
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
                            class="form-control" 
                            placeholder="Create a strong password"
                            required
                        >
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <div class="strength-text" id="strengthText">Password strength</div>
                        </div>
                        <div class="error-message d-none" id="registerPasswordError">
                            <i class="fas fa-exclamation-circle"></i>
                            <span></span>
                        </div>
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
                            class="form-control" 
                            placeholder="Confirm your password"
                            required
                        >
                        <div class="error-message d-none" id="registerPasswordConfirmError">
                            <i class="fas fa-exclamation-circle"></i>
                            <span></span>
                        </div>
                        <div class="success-message d-none" id="registerPasswordConfirmSuccess">
                            <i class="fas fa-check-circle"></i>
                            <span>Passwords match</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn" id="registerBtn">
                        <span class="loading-text">
                            <i class="fas fa-user-plus"></i> Create Account
                        </span>
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
            // Element references
            const loginTab = document.getElementById('loginTab');
            const registerTab = document.getElementById('registerTab');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const showRegisterLink = document.getElementById('showRegister');
            const showLoginLink = document.getElementById('showLogin');
            
            // Form elements
            const loginFormElement = document.getElementById('loginFormElement');
            const registerFormElement = document.getElementById('registerFormElement');

            // Tab switching functionality
            function switchToLogin() {
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
                loginForm.classList.add('active');
                registerForm.classList.remove('active');
                clearMessages();
            }

            function switchToRegister() {
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
                registerForm.classList.add('active');
                loginForm.classList.remove('active');
                clearMessages();
            }

            // Clear all messages
            function clearMessages() {
                document.querySelectorAll('.alert').forEach(alert => alert.classList.add('d-none'));
                document.querySelectorAll('.error-message, .success-message').forEach(msg => msg.classList.add('d-none'));
                document.querySelectorAll('.form-control').forEach(input => {
                    input.classList.remove('is-invalid', 'is-valid');
                });
            }

            // Event listeners for tabs
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

            // Validation functions
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            function validateName(name) {
                return name.trim().length >= 2 && /^[a-zA-Z\s]+$/.test(name.trim());
            }

            function validatePassword(password) {
                return {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
                };
            }

            function getPasswordStrength(password) {
                const checks = validatePassword(password);
                const score = Object.values(checks).filter(Boolean).length;
                
                if (score < 2) return { strength: 'weak', text: 'Weak password' };
                if (score < 3) return { strength: 'fair', text: 'Fair password' };
                if (score < 4) return { strength: 'good', text: 'Good password' };
                return { strength: 'strong', text: 'Strong password' };
            }

            function showError(inputId, message) {
                const input = document.getElementById(inputId);
                const errorElement = document.getElementById(inputId + 'Error');
                const successElement = document.getElementById(inputId + 'Success');
                
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                
                if (errorElement) {
                    errorElement.querySelector('span').textContent = message;
                    errorElement.classList.remove('d-none');
                }
                
                if (successElement) {
                    successElement.classList.add('d-none');
                }
            }

            function showSuccess(inputId, message = '') {
                const input = document.getElementById(inputId);
                const errorElement = document.getElementById(inputId + 'Error');
                const successElement = document.getElementById(inputId + 'Success');
                
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                
                if (errorElement) {
                    errorElement.classList.add('d-none');
                }
                
                if (successElement && message) {
                    successElement.querySelector('span').textContent = message;
                    successElement.classList.remove('d-none');
                }
            }

            function clearValidation(inputId) {
                const input = document.getElementById(inputId);
                const errorElement = document.getElementById(inputId + 'Error');
                const successElement = document.getElementById(inputId + 'Success');
                
                input.classList.remove('is-invalid', 'is-valid');
                
                if (errorElement) errorElement.classList.add('d-none');
                if (successElement) successElement.classList.add('d-none');
            }

            // Real-time validation for login form
            document.getElementById('loginEmail').addEventListener('input', function() {
                const email = this.value.trim();
                if (email === '') {
                    clearValidation('loginEmail');
                } else if (!validateEmail(email)) {
                    showError('loginEmail', 'Please enter a valid email address');
                } else {
                    showSuccess('loginEmail');
                }
            });

            document.getElementById('loginPassword').addEventListener('input', function() {
                const password = this.value;
                if (password === '') {
                    clearValidation('loginPassword');
                } else if (password.length < 1) {
                    showError('loginPassword', 'Password is required');
                } else {
                    showSuccess('loginPassword');
                }
            });

            // Real-time validation for register form
            document.getElementById('registerName').addEventListener('input', function() {
                const name = this.value.trim();
                if (name === '') {
                    clearValidation('registerName');
                } else if (!validateName(name)) {
                    showError('registerName', 'Name must be at least 2 characters and contain only letters');
                } else {
                    showSuccess('registerName', 'Valid name');
                }
            });

            document.getElementById('registerEmail').addEventListener('input', function() {
                const email = this.value.trim();
                if (email === '') {
                    clearValidation('registerEmail');
                } else if (!validateEmail(email)) {
                    showError('registerEmail', 'Please enter a valid email address');
                } else {
                    showSuccess('registerEmail', 'Valid email');
                }
            });

            // Password strength indicator
            document.getElementById('registerPassword').addEventListener('input', function() {
                const password = this.value;
                const strengthFill = document.getElementById('strengthFill');
                const strengthText = document.getElementById('strengthText');
                
                if (password === '') {
                    clearValidation('registerPassword');
                    strengthFill.className = 'strength-fill';
                    strengthText.textContent = 'Password strength';
                    return;
                }
                
                const checks = validatePassword(password);
                const strength = getPasswordStrength(password);
                
                // Update strength bar
                strengthFill.className = `strength-fill strength-${strength.strength}`;
                strengthText.textContent = strength.text;
                
                // Validate password requirements
                if (password.length < 8) {
                    showError('registerPassword', 'Password must be at least 8 characters long');
                } else if (!checks.uppercase || !checks.lowercase || !checks.number) {
                    showError('registerPassword', 'Password must contain uppercase, lowercase, and numbers');
                } else {
                    showSuccess('registerPassword');
                }
                
                // Re-validate password confirmation if it has value
                const confirmPassword = document.getElementById('registerPasswordConfirm').value;
                if (confirmPassword) {
                    validatePasswordConfirm();
                }
            });

            // Password confirmation validation
            function validatePasswordConfirm() {
                const password = document.getElementById('registerPassword').value;
                const confirmPassword = document.getElementById('registerPasswordConfirm').value;
                
                if (confirmPassword === '') {
                    clearValidation('registerPasswordConfirm');
                } else if (password !== confirmPassword) {
                    showError('registerPasswordConfirm', 'Passwords do not match');
                } else {
                    showSuccess('registerPasswordConfirm', 'Passwords match');
                }
            }

            document.getElementById('registerPasswordConfirm').addEventListener('input', validatePasswordConfirm);

            // Form submission handlers
            loginFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('loginEmail').value.trim();
                const password = document.getElementById('loginPassword').value;
                
                // Clear previous messages
                document.getElementById('loginError').classList.add('d-none');
                document.getElementById('loginSuccess').classList.add('d-none');
                
                // Validate fields
                let isValid = true;
                
                if (!email) {
                    showError('loginEmail', 'Email is required');
                    isValid = false;
                } else if (!validateEmail(email)) {
                    showError('loginEmail', 'Please enter a valid email address');
                    isValid = false;
                }
                
                if (!password) {
                    showError('loginPassword', 'Password is required');
                    isValid = false;
                }
                
                if (isValid) {
                    // Show loading state
                    const submitBtn = document.getElementById('loginBtn');
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    
                    // Simulate API call (replace with actual login logic)
                    setTimeout(() => {
                        // Reset loading state
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                        
                        // Simulate login response (replace with actual logic)
                        const loginSuccess = Math.random() > 0.3; // 70% success rate for demo
                        
                        if (loginSuccess) {
                            document.getElementById('loginSuccessMessage').textContent = 'Login successful! Redirecting...';
                            document.getElementById('loginSuccess').classList.remove('d-none');
                            
                            // Redirect after success (replace with actual redirect)
                            setTimeout(() => {
                                alert('Login successful! This would redirect to dashboard.');
                            }, 1500);
                        } else {
                            document.getElementById('loginErrorMessage').textContent = 'Invalid email or password. Please try again.';
                            document.getElementById('loginError').classList.remove('d-none');
                        }
                    }, 2000);
                }
            });

            registerFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const name = document.getElementById('registerName').value.trim();
                const email = document.getElementById('registerEmail').value.trim();
                const password = document.getElementById('registerPassword').value;
                const confirmPassword = document.getElementById('registerPasswordConfirm').value;
                
                // Clear previous messages
                document.getElementById('registerError').classList.add('d-none');
                document.getElementById('registerSuccess').classList.add('d-none');
                
                // Validate all fields
                let isValid = true;
                
                if (!name) {
                    showError('registerName', 'Full name is required');
                    isValid = false;
                } else if (!validateName(name)) {
                    showError('registerName', 'Name must be at least 2 characters and contain only letters');
                    isValid = false;
                }
                
                if (!email) {
                    showError('registerEmail', 'Email is required');
                    isValid = false;
                } else if (!validateEmail(email)) {
                    showError('registerEmail', 'Please enter a valid email address');
                    isValid = false;
                }
                
                if (!password) {
                    showError('registerPassword', 'Password is required');
                    isValid = false;
                } else if (password.length < 8) {
                    showError('registerPassword', 'Password must be at least 8 characters long');
                    isValid = false;
                } else {
                    const checks = validatePassword(password);
                    if (!checks.uppercase || !checks.lowercase || !checks.number) {
                        showError('registerPassword', 'Password must contain uppercase, lowercase, and numbers');
                        isValid = false;
                    }
                }
                
                if (!confirmPassword) {
                    showError('registerPasswordConfirm', 'Please confirm your password');
                    isValid = false;
                } else if (password !== confirmPassword) {
                    showError('registerPasswordConfirm', 'Passwords do not match');
                    isValid = false;
                }
                
                if (isValid) {
                    // Show loading state
                    const submitBtn = document.getElementById('registerBtn');
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    
                    // Simulate API call (replace with actual registration logic)
                    setTimeout(() => {
                        // Reset loading state
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                        
                        // Simulate registration response (replace with actual logic)
                        const registrationSuccess = Math.random() > 0.2; // 80% success rate for demo
                        
                        if (registrationSuccess) {
                            document.getElementById('registerSuccessMessage').textContent = 'Registration successful! You can now log in.';
                            document.getElementById('registerSuccess').classList.remove('d-none');
                            
                            // Clear form and switch to login after success
                            setTimeout(() => {
                                registerFormElement.reset();
                                clearMessages();
                                document.getElementById('strengthFill').className = 'strength-fill';
                                document.getElementById('strengthText').textContent = 'Password strength';
                                switchToLogin();
                            }, 2000);
                        } else {
                            const errorMessages = [
                                'Email address is already registered.',
                                'Registration failed. Please try again.',
                                'Server error. Please try again later.'
                            ];
                            const randomError = errorMessages[Math.floor(Math.random() * errorMessages.length)];
                            document.getElementById('registerErrorMessage').textContent = randomError;
                            document.getElementById('registerError').classList.remove('d-none');
                        }
                    }, 2500);
                }
            });

            // Enhanced input focus effects
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });

            // Keyboard navigation for tabs
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' && e.altKey) {
                    e.preventDefault();
                    if (loginTab.classList.contains('active')) {
                        switchToRegister();
                    } else {
                        switchToLogin();
                    }
                }
            });

            // Auto-resize container based on content
            function adjustContainerHeight() {
                const container = document.querySelector('.auth-container');
                const maxHeight = window.innerHeight * 0.95;
                container.style.maxHeight = maxHeight + 'px';
            }

            window.addEventListener('resize', adjustContainerHeight);
            adjustContainerHeight();

            // Show success message if coming from registration
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('registered') === 'true') {
                document.getElementById('loginSuccessMessage').textContent = 'Registration successful! Please log in with your credentials.';
                document.getElementById('loginSuccess').classList.remove('d-none');
            }
        });
    </script>
</body>
</html>