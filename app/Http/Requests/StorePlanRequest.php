<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:domain,hosting,email'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'type.required' => 'O tipo é obrigatório.',
            'type.in' => 'O tipo informado não é válido. Valores aceitos: domain, hosting, email.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico.',
            'price.min' => 'O preço não pode ser negativo.',
            'duration_months.required' => 'A duração em meses é obrigatória.',
            'duration_months.integer' => 'A duração em meses deve ser um número inteiro.',
            'duration_months.min' => 'A duração em meses deve ser pelo menos 1.',
            'features.array' => 'As funcionalidades devem ser um array.',
            'features.*.string' => 'Cada funcionalidade deve ser um texto.',
            'active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
