<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:support,billing,technical,sales,email,dns,domain,hosting,other'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,urgent'],
            'description' => ['nullable', 'string'],
            'message' => ['required_without:description', 'nullable', 'string'],
            'related_service_type' => ['nullable', 'string', 'max:50'],
            'related_service_id' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'O assunto é obrigatório.',
            'subject.max' => 'O assunto não pode ter mais de 255 caracteres.',
            'category.required' => 'A categoria é obrigatória.',
            'category.in' => 'A categoria informada não é válida. Valores aceitos: support, billing, technical, sales, other.',
            'priority.in' => 'A prioridade informada não é válida. Valores aceitos: low, medium, high, urgent.',
            'description.string' => 'A descrição deve ser um texto.',
            'message.required_without' => 'A mensagem é obrigatória quando a descrição não é fornecida.',
            'message.string' => 'A mensagem deve ser um texto.',
            'related_service_type.max' => 'O tipo do serviço relacionado não pode ter mais de 50 caracteres.',
            'related_service_id.integer' => 'O ID do serviço relacionado deve ser um número inteiro.',
        ];
    }
}
