<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Supplier</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #333;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-header h2 {
            color: #666;
            font-size: 20px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 16px;
            color: #333;
            background: white;
            transition: border-color 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #F59E0B;
        }

        .form-input::placeholder {
            color: #999;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 4px;
        }

        .password-toggle:hover {
            color: #F59E0B;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 8px;
        }

        .checkbox-container label {
            color: #666;
            font-size: 14px;
            cursor: pointer;
        }

        .login-button {
    width: 100%;
    padding: 14px;
    background-color: #007BFF; /* Warna biru */
    border: none;
    border-radius: 6px;
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.login-button:hover {
    background-color: #0056b3; /* Biru lebih gelap saat hover */
}

.login-button:active {
    background-color: #004085; /* Biru lebih pekat saat ditekan */
}


        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 8px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 20px;
            }
            
            .login-header h2 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>SmartKantin</h1>
            <h2>Login Supllier</h2>
        </div>

        <form method="POST" action="{{ route('supplier.login.submit') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email address *</label>
                <input 
                    type="email" 
                    id="email"
                    name="email" 
                    class="form-input" 
                    required 
                    autofocus
                    value="{{ old('email') }}"
                >
                @error('email')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password *</label>
                <div class="password-container">
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        class="form-input" 
                        required
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path id="eye-path" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle id="eye-circle" cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            {{-- <div class="checkbox-container">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div> --}}

            <button type="submit" class="login-button">Sign in</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyePath = document.getElementById('eye-path');
            const eyeCircle = document.getElementById('eye-circle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyePath.setAttribute('d', 'M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24');
                eyeCircle.style.display = 'none';
            } else {
                passwordInput.type = 'password';
                eyePath.setAttribute('d', 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z');
                eyeCircle.style.display = 'block';
            }
        }
    </script>
</body>
</html>