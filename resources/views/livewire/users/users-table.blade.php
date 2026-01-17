<div>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Users</h1>
                <p class="text-muted">Manage system users and their roles</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New User
            </a>
        </div>
    </div>

    @if(session('generated_password'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-key me-2"></i>User Created Successfully!</h5>
            <p class="mb-2"><strong>Generated Password:</strong></p>
            <div class="bg-light p-3 rounded mb-2">
                <code class="fs-5 fw-bold text-primary">{{ session('generated_password') }}</code>
            </div>
            <p class="mb-0"><small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Please save this password. It will not be shown again.</small></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @foreach($users as $user)
        @if(session('reset_password_' . $user->id))
            <div class="alert alert-info alert-dismissible fade show" role="alert" id="password-alert-{{ $user->id }}">
                <h5 class="alert-heading"><i class="fas fa-key me-2"></i>Password Reset for {{ $user->name }}</h5>
                <p class="mb-2"><strong>New Password:</strong></p>
                <div class="bg-light p-3 rounded mb-2">
                    <code class="fs-5 fw-bold text-primary">{{ session('reset_password_' . $user->id) }}</code>
                </div>
                <p class="mb-0"><small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Please save this password. It will not be shown again.</small></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Users</h5>
            <div>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search users..." style="width: 250px;">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Organization</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->mobile ?? 'N/A' }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->organization->name ?? 'N/A' }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-info me-1">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                @endforeach
                                @if($user->roles->isEmpty())
                                    <span class="text-muted">No roles assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">Active</span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @role('admin')
                                    <a href="{{ route('users.permissions', $user->id) }}" class="btn btn-sm btn-outline-info" title="Manage Permissions">
                                        <i class="fas fa-user-shield"></i>
                                    </a>
                                    <button wire:click="resetUserPassword({{ $user->id }})" 
                                            wire:confirm="Are you sure you want to reset the password for {{ $user->name }}? A new password will be generated and displayed."
                                            class="btn btn-sm btn-outline-warning" 
                                            title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    @endrole
                                    @if($user->id !== auth()->id())
                                    <button wire:click="delete({{ $user->id }})" 
                                            wire:confirm="Are you sure you want to delete this user?"
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No users found. <a href="{{ route('users.create') }}">Create one now</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

