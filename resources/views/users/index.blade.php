@extends('layouts.admin')
@section('title', 'Manage Users')
@section('content-header', 'User Management')

@section('content-actions')
    <a href="{{ route('users.create') }}" class="btn btn-primary" style="border-radius: 8px;">
        <i class="fas fa-plus mr-2"></i>Create New User
    </a>
@endsection

@section('css')
<style>
    .user-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .user-card:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .user-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
    }

    .user-info h5 {
        margin: 0;
        font-weight: 600;
        font-size: 16px;
    }

    .user-info p {
        margin: 3px 0 0 0;
        font-size: 13px;
        opacity: 0.9;
    }

    .user-body {
        padding: 20px;
    }

    .user-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
    }

    .meta-label {
        font-size: 12px;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .meta-value {
        font-size: 14px;
        color: #2d3748;
        font-weight: 500;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-badge.admin {
        background: linear-gradient(135deg, #fc5c65, #ff6b6b);
        color: white;
    }

    .role-badge.cashier {
        background: linear-gradient(135deg, #4ecdc4, #44a08d);
        color: white;
    }

    .role-badge.kitchen {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
    }

    .user-actions {
        display: flex;
        gap: 8px;
        padding-top: 15px;
        border-top: 1px solid #e2e8f0;
    }

    .user-actions a, .user-actions button {
        flex: 1;
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        text-align: center;
    }

    .btn-edit {
        background: #667eea;
        color: white;
    }

    .btn-edit:hover {
        background: #5a67d8;
        transform: translateY(-1px);
    }

    .btn-delete {
        background: #fc5c65;
        color: white;
        border: none;
    }

    .btn-delete:hover {
        background: #eb4c56;
        transform: translateY(-1px);
    }

    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 12px;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5e0;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #4a5568;
        margin: 0 0 10px 0;
    }

    .empty-state p {
        color: #718096;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .users-grid {
            grid-template-columns: 1fr;
        }

        .user-meta {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    @if($users->count() > 0)
        <div class="users-grid">
            @foreach($users as $user)
                <div class="user-card">
                    <div class="user-header">
                        <div class="user-avatar">
                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <h5>{{ $user->getFullname() }}</h5>
                            <p>{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="user-body">
                        <div class="user-meta">
                            <div class="meta-item">
                                <span class="meta-label">Role</span>
                                <span class="role-badge {{ $user->role }}">
                                    <i class="fas {{ $user->role === 'admin' ? 'fa-crown' : ($user->role === 'cashier' ? 'fa-cash-register' : 'fa-chef-hat') }}"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Member Since</span>
                                <span class="meta-value">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="user-actions">
                            <a href="{{ route('users.edit', $user) }}" class="btn-edit">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                      style="flex: 1;" 
                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" style="width: 100%;">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            @else
                                <button class="btn-delete" onclick="alert('You cannot delete yourself!')" style="opacity: 0.5; cursor: not-allowed;">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>No Users Found</h3>
            <p>Start by creating your first user account</p>
            <a href="{{ route('users.create') }}" class="btn btn-primary" style="border-radius: 8px; display: inline-block;">
                <i class="fas fa-plus mr-2"></i>Create First User
            </a>
        </div>
    @endif
</div>
@endsection
