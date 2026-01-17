<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserForm extends Component
{
    public $userId;
    public $name;
    public $email;
    public $mobile;
    public $password;
    public $passwordConfirmation;
    public $role;
    public $organizationId;
    public $generatedPassword = null; // Store auto-generated password to display

    public $roles = [];
    public $organizations = [];

    public function mount($user = null)
    {
        $this->roles = Role::where('guard_name', 'web')->orderBy('name', 'asc')->get();
        $this->organizations = Organization::orderBy('name', 'asc')->get();

        if ($user) {
            // Handle route model binding - Laravel passes the model instance
            if (is_object($user)) {
                $userModel = $user;
            } else {
                // Fallback if ID is passed
                $userModel = User::where('organization_id', Auth::user()->organization_id)
                    ->findOrFail($user);
            }

            $this->userId = $userModel->id;
            $this->name = $userModel->name;
            $this->email = $userModel->email;
            $this->mobile = $userModel->mobile;
            $this->organizationId = $userModel->organization_id;
            
            // Get user's first role
            $userRole = $userModel->roles->first();
            $this->role = $userRole ? $userRole->name : '';
        } else {
            $this->organizationId = Auth::user()->organization_id;
        }
    }

    public function generatePassword()
    {
        // Generate a random 12-character password with secure random bytes
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        $max = strlen($characters) - 1;
        
        for ($i = 0; $i < 12; $i++) {
            $password .= $characters[random_int(0, $max)];
        }
        
        $this->generatedPassword = $password;
        $this->password = $this->generatedPassword;
        $this->passwordConfirmation = $this->generatedPassword;
    }

    public function resetPassword()
    {
        if (!$this->userId) {
            session()->flash('error', 'Cannot reset password for new user.');
            return;
        }

        $currentUser = Auth::user();
        
        // Check if user is admin
        if (!$currentUser->hasRole('admin')) {
            session()->flash('error', 'Only administrators can reset passwords.');
            return;
        }

        $user = User::where('organization_id', $currentUser->organization_id)
            ->findOrFail($this->userId);

        // Generate a new secure password
        $this->generatePassword();

        // Update user password
        $user->update([
            'password' => Hash::make($this->generatedPassword),
        ]);

        session()->flash('reset_password', $this->generatedPassword);
        session()->flash('message', 'Password reset successfully. Please note the new password below.');
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'mobile' => 'required|string|regex:/^[0-9]{10}$/|unique:users,mobile,' . $this->userId,
            'role' => 'required|string|exists:roles,name',
            'organizationId' => 'required|exists:organizations,id',
        ];

        // For new users, auto-generate password if not already generated
        if (!$this->userId) {
            if (!$this->generatedPassword) {
                $this->generatePassword();
            }
            $rules['password'] = 'required|string|min:8';
        } elseif ($this->password) {
            // For existing users, only validate if password is being changed
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $this->validate($rules);

        if ($this->userId) {
            $user = User::where('organization_id', Auth::user()->organization_id)
                ->findOrFail($this->userId);

            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
                'mobile' => $this->mobile,
                'organization_id' => $this->organizationId,
            ];

            if ($this->password) {
                $updateData['password'] = Hash::make($this->password);
                // Store password to display if manually set
                if ($this->password && !$this->generatedPassword) {
                    session()->flash('reset_password', $this->password);
                }
            }

            $user->update($updateData);

            // Update role
            $user->syncRoles([$this->role]);

            session()->flash('message', 'User updated successfully');
        } else {
            // Auto-generate password for new users
            $this->generatePassword();
            
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'mobile' => $this->mobile,
                'password' => Hash::make($this->generatedPassword),
                'organization_id' => $this->organizationId,
            ]);

            // Assign role
            $user->assignRole($this->role);

            // Store generated password in session to display
            session()->flash('generated_password', $this->generatedPassword);
            session()->flash('message', 'User created successfully. Please note the generated password below.');
        }

        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.users.user-form');
    }
}

