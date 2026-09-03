<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_user_id' => ['required', 'exists:users,id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_user_id.required' => 'O usuário de destino é obrigatório.',
            'to_user_id.exists' => 'O usuário de destino não existe.',
            'reason.max' => 'O motivo não pode ter mais de 500 caracteres.',
        ];
    }
}
