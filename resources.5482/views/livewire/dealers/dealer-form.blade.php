<div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $dealerId ? 'Edit' : 'Create' }} Dealer</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" wire:model="name" class="form-control">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile *</label>
                        <input type="text" wire:model="mobile" class="form-control">
                        @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" wire:model="email" class="form-control">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">GSTIN</label>
                        <input type="text" wire:model="gstin" class="form-control">
                        @error('gstin') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea wire:model="address" class="form-control" rows="2"></textarea>
                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State *</label>
                        <select wire:model.live="stateId" class="form-select">
                            <option value="">-- Select State --</option>
                            @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @error('stateId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City *</label>
                        <select wire:model.live="cityId" class="form-select">
                            <option value="">-- Select City --</option>
                            @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('cityId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Zone</label>
                        <select wire:model="zoneId" class="form-select">
                            <option value="">-- Select Zone --</option>
                            @foreach($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                        @error('zoneId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pincode</label>
                        <input type="text" wire:model="pincode" class="form-control">
                        @error('pincode') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input type="checkbox" wire:model="isActive" class="form-check-input" id="isActive">
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">Additional Information</h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" wire:model="dateOfBirth" class="form-control">
                        @error('dateOfBirth') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender</label>
                        <select wire:model="gender" class="form-select">
                            <option value="">-- Select Gender --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Alternate Mobile</label>
                        <input type="text" wire:model="alternateMobile" class="form-control">
                        @error('alternateMobile') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone (Landline)</label>
                        <input type="text" wire:model="phone" class="form-control">
                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alternate Email</label>
                        <input type="email" wire:model="alternateEmail" class="form-control">
                        @error('alternateEmail') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Person Name</label>
                        <input type="text" wire:model="contactPersonName" class="form-control">
                        @error('contactPersonName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Person Phone</label>
                        <input type="text" wire:model="contactPersonPhone" class="form-control">
                        @error('contactPersonPhone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">Business Information</h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">PAN Number</label>
                        <input type="text" wire:model="panNumber" class="form-control" maxlength="10">
                        @error('panNumber') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Aadhar Number</label>
                        <input type="text" wire:model="aadharNumber" class="form-control" maxlength="12">
                        @error('aadharNumber') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Registration Date</label>
                        <input type="date" wire:model="registrationDate" class="form-control">
                        @error('registrationDate') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dealer Type</label>
                        <select wire:model="dealerType" class="form-select">
                            <option value="">-- Select Type --</option>
                            <option value="retailer">Retailer</option>
                            <option value="wholesaler">Wholesaler</option>
                            <option value="distributor">Distributor</option>
                            <option value="other">Other</option>
                        </select>
                        @error('dealerType') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Credit Limit (₹)</label>
                        <input type="number" wire:model="creditLimit" step="0.01" min="0" class="form-control">
                        @error('creditLimit') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Credit Days</label>
                        <input type="number" wire:model="creditDays" min="0" class="form-control">
                        @error('creditDays') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bank Details</label>
                    <textarea wire:model="bankDetails" class="form-control" rows="3" placeholder="Bank name, account number, IFSC code, etc."></textarea>
                    @error('bankDetails') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea wire:model="notes" class="form-control" rows="3"></textarea>
                    @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('dealers.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

