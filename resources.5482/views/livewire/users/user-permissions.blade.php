<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Manage Permissions: {{ $user->name }}</h1>
            <p class="text-muted">Control access and permissions for this user</p>
        </div>
    </div>

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

    <div class="row">
        <!-- Role Management -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-tag me-2"></i>Role Management</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Current Roles</label>
                        <div class="mb-2">
                            @forelse($user->roles as $role)
                            <span class="badge bg-primary me-2">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                            @empty
                            <span class="text-muted">No roles assigned</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign Role</label>
                        <select wire:model="selectedRole" class="form-select">
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button wire:click="saveRole" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Role
                    </button>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>User Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Mobile:</th>
                            <td>{{ $user->mobile }}</td>
                        </tr>
                        <tr>
                            <th>Organization:</th>
                            <td>{{ $user->organization->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Management -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-key me-2"></i>Permission Management</h5>
            <button wire:click="savePermissions" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-2"></i>Save Permissions
            </button>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Select specific permissions for this user. Permissions from assigned roles are included automatically.</p>

            @foreach($permissions as $module => $modulePermissions)
            <div class="mb-4">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-folder me-2"></i>{{ ucfirst($module) }}
                </h6>
                <div class="row">
                    @foreach($modulePermissions as $permission)
                    <div class="col-md-4 col-lg-3 mb-2">
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                wire:model="selectedPermissions"
                                value="{{ $permission->id }}"
                                id="permission_{{ $permission->id }}"
                                @if(in_array($permission->id, $userPermissions)) checked @endif
                            >
                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                {{ ucfirst(str_replace(['.', '_'], [' ', ' '], $permission->name)) }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>
</div>
