<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkerRequest extends FormRequest
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
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',

        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'preferred_language' => 'required|string|max:20',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        'nationality' => 'required|string|max:255',
        'job_title' => 'required|string|max:255',
    ];
    }
}
