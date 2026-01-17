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
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'pincode' => 'nullable|string|max:10',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}

