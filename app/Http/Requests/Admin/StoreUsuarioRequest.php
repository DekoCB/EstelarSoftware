<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'cargo'                 => ['nullable', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'unique:users,email', 'max:255'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'rol'                   => ['required', 'exists:roles,name'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'  => 'nombre',
            'email' => 'correo',
            'rol'   => 'rol',
        ];
    }
}
