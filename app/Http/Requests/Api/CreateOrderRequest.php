<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'cart_items' => 'required_without:use_cart|array|min:1',
            'cart_items.*.product_id' => 'required_with:cart_items|exists:products,id',
            'cart_items.*.quantity' => 'required_with:cart_items|integer|min:1',
            'use_cart' => 'sometimes|boolean',
        ];
    }
}

