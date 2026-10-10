<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | Operations Center Team Acosta 2028</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        :root {
            --auth-bg-dark: #0A192F;
            --auth-navy: #102A4E;
            --auth-primary: #075998;
            --auth-cyan: #51B8E5;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--auth-bg-dark);
            min-height: 100vh;
            color: #0E2038;
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 10% 20%, rgba(7, 89, 152, 0.45) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(81, 184, 229, 0.3) 0%, transparent 40%),
                        linear-gradient(135deg, #09182D 0%, #102A4E 50%, #08345C 100%);
            padding: 30px 20px;
            position: relative;
            overflow: hidden;
        }

        /* Ambient Glow Spheres */
        .auth-glow-1 {
            position: absolute;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(81, 184, 229, 0.18) 0%, rgba(0,0,0,0) 70%);
            top: -100px;
            left: -100px;
            pointer-events: none;
            filter: blur(40px);
        }

        .auth-glow-2 {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(7, 89, 152, 0.28) 0%, rgba(0,0,0,0) 70%);
            bottom: -120px;
            right: -120px;
            pointer-events: none;
            filter: blur(50px);
        }

        .auth-reset-card {
            width: 100%;
            max-width: 480px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
            padding: 40px 36px;
            position: relative;
            z-index: 10;
        }

        .reset-brand-top {
            text-align: center;
            margin-bottom: 24px;
        }

        .reset-brand-logo {
            height: 56px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 10px rgba(16, 42, 78, 0.15));
            margin-bottom: 14px;
        }

        .reset-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            color: var(--auth-primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        .reset-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--auth-navy);
            letter-spacing: -0.02em;
            margin: 0 0 6px;
        }

        .reset-subtitle {
            font-size: 13.5px;
            color: #5C728A;
            margin: 0;
            line-height: 1.4;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--auth-navy);
            margin-bottom: 6px;
            display: block;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #8CA0B4;
            font-size: 16px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-control-custom {
            width: 100%;
            height: 46px;
            padding: 10px 42px 10px 40px;
            font-size: 14px;
            font-family: inherit;
            color: #0E2038;
            background-color: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .form-control-custom:focus {
            outline: none;
            background-color: #FFFFFF;
            border-color: var(--auth-primary);
            box-shadow: 0 0 0 4px rgba(7, 89, 152, 0.12);
        }

        .form-control-custom:focus + .input-icon,
        .input-group-custom:focus-within .input-icon {
            color: var(--auth-primary);
        }

        .form-control-custom[readonly] {
            background-color: #F1F5F9;
            color: #64748B;
            cursor: not-allowed;
        }

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #8CA0B4;
            cursor: pointer;
            padding: 4px;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .toggle-password-btn:hover {
            color: var(--auth-primary);
        }

        .submit-reset-btn {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, var(--auth-primary) 0%, #0B6BB5 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(7, 89, 152, 0.35);
            transition: all 0.2s ease;
            margin-top: 22px;
            margin-bottom: 16px;
        }

        .submit-reset-btn:hover {
            background: linear-gradient(135deg, #064a7e 0%, #075998 100%);
            box-shadow: 0 6px 20px rgba(7, 89, 152, 0.45);
            transform: translateY(-1px);
        }

        .submit-reset-btn:active {
            transform: translateY(0);
        }

        .btn-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Password Helper Hints */
        .password-hints {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px 12px;
            margin-top: 8px;
            font-size: 11.5px;
            color: #64748B;
        }

        .hint-item {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 3px;
        }
        .hint-item:last-child {
            margin-bottom: 0;
        }
        .hint-item.valid {
            color: #16A34A;
        }
        .hint-item.invalid {
            color: #64748B;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-glow-1"></div>
        <div class="auth-glow-2"></div>

        <div class="auth-reset-card">
            <div class="reset-brand-top">
                <img src="{{ asset('images/logo.png') }}" alt="Operations Center Team Acosta 2028" class="reset-brand-logo">
                <div>
                    <span class="reset-badge">
                        <i class="bi bi-shield-lock-fill"></i> Account Security
                    </span>
                    <h1 class="reset-title">Set New Password</h1>
                    <p class="reset-subtitle">Create and confirm a new secure password for your account</p>
                </div>
            </div>

            @if(session('error'))
                <div style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 4px solid #EF4444; padding: 12px 14px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; color: #DC2626; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 4px solid #EF4444; padding: 12px 14px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; color: #DC2626; font-weight: 600;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>Please correct the following:</span>
                    </div>
                    <ul style="margin: 0; padding-left: 20px; font-weight: 500; font-size: 12.5px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" id="resetPasswordForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Account Email (Read-only verified) -->
                <div class="form-group">
                    <label for="email" class="form-label">Account Email Address</label>
                    <div class="input-group-custom">
                        <i class="bi bi-envelope-check-fill input-icon"></i>
                        <input type="email" id="email" name="email" class="form-control-custom @error('email') is-invalid @enderror" value="{{ old('email', $email) }}" required readonly>
                    </div>
                </div>

                <!-- New Password -->
                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <div class="input-group-custom">
                        <i class="bi bi-key-fill input-icon"></i>
                        <input type="password" id="password" name="password" class="form-control-custom @error('password') is-invalid @enderror" required placeholder="Enter new password (min. 8 characters)" autofocus>
                        <button type="button" class="toggle-password-btn" onclick="togglePassVisibility('password', this)" tabindex="-1" title="Toggle visibility">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div style="color: #DC2626; font-size: 12px; margin-top: 4px; font-weight: 600;">{{ $message }}</div>
                    @enderror

                    <!-- Password Live Rules Helper -->
                    <div class="password-hints">
                        <div class="hint-item" id="hint-length">
                            <i class="bi bi-circle"></i> Minimum 8 characters in length
                        </div>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <div class="input-group-custom">
                        <i class="bi bi-shield-check input-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control-custom" required placeholder="Re-type new password">
                        <button type="button" class="toggle-password-btn" onclick="togglePassVisibility('password_confirmation', this)" tabindex="-1" title="Toggle visibility">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="password-hints" style="margin-top: 8px;">
                        <div class="hint-item" id="hint-match">
                            <i class="bi bi-circle"></i> Passwords must match exactly
                        </div>
                    </div>
                </div>

                <button type="submit" id="resetSubmitBtn" class="submit-reset-btn">
                    <span id="resetBtnSpinner" class="btn-spinner" style="display: none;"></span>
                    <i id="resetBtnIcon" class="bi bi-check2-circle"></i>
                    <span id="resetBtnText">Update & Activate Password</span>
                </button>

                <div style="text-align: center;">
                    <a href="{{ route('login') }}" style="font-size: 13px; font-weight: 700; color: var(--auth-primary); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: color 0.2s ease;">
                        <i class="bi bi-arrow-left"></i> Back to Sign In
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Live validation hints
        const passInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const hintLength = document.getElementById('hint-length');
        const hintMatch = document.getElementById('hint-match');

        function updateHints() {
            const val = passInput.value;
            const confirmVal = confirmInput.value;

            // Length check
            if (val.length >= 8) {
                hintLength.className = 'hint-item valid';
                hintLength.querySelector('i').className = 'bi bi-check-circle-fill';
            } else {
                hintLength.className = 'hint-item invalid';
                hintLength.querySelector('i').className = 'bi bi-circle';
            }

            // Match check
            if (val.length > 0 && confirmVal.length > 0 && val === confirmVal) {
                hintMatch.className = 'hint-item valid';
                hintMatch.querySelector('i').className = 'bi bi-check-circle-fill';
            } else {
                hintMatch.className = 'hint-item invalid';
                hintMatch.querySelector('i').className = 'bi bi-circle';
            }
        }

        passInput.addEventListener('input', updateHints);
        confirmInput.addEventListener('input', updateHints);

        // Submit form animation
        document.getElementById('resetPasswordForm').addEventListener('submit', function (e) {
            const btn = document.getElementById('resetSubmitBtn');
            const spinner = document.getElementById('resetBtnSpinner');
            const icon = document.getElementById('resetBtnIcon');
            const text = document.getElementById('resetBtnText');

            if (btn) {
                btn.disabled = true;
                btn.style.opacity = '0.85';
                btn.style.cursor = 'wait';
            }
            if (spinner) spinner.style.display = 'inline-block';
            if (icon) icon.style.display = 'none';
            if (text) text.textContent = 'Updating Password...';
        });
    </script>
</body>
</html>
