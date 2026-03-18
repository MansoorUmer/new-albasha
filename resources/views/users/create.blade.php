@extends('layouts.admin')
@section('title', 'Create User')
@section('content-header', 'Create New User')

@section('css')
<style>
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border: none;
        font-weight: 600;
        font-size: 16px;
    }

    .card-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2d3748;
        font-size: 14px;
        letter-spacing: 0.5px;
    }

    .form-control {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 15px;
        background: #ffffff;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: #ffffff;
    }

    .form-control.is-invalid {
        border-color: #fc5c65;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(252, 92, 101, 0.1);
    }

    .invalid-feedback {
        display: block;
        color: #fc5c65;
        font-size: 13px;
        margin-top: 6px;
        font-weight: 500;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .role-group {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-top: 12px;
    }

    .role-option {
        position: relative;
    }

    .role-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .role-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        color: #4a5568;
        text-align: center;
        min-height: 80px;
        flex-direction: column;
    }

    .role-option input[type="radio"]:checked + .role-label {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        color: #667eea;
    }

    .role-label i {
        font-size: 24px;
    }

    .role-option input[type="radio"]:checked + .role-label i {
        color: #667eea;
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 35px;
        padding-top: 25px;
        border-top: 1px solid #e2e8f0;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        flex: 1;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    .btn-secondary {
        background: #e2e8f0;
        color: #4a5568;
        flex: 1;
    }

    .btn-secondary:hover {
        background: #cbd5e0;
    }

    .password-strength {
        margin-top: 8px;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
    }

    .password-strength.weak {
        background: #fee;
        color: #c53030;
    }

    .password-strength.medium {
        background: #fef5e7;
        color: #d68910;
    }

    .password-strength.strong {
        background: #eafaf1;
        color: #186a3b;
    }

    .help-text {
        font-size: 12px;
        color: #718096;
        margin-top: 6px;
        font-style: italic;
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #2d3748;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-plus mr-2"></i>Create New User
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <h5 class="form-section-title"><i class="fas fa-id-card mr-2" style="color: #667eea;"></i>Personal Information</h5>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">
                                        <i class="fas fa-user mr-2" style="color: #667eea;"></i>First Name
                                    </label>
                                    <input 
                                        type="text" 
                                        id="first_name"
                                        name="first_name" 
                                        class="form-control @error('first_name') is-invalid @enderror"
                                        placeholder="Enter first name"
                                        value="{{ old('first_name') }}"
                                        required
                                    >
                                    @error('first_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="last_name">
                                        <i class="fas fa-user mr-2" style="color: #667eea;"></i>Last Name
                                    </label>
                                    <input 
                                        type="text" 
                                        id="last_name"
                                        name="last_name" 
                                        class="form-control @error('last_name') is-invalid @enderror"
                                        placeholder="Enter last name"
                                        value="{{ old('last_name') }}"
                                        required
                                    >
                                    @error('last_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope mr-2" style="color: #667eea;"></i>Email Address
                                </label>
                                <input 
                                    type="email" 
                                    id="email"
                                    name="email" 
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Enter email address"
                                    value="{{ old('email') }}"
                                    required
                                >
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <p class="help-text">Email must be unique and valid</p>
                            </div>
                        </div>

                        <!-- Security Section -->
                        <div class="form-section">
                            <h5 class="form-section-title"><i class="fas fa-lock mr-2" style="color: #667eea;"></i>Security</h5>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password">
                                        <i class="fas fa-key mr-2" style="color: #667eea;"></i>Password
                                    </label>
                                    <input 
                                        type="password" 
                                        id="password"
                                        name="password" 
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Enter password"
                                        required
                                        minlength="8"
                                        onkeyup="checkPasswordStrength(this.value)"
                                    >
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <div id="strength" style="margin-top: 8px;"></div>
                                    <p class="help-text">Minimum 8 characters</p>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">
                                        <i class="fas fa-check-circle mr-2" style="color: #667eea;"></i>Confirm Password
                                    </label>
                                    <input 
                                        type="password" 
                                        id="password_confirmation"
                                        name="password_confirmation" 
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        placeholder="Confirm password"
                                        required
                                    >
                                    @error('password_confirmation')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <p class="help-text">Must match the password above</p>
                                </div>
                            </div>
                        </div>

                        <!-- Role Selection Section -->
                        <div class="form-section">
                            <h5 class="form-section-title"><i class="fas fa-shield-alt mr-2" style="color: #667eea;"></i>User Role</h5>

                            <label>Select Role</label>
                            <div class="role-group">
                                <div class="role-option">
                                    <input 
                                        type="radio" 
                                        id="role_admin"
                                        name="role" 
                                        value="admin" 
                                        {{ old('role') === 'admin' ? 'checked' : '' }}
                                        required
                                    >
                                    <label for="role_admin" class="role-label">
                                        <i class="fas fa-crown"></i>
                                        <span>Administrator</span>
                                    </label>
                                </div>

                                <div class="role-option">
                                    <input 
                                        type="radio" 
                                        id="role_cashier"
                                        name="role" 
                                        value="cashier" 
                                        {{ old('role') === 'cashier' ? 'checked' : '' }}
                                    >
                                    <label for="role_cashier" class="role-label">
                                        <i class="fas fa-cash-register"></i>
                                        <span>Cashier</span>
                                    </label>
                                </div>

                                <div class="role-option">
                                    <input 
                                        type="radio" 
                                        id="role_kitchen"
                                        name="role" 
                                        value="kitchen" 
                                        {{ old('role') === 'kitchen' ? 'checked' : '' }}
                                    >
                                    <label for="role_kitchen" class="role-label">
                                        <i class="fas fa-chef-hat"></i>
                                        <span>Kitchen Staff</span>
                                    </label>
                                </div>
                            </div>
                            @error('role')
                                <span class="invalid-feedback" style="display: block; margin-top: 10px;">{{ $message }}</span>
                            @enderror
                            <p class="help-text" style="margin-top: 12px;">
                                <strong>Admin:</strong> Full system access | 
                                <strong>Cashier:</strong> Orders & transactions | 
                                <strong>Kitchen:</strong> Order preparation
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="button-group">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check mr-2"></i>Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkPasswordStrength(password) {
    const strengthDiv = document.getElementById('strength');
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    if (password.length === 0) {
        strengthDiv.innerHTML = '';
    } else if (strength <= 1) {
        strengthDiv.innerHTML = '<div class="password-strength weak"><i class="fas fa-exclamation-circle mr-1"></i>Weak Password</div>';
    } else if (strength <= 2) {
        strengthDiv.innerHTML = '<div class="password-strength medium"><i class="fas fa-check-circle mr-1"></i>Medium Strength</div>';
    } else {
        strengthDiv.innerHTML = '<div class="password-strength strong"><i class="fas fa-check-circle mr-1"></i>Strong Password</div>';
    }
}
</script>
@endsection
