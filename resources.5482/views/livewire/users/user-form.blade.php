<div>
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">{{ $userId ? 'Edit' : 'Create' }} User</h1>
            <p class="text-muted">{{ $userId ? 'Update user information and roles' : 'Add a new user to the system' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile Number *</label>
                        <input type="text" wire:model="mobile" class="form-control @error('mobile') is-invalid @enderror" placeholder="10 digit mobile number" maxlength="10">
                        @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                        <small class="text-muted">User will login using this mobile number</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" wire:model="dateOfBirth" class="form-control @error('dateOfBirth') is-invalid @enderror">
                        @error('dateOfBirth') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gender</label>
                        <select wire:model="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">-- Select Gender --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" wire:model="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Landline number">
                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea wire:model="address" class="form-control @error('address') is-invalid @enderror" rows="2"></textarea>
                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" wire:model="city" class="form-control @error('city') is-invalid @enderror">
                        @error('city') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" wire:model="state" class="form-control @error('state') is-invalid @enderror">
                        @error('state') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pincode</label>
                        <input type="text" wire:model="pincode" class="form-control @error('pincode') is-invalid @enderror" maxlength="10">
                        @error('pincode') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alternate Email</label>
                        <input type="email" wire:model="alternateEmail" class="form-control @error('alternateEmail') is-invalid @enderror">
                        @error('alternateEmail') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Emergency Contact Name</label>
                        <input type="text" wire:model="emergencyContactName" class="form-control @error('emergencyContactName') is-invalid @enderror">
                        @error('emergencyContactName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Emergency Contact Phone</label>
                        <input type="text" wire:model="emergencyContactPhone" class="form-control @error('emergencyContactPhone') is-invalid @enderror">
                        @error('emergencyContactPhone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea wire:model="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"></textarea>
                    @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Organization *</label>
                        <select wire:model="organizationId" class="form-select @error('organizationId') is-invalid @enderror">
                            <option value="">-- Select Organization --</option>
                            @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                        @error('organizationId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role *</label>
                        <select wire:model="role" class="form-select @error('role') is-invalid @enderror">
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $roleOption)
                            <option value="{{ $roleOption->name }}">{{ ucfirst(str_replace('_', ' ', $roleOption->name)) }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if(!$userId)
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Password will be auto-generated</strong> when you create the user. The generated password will be displayed after creation.
                        </div>
                    </div>
                </div>
                @else
                <div class="row">
                    <div class="col-12 mb-3">
                        @role('admin')
                        <div class="alert alert-warning">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-key me-2"></i>
                                    <strong>Reset Password:</strong> Generate a new password for this user
                                </div>
                                <button type="button" wire:click="resetPassword" 
                                        wire:confirm="Are you sure you want to reset the password? A new password will be generated and displayed."
                                        class="btn btn-warning btn-sm">
                                    <i class="fas fa-key me-1"></i>Reset Password
                                </button>
                            </div>
                        </div>
                        @endrole
                    </div>
                    @if(session('reset_password'))
                    <div class="col-12 mb-3">
                        <div class="alert alert-success">
                            <h5 class="alert-heading"><i class="fas fa-key me-2"></i>Password Reset Successfully!</h5>
                            <p class="mb-2"><strong>New Password:</strong></p>
                            <div class="bg-light p-3 rounded mb-2">
                                <code class="fs-5 fw-bold text-primary">{{ session('reset_password') }}</code>
                            </div>
                            <p class="mb-0"><small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Please save this password. It will not be shown again.</small></p>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6 mb-3">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    @if($password)
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" wire:model="passwordConfirmation" class="form-control @error('passwordConfirmation') is-invalid @enderror">
                        @error('passwordConfirmation') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    @endif
                </div>
                @endif

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $userId ? 'Update' : 'Create' }} User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

