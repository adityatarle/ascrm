<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class DealerRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:dealers,mobile',
            'email' => 'nullable|email|unique:dealers,email',
            'gstin' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'state_id' => 'required|exists:tbl_state_master,fld_state_id',
            'district_id' => 'required|exists:tbl_dist_master,fld_dist_id',
            'taluka_id' => 'required|exists:tbl_taluka_master,fld_taluka_id',
            'city_id' => 'required|exists:cities,id',
            'pincode' => 'nullable|string|max:10',
            'password' => 'required|string|min:8|confirmed',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}

