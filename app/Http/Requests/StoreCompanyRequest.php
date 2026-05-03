<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
      return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:companies,email',
        'phone' => 'required|string|max:20',
        'address' => 'required|string',
        'preferred_language' => 'required|string|max:20',
        'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ];
    }
}
