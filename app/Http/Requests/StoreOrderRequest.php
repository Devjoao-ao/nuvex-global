<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'string', 'in:domain,hosting,email'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.plan_id' => ['nullable', 'exists:plans,id'],
            'items.*.domain_name' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['sometimes', 'integer', 'min:1'],
            'items.*.metadata' => ['nullable', 'array'],
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Os itens do pedido são obrigatórios.',
            'items.array' => 'Os itens do pedido devem ser um array.',
            'items.min' => 'O pedido deve conter pelo menos 1 item.',
            'items.*.type.required' => 'O tipo do item é obrigatório.',
            'items.*.type.in' => 'O tipo informado não é válido.',
            'items.*.name.required' => 'O nome do item é obrigatório.',
            'items.*.price.required' => 'O preço do item é obrigatório.',
            'items.*.price.numeric' => 'O preço do item deve ser um valor numérico.',
            'items.*.price.min' => 'O preço do item não pode ser negativo.',
            'items.*.plan_id.exists' => 'O plano informado não existe.',
            'payment_method.required' => 'A forma de pagamento é obrigatória.',
        ];
    }
}
