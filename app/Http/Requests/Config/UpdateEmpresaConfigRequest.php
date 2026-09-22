<?php

namespace App\Http\Requests\Config;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmpresaConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('configuracion.editar');
    }

    public function rules(): array
    {
        return [
            // Datos fiscales
            'ruc'              => ['nullable', 'string', 'size:11'],
            'razon_social'     => ['nullable', 'string', 'max:200'],
            'nombre_comercial' => ['nullable', 'string', 'max:200'],
            'direccion'        => ['nullable', 'string', 'max:500'],
            'ubigeo'           => ['nullable', 'string', 'size:6'],
            'email'            => ['nullable', 'email', 'max:150'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'web'              => ['nullable', 'url', 'max:200'],

            // Reuniones
            'recordatorio_reunion_minutos' => ['nullable', 'integer', 'min:1', 'max:10080'],
            'alerta_despues_minutos'       => ['nullable', 'integer', 'min:1', 'max:10080'],

            // Logos
            'logo_sidebar'     => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:512'],
            'logo_login'       => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:1024'],
            'logo_documentos'  => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp',     'max:1024'],

            // Facturación
            'igv_porcentaje'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'moneda'           => ['nullable', 'string', 'size:3'],
            'serie_boleta'     => ['nullable', 'string', 'max:10'],
            'serie_factura'    => ['nullable', 'string', 'max:10'],
            'sunat_modo'       => ['nullable', 'in:sandbox,produccion'],
            'nubefact_url'     => ['nullable', 'url', 'max:200'],
            'nubefact_token'   => ['nullable', 'string', 'max:500'],

            // Certificado
            'certificado_pfx_path' => ['nullable', 'string', 'max:300'],
            'certificado_pfx_clave'=> ['nullable', 'string', 'max:200'],

            // Flags de eliminación de logos
            'delete_logo_sidebar'    => ['nullable', 'boolean'],
            'delete_logo_login'      => ['nullable', 'boolean'],
            'delete_logo_documentos' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'ruc'             => 'RUC',
            'razon_social'    => 'razón social',
            'igv_porcentaje'  => 'porcentaje de IGV',
            'logo_sidebar'    => 'logo sidebar',
            'logo_login'      => 'logo login',
            'logo_documentos' => 'logo documentos',
        ];
    }
}
