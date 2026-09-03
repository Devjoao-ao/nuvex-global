<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDnsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dns_provider' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'dns_provider.required' => 'O provedor DNS é obrigatório.',
            'dns_provider.max' => 'O provedor DNS não pode ter mais de 255 caracteres.',
        ];
    }
}
