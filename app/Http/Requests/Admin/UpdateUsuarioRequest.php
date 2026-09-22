<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'cargo'                 => ['nullable', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->usuario->id)],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable'],
            'rol'                   => ['required', 'exists:roles,name'],
        ];
    }
}
