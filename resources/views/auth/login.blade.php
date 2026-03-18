@extends('layouts.auth')

@section('css')
<style>
    body.hold-transition.login-page {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif;
    }

    .login-box {
        width: 100%;
        max-width: 420px;
        background: transparent;
    }

    .login-logo {
        text-align: center;
        font-size: 32px;
        font-weight: 700;
        color: white;
        margin-bottom: 40px;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        letter-spacing: 1px;
    }

    .card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-card-body {
        padding: 40px;
    }

    .login-box-msg {
        font-size: 16px;
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 30px;
        text-align: center;
        letter-spacing: 0.5px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2d3748;
        font-size: 14px;
    }

    .input-group {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .input-group:focus-within {
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        transform: translateY(-2px);
    }

    .input-group-text {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        color: #667eea;
        border-radius: 12px 0 0 12px;
        padding: 0 16px;
        display: flex;
        align-items: center;
        font-size: 18px;
    }

    .form-control {
        border: 1px solid #e2e8f0;
        border-left: none;
        padding: 12px 16px;
        font-size: 15px;
        background: #ffffff;
        transition: all 0.3s ease;
        border-radius: 0 12px 12px 0;
        font-weight: 500;
        color: #2d3748;
    }

    .form-control::placeholder {
        color: #a0aec0;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: none;
    }

    .form-control:focus + .input-group-text,
    .form-control:focus {
        background-color: #f7fafc;
    }

    .form-control.is-invalid {
        border-color: #fc5c65;
    }

    .invalid-feedback {
        display: block;
        color: #fc5c65;
        font-size: 13px;
        margin-top: 6px;
        font-weight: 500;
    }

    .row {
        margin-top: 30px;
    }

    .icheck-primary {
        display: flex;
        align-items: center;
    }

    .icheck-primary input[type="checkbox"] {
        margin-right: 8px;
        cursor: pointer;
        width: 18px;
        height: 18px;
    }

    .icheck-primary label {
        margin: 0;
        font-weight: 500;
        color: #4a5568;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
        background: linear-gradient(135deg, #5568d3 0%, #653a91 100%);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-block {
        width: 100%;
        padding: 14px 24px;
        font-size: 16px;
    }

    .forgot-password, .register-link {
        text-align: center;
        margin-top: 20px;
    }

    .forgot-password a, .register-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .forgot-password a:hover, .register-link a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    .mb-1 {
        margin-bottom: 12px;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .divider {
        text-align: center;
        margin: 20px 0;
        color: #cbd5e0;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
<p class="login-box-msg">
    <i class="fas fa-lock mr-2" style="color: #667eea;"></i>Sign in to your account
</p>

<form action="{{ route('login') }}" method="post">
    @csrf
    <div class="form-group">
        <label for="email"><i class="fas fa-envelope mr-2" style="color: #667eea;"></i>Email Address</label>
        <div class="input-group">
            <input 
                type="email" 
                id="email"
                name="email" 
                class="form-control @error('email') is-invalid @enderror"
                placeholder="Enter your email" 
                value="{{ old('email') }}" 
                required 
                autocomplete="email" 
                autofocus
            >
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password"><i class="fas fa-lock mr-2" style="color: #667eea;"></i>Password</label>
        <div class="input-group">
            <input 
                type="password" 
                id="password"
                class="form-control @error('password') is-invalid @enderror" 
                placeholder="Enter your password"
                name="password" 
                required 
                autocomplete="current-password"
            >
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="row">
        <div class="col-6">
            <div class="icheck-primary">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Remember Me</label>
            </div>
        </div>
        <div class="col-6 text-right">
            <a href="{{ route('password.request') }}" style="color: #667eea; font-weight: 500; font-size: 13px;">
                Forgot?
            </a>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block mt-4">
        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
    </button>
</form>

<div class="divider">
    OR
</div>

<div class="forgot-password mb-1">
    <a href="{{ route('password.request') }}">
        <i class="fas fa-key mr-1"></i>I forgot my password
    </a>
</div>

@endsection