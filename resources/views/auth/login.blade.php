<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Operations Center Team Acosta 2028</title>
    
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
            --auth-card: #FFFFFF;
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

        .auth-layout-grid {
            width: 100%;
            max-width: 1080px;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        @media (max-width: 920px) {
            .auth-layout-grid {
                grid-template-columns: 1fr;
                max-width: 480px;
            }
            .auth-brand-side {
                display: none !important;
            }
        }

        /* Left Side: Brand & Hero Panel */
        .auth-brand-side {
            background: linear-gradient(145deg, #102A4E 0%, #083863 60%, #075998 100%);
            padding: 48px 40px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .auth-brand-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%2351B8E5' fill-opacity='0.04' fill-rule='evenodd'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.8;
            pointer-events: none;
        }

        .brand-top {
            position: relative;
            z-index: 2;
        }

        .brand-logo-img {
            height: 58px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));
            margin-bottom: 24px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(81, 184, 229, 0.15);
            border: 1px solid rgba(81, 184, 229, 0.35);
            color: #51B8E5;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
        }

        .brand-heading {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin: 0 0 12px;
        }

        .brand-description {
            font-size: 14px;
            color: #A9C5DF;
            line-height: 1.6;
            margin: 0;
        }

        .role-hierarchy-preview {
            position: relative;
            z-index: 2;
            margin: 32px 0;
            background: rgba(16, 42, 78, 0.5);
            border: 1px solid rgba(81, 184, 229, 0.2);
            border-radius: 16px;
            padding: 18px 20px;
            backdrop-filter: blur(8px);
        }

        .role-preview-title {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #51B8E5;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .role-preview-chain {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 12px;
        }

        .chain-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #E2EEF8;
            padding: 4px 0;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.08);
        }
        .chain-item:last-child {
            border-bottom: none;
        }

        .brand-footer {
            position: relative;
            z-index: 2;
            font-size: 11.5px;
            color: #7E9BB8;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Right Side: Form & Interactive Demo */
        .auth-form-side {
            padding: 42px 38px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #ffffff;
        }

        .form-header-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--auth-navy);
            letter-spacing: -0.02em;
            margin: 0 0 6px;
        }

        .form-header-sub {
            font-size: 13.5px;
            color: #5C728A;
            margin: 0 0 24px;
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

        .submit-login-btn {
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
            margin-top: 6px;
        }

        .submit-login-btn:hover {
            background: linear-gradient(135deg, #064a7e 0%, #075998 100%);
            box-shadow: 0 6px 20px rgba(7, 89, 152, 0.45);
            transform: translateY(-1px);
        }

        .submit-login-btn:active {
            transform: translateY(0);
        }

        /* 1-Click Role Fill Cards */
        .demo-roles-section {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #EEF2F6;
        }

        .demo-roles-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .demo-roles-label {
            font-size: 11px;
            font-weight: 800;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .demo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .demo-role-btn {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 8px 10px;
            text-align: left;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .demo-role-btn:hover {
            background: #EFF6FF;
            border-color: #BFDBFE;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(7, 89, 152, 0.08);
        }

        .role-indicator-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .demo-role-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--auth-navy);
            line-height: 1.2;
        }

        .demo-role-user {
            font-size: 10.5px;
            color: #64748B;
            font-family: monospace;
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
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-glow-1"></div>
        <div class="auth-glow-2"></div>

        <div class="auth-layout-grid">
            <!-- Left Brand Hero Panel -->
            <div class="auth-brand-side">
                <div class="brand-top">
                    <img src="{{ asset('images/logo.png') }}" alt="Operations Center Team Acosta 2028" class="brand-logo-img">
                    <div>
                        <span class="brand-badge">
                            <i class="bi bi-shield-check"></i> Field Operations Command
                        </span>
                        <h2 class="brand-heading">Operations Center Team Acosta 2028</h2>
                        <p class="brand-description">
                            Streamlined field directives, real-time census tracking, proof-of-work documentation, and comprehensive task orchestration across all operational desks.
                        </p>
                    </div>

                    <!-- Operational Highlights -->
                    <div style="margin-top: 36px; display: flex; flex-direction: column; gap: 14px;">
                        <div style="display: flex; align-items: center; gap: 12px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); padding: 12px 16px; border-radius: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(81, 184, 229, 0.2); color: #51B8E5; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="bi bi-broadcast"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 700; color: #FFFFFF;">Real-time Field Directives</div>
                                <div style="font-size: 11.5px; color: #94A3B8;">Instant bulk and individual task assignments</div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 12px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); padding: 12px 16px; border-radius: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(16, 185, 129, 0.2); color: #34D399; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="bi bi-clipboard-check-fill"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 700; color: #FFFFFF;">Proof of Accomplishment</div>
                                <div style="font-size: 11.5px; color: #94A3B8;">Evidence verification & submission monitoring</div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 12px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); padding: 12px 16px; border-radius: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(245, 158, 11, 0.2); color: #FBBF24; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="bi bi-calendar3-event"></i>
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 700; color: #FFFFFF;">Operations Calendar & Logs</div>
                                <div style="font-size: 11.5px; color: #94A3B8;">Hierarchical coordination across all clusters</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="brand-footer" style="margin-top: 28px;">
                    <i class="bi bi-lock-fill" style="color: #34D399;"></i>
                    <span>Authorized Personnel Only &bull; Operations Center Team Acosta 2028</span>
                </div>
            </div>

            <!-- Right Login Form Panel -->
            <div class="auth-form-side">
                <div>
                    <h1 class="form-header-title">Welcome Back</h1>
                    <p class="form-header-sub">Sign in with your operations credentials to continue</p>

                    @if(session('error'))
                        <div style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 4px solid #EF4444; padding: 12px 14px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; color: #DC2626; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-left: 4px solid #10B981; padding: 12px 14px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; color: #166534; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" id="loginForm">
                        @csrf
                        <div class="form-group">
                            <label for="login" class="form-label">Username or Verified Email</label>
                            <div class="input-group-custom">
                                <i class="bi bi-person-fill input-icon"></i>
                                <input type="text" id="login" name="login" class="form-control-custom @error('login') is-invalid @enderror" value="{{ old('login', 'admin') }}" required autofocus placeholder="Enter your username or email">
                            </div>
                            @error('login')
                                <div style="color: #DC2626; font-size: 12px; margin-top: 4px; font-weight: 600;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <label for="password" class="form-label" style="margin-bottom: 0;">Password</label>
                                <a href="javascript:void(0)" onclick="openModal('forgotPasswordModal')" style="font-size: 12px; font-weight: 700; color: var(--auth-primary); text-decoration: none;">
                                    Forgot Password?
                                </a>
                            </div>
                            <div class="input-group-custom">
                                <i class="bi bi-key-fill input-icon"></i>
                                <input type="password" id="password" name="password" class="form-control-custom @error('password') is-invalid @enderror" required placeholder="Enter password" value="password123">
                                <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('password', this)" tabindex="-1" title="Toggle visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div style="color: #DC2626; font-size: 12px; margin-top: 4px; font-weight: 600;">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" id="loginSubmitBtn" class="submit-login-btn">
                            <span id="loginBtnSpinner" class="btn-spinner" style="display: none;"></span>
                            <i id="loginBtnIcon" class="bi bi-box-arrow-in-right"></i>
                            <span id="loginBtnText">Sign In to Operations</span>
                        </button>
                    </form>
                </div>

                <!-- 1-Click Demo Accounts Grid -->
                <div class="demo-roles-section">
                    <div class="demo-roles-header">
                        <span class="demo-roles-label">
                            <i class="bi bi-lightning-charge-fill text-warning"></i> 1-Click Role Credential Fill
                        </span>
                        <span style="font-size: 10.5px; color: #64748B;">PW: <code>password123</code></span>
                    </div>

                    <div class="demo-grid">
                        <!-- 1. 627 Admin -->
                        <div class="demo-role-btn" onclick="fillCreds('admin', 'password123')" title="Click to auto-fill 627 Admin">
                            <span class="role-indicator-dot" style="background: #EF4444;"></span>
                            <div>
                                <div class="demo-role-name">627 (Admin)</div>
                                <div class="demo-role-user">admin</div>
                            </div>
                        </div>

                        <!-- 2. HQ Command -->
                        <div class="demo-role-btn" onclick="fillCreds('hq_lead', 'password123')" title="Click to auto-fill HQ">
                            <span class="role-indicator-dot" style="background: #1D4ED8;"></span>
                            <div>
                                <div class="demo-role-name">HQ Command</div>
                                <div class="demo-role-user">hq_lead</div>
                            </div>
                        </div>

                        <!-- 3. Cluster Head 1 -->
                        <div class="demo-role-btn" onclick="fillCreds('cluster_head1', 'password123')" title="Click to auto-fill Cluster Head 1">
                            <span class="role-indicator-dot" style="background: #0284C7;"></span>
                            <div>
                                <div class="demo-role-name">Cluster Head 1</div>
                                <div class="demo-role-user">cluster_head1</div>
                            </div>
                        </div>

                        <!-- 4. Top Coor 1 -->
                        <div class="demo-role-btn" onclick="fillCreds('topcoor_c1', 'password123')" title="Click to auto-fill Top Coor 1">
                            <span class="role-indicator-dot" style="background: #D97706;"></span>
                            <div>
                                <div class="demo-role-name">Top Coor (C1)</div>
                                <div class="demo-role-user">topcoor_c1</div>
                            </div>
                        </div>

                        <!-- 5. Registry Officer Juan -->
                        <div class="demo-role-btn" style="grid-column: 1 / -1;" onclick="fillCreds('officer_juan', 'password123')" title="Click to auto-fill Registry Officer">
                            <span class="role-indicator-dot" style="background: #10B981;"></span>
                            <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                                <div>
                                    <span class="demo-role-name">Registry Officer Juan</span>
                                    <span style="font-size: 10px; color: #166534; background: #DCFCE7; padding: 1px 5px; border-radius: 4px; margin-left: 4px;">Cluster 1</span>
                                </div>
                                <div class="demo-role-user">officer_juan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal-backdrop-custom" id="forgotPasswordModal">
        <div class="wa-modal-dialog" style="max-width: 440px;">
            <div class="wa-modal-header" style="background: #F0F9FF;">
                <div class="wa-modal-title" style="color: var(--auth-navy); font-weight: 700; font-size: 16px;">
                    <i class="bi bi-shield-lock-fill text-primary me-1"></i>
                    <span>Password Recovery</span>
                </div>
                <button type="button" class="wa-modal-close" onclick="closeModal('forgotPasswordModal')">&times;</button>
            </div>
            
            <form action="{{ route('password.email') }}" method="POST" id="forgotPasswordForm">
                @csrf
                <div class="wa-modal-body" style="padding: 24px 22px;">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <div style="width: 52px; height: 52px; border-radius: 50%; background: #EFF6FF; color: var(--auth-primary); display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 10px;">
                            <i class="bi bi-envelope-at-fill"></i>
                        </div>
                        <h3 style="font-size: 16px; font-weight: 800; color: var(--auth-navy); margin-bottom: 4px;">
                            Reset Your Password
                        </h3>
                        <p style="font-size: 13px; color: #5C728A; line-height: 1.4; margin: 0;">
                            Enter your registered email address or username to receive a secure recovery link.
                        </p>
                    </div>

                    @if(session('forgot_error'))
                        <div style="background: #FEF2F2; border-left: 4px solid #EF4444; padding: 10px 12px; border-radius: 6px; margin-bottom: 16px; font-size: 12.5px; color: #DC2626; font-weight: 600;">
                            <i class="bi bi-exclamation-octagon me-1"></i> {{ session('forgot_error') }}
                        </div>
                    @endif

                    @if(session('forgot_success'))
                        <div style="background: #ECFDF5; border-left: 4px solid #10B981; padding: 10px 12px; border-radius: 6px; margin-bottom: 16px; font-size: 12.5px; color: #166534; font-weight: 600;">
                            <i class="bi bi-check-circle me-1"></i> {{ session('forgot_success') }}
                        </div>
                    @endif

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label for="forgot_email" class="form-label">Email Address or Username</label>
                        <input type="text" id="forgot_email" name="email" class="form-control-custom" value="{{ old('email') }}" required autofocus placeholder="e.g. admin@teamacosta.com">
                    </div>

                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px 12px; font-size: 11.5px; color: #64748B; display: flex; gap: 8px; align-items: flex-start;">
                        <i class="bi bi-info-circle text-primary" style="font-size: 14px; margin-top: 1px;"></i>
                        <span>A recovery link will be sent to the verified email address linked with this personnel account.</span>
                    </div>
                </div>

                <div class="wa-modal-footer" style="display: flex; gap: 10px; justify-content: flex-end; padding: 14px 20px; background: #F8FAFC;">
                    <button type="button" class="btn-wa btn-wa-outline" onclick="closeModal('forgotPasswordModal')">
                        Cancel
                    </button>
                    <button type="submit" id="forgotSubmitBtn" class="btn-wa btn-wa-primary">
                        <span id="forgotBtnSpinner" class="btn-spinner" style="display: none;"></span>
                        <i id="forgotBtnIcon" class="bi bi-send me-1"></i>
                        <span id="forgotBtnText">Send Reset Link</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function fillCreds(username, password) {
            document.getElementById('login').value = username;
            document.getElementById('password').value = password;
            
            // Highlight inputs briefly to show autofill feedback
            const loginInput = document.getElementById('login');
            const passInput = document.getElementById('password');
            loginInput.style.backgroundColor = '#EFF6FF';
            passInput.style.backgroundColor = '#EFF6FF';
            setTimeout(() => {
                loginInput.style.backgroundColor = '';
                passInput.style.backgroundColor = '';
            }, 300);
        }

        function togglePasswordVisibility(inputId, btn) {
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

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // Close on clicking backdrop
        document.addEventListener('click', (e) => {
            if (e.target.classList && e.target.classList.contains('modal-backdrop-custom')) {
                closeModal(e.target.id);
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-backdrop-custom.active').forEach(modal => {
                    closeModal(modal.id);
                });
            }
        });

        // Handle Login form submission with spinner
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function () {
                const btn = document.getElementById('loginSubmitBtn');
                const spinner = document.getElementById('loginBtnSpinner');
                const icon = document.getElementById('loginBtnIcon');
                const text = document.getElementById('loginBtnText');

                if (btn) {
                    btn.disabled = true;
                    btn.style.opacity = '0.85';
                    btn.style.cursor = 'wait';
                }
                if (spinner) spinner.style.display = 'inline-block';
                if (icon) icon.style.display = 'none';
                if (text) text.textContent = 'Authenticating...';
            });
        }

        // Handle Forgot Password form submission with spinner
        const forgotForm = document.getElementById('forgotPasswordForm');
        if (forgotForm) {
            forgotForm.addEventListener('submit', function () {
                const btn = document.getElementById('forgotSubmitBtn');
                const spinner = document.getElementById('forgotBtnSpinner');
                const icon = document.getElementById('forgotBtnIcon');
                const text = document.getElementById('forgotBtnText');

                if (btn) {
                    btn.disabled = true;
                    btn.style.opacity = '0.85';
                    btn.style.cursor = 'wait';
                }
                if (spinner) spinner.style.display = 'inline-block';
                if (icon) icon.style.display = 'none';
                if (text) text.textContent = 'Sending Link...';
            });
        }

        @if(session('forgot_error') || session('forgot_success') || (isset($openForgotModal) && $openForgotModal))
            document.addEventListener('DOMContentLoaded', function() {
                openModal('forgotPasswordModal');
            });
        @endif
    </script>
</body>
</html>
