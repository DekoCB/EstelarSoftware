<?php

namespace App\Http\Requests\Eventos;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Comprobante de pago de la inscripcion de un equipo. Publico (sin
 * login): quien tiene el token del equipo -lo recibe al inscribirse-
 * puede subirlo.
 */
class StoreComprobantePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comprobante' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'comprobante' => 'comprobante de pago',
        ];
    }
}
