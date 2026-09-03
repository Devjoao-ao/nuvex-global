<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['sometimes', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:500'],
            'username' => ['sometimes', 'string', 'max:255'],
            'password' => ['sometimes', 'string', 'max:255'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'additional_info' => ['nullable', 'string', 'max:2000'],
            'is_visible_to_customer' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'label.max' => 'A etiqueta não pode ter mais de 255 caracteres.',
            'url.url' => 'A URL deve ser um endereço válido.',
            'url.max' => 'A URL não pode ter mais de 500 caracteres.',
            'username.max' => 'O nome de usuário não pode ter mais de 255 caracteres.',
            'password.max' => 'A senha não pode ter mais de 255 caracteres.',
            'port.integer' => 'A porta deve ser um número inteiro.',
            'port.min' => 'A porta mínima é 1.',
            'port.max' => 'A porta máxima é 65535.',
            'instructions.max' => 'As instruções não podem ter mais de 2000 caracteres.',
            'additional_info.max' => 'As informações adicionais não podem ter mais de 2000 caracteres.',
            'is_visible_to_customer.boolean' => 'O campo visível ao cliente deve ser verdadeiro ou falso.',
        ];
    }
}
