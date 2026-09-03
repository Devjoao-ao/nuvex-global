<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ip' => ['required', 'string', 'max:45'],
        ];
    }

    public function messages(): array
    {
        return [
            'ip.required' => 'O endereço IP é obrigatório.',
            'ip.max' => 'O endereço IP não pode ter mais de 45 caracteres.',
        ];
    }
}
