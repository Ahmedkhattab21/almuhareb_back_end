<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanySupervisorRequest extends FormRequest
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
        'company_id' => 'required|exists:companies,id',

        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',

        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'preferred_language' => 'required|string|max:20',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        'position' => 'required|string|max:255',
    ];
    }
}
