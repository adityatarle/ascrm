<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $resetPasswordUserId = null;
    public $resetPassword = null;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetUserPassword($userId)
    {
        $currentUser = Auth::user();
        
        // Check if user is admin
        if (!$currentUser->hasRole('admin')) {
            session()->flash('error', 'Only administrators can reset passwords.');
            return;
        }

        $user = User::where('organization_id', $currentUser->organization_id)
            ->findOrFail($userId);

        // Generate a new secure password
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $newPassword = '';
        $max = strlen($characters) - 1;
        
        for ($i = 0; $i < 12; $i++) {
            $newPassword .= $characters[random_int(0, $max)];
        }

        // Update user password
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Store password to display
        $this->resetPasswordUserId = $userId;
        $this->resetPassword = $newPassword;

        session()->flash('reset_password_' . $userId, $newPassword);
        session()->flash('message', 'Password reset successfully for ' . $user->name);
    }

    public function closePasswordModal()
    {
        $this->resetPasswordUserId = null;
        $this->resetPassword = null;
    }

    public function delete($userId)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->hasRole('admin')) {
            session()->flash('error', 'Only administrators can delete users.');
            return;
        }

        // Prevent self-deletion
        if ($userId == $user->id) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $userToDelete = User::where('organization_id', $user->organization_id)
            ->findOrFail($userId);

        $userToDelete->delete();

        session()->flash('message', 'User deleted successfully');
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = User::with(['organization', 'roles'])
            ->where('organization_id', $user->organization_id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('mobile', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.users.users-table', [
            'users' => $users,
        ]);
    }
}

