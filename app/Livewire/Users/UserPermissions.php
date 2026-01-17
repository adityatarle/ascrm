<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserPermissions extends Component
{
    public $userId;
    public $user;
    public $selectedRole;
    public $selectedPermissions = [];
    
    public $roles = [];
    public $permissions = [];
    public $userRoles = [];
    public $userPermissions = [];

    public function mount($user = null)
    {
        if (!$user) {
            return redirect()->route('users.index');
        }

        // Handle route model binding
        if (is_object($user)) {
            $this->user = $user;
        } else {
            $this->user = User::where('organization_id', Auth::user()->organization_id)
                ->findOrFail($user);
        }

        $this->userId = $this->user->id;
        
        // Load roles and permissions
        $this->roles = Role::where('guard_name', 'web')->orderBy('name', 'asc')->get();
        $this->permissions = Permission::where('guard_name', 'web')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy(function($permission) {
                // Group by module (e.g., 'products', 'orders', 'dealers')
                $parts = explode('.', $permission->name);
                return $parts[0] ?? 'other';
            });
        
        // Load user's current roles and permissions
        $this->userRoles = $this->user->roles->pluck('id')->toArray();
        $this->userPermissions = $this->user->getAllPermissions()->pluck('id')->toArray();
        $this->selectedPermissions = $this->userPermissions;
        
        if (count($this->userRoles) > 0) {
            $this->selectedRole = $this->userRoles[0];
        }
    }

    public function updatedSelectedRole()
    {
        // When role changes, update permissions based on role
        if ($this->selectedRole) {
            $role = Role::find($this->selectedRole);
            if ($role) {
                $rolePermissions = $role->permissions->pluck('id')->toArray();
                $this->selectedPermissions = array_merge($this->selectedPermissions, $rolePermissions);
                $this->selectedPermissions = array_unique($this->selectedPermissions);
            }
        }
    }

    public function saveRole()
    {
        if (!$this->selectedRole) {
            session()->flash('error', 'Please select a role');
            return;
        }

        $this->user->syncRoles([$this->selectedRole]);
        
        session()->flash('message', 'User role updated successfully');
        $this->mount($this->user);
    }

    public function savePermissions()
    {
        $this->user->syncPermissions($this->selectedPermissions);
        
        session()->flash('message', 'User permissions updated successfully');
        $this->mount($this->user);
    }

    public function togglePermission($permissionId)
    {
        if (in_array($permissionId, $this->selectedPermissions)) {
            $this->selectedPermissions = array_diff($this->selectedPermissions, [$permissionId]);
        } else {
            $this->selectedPermissions[] = $permissionId;
        }
    }

    public function render()
    {
        return view('livewire.users.user-permissions');
    }
}
