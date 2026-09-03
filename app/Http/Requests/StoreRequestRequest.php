<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:dns_change,ns_change,ip_change,transfer,renewal,dns,nameserver,upgrade,support,cancellation,billing,technical,other'],
            'service_type' => ['nullable', 'string', 'max:50'],
            'service_id' => ['nullable', 'integer'],
            'data' => ['nullable', 'array'],
            'data.*' => ['string'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'O tipo da solicitação é obrigatório.',
            'type.in' => 'O tipo informado não é válido. Valores aceitos: dns_change, ns_change, ip_change, transfer, renewal, dns, nameserver, upgrade, support, cancellation, billing, technical, other.',
            'service_type.max' => 'O tipo do serviço não pode ter mais de 50 caracteres.',
            'service_id.integer' => 'O ID do serviço deve ser um número inteiro.',
            'data.array' => 'Os dados devem ser um array.',
            'reason.max' => 'O motivo não pode ter mais de 500 caracteres.',
        ];
    }
}
