<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ns1' => ['required', 'string', 'max:255'],
            'ns2' => ['required', 'string', 'max:255'],
            'ns3' => ['nullable', 'string', 'max:255'],
            'ns4' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ns1.required' => 'O primeiro nameserver é obrigatório.',
            'ns1.max' => 'O nameserver não pode ter mais de 255 caracteres.',
            'ns2.required' => 'O segundo nameserver é obrigatório.',
            'ns2.max' => 'O nameserver não pode ter mais de 255 caracteres.',
            'ns3.max' => 'O nameserver não pode ter mais de 255 caracteres.',
            'ns4.max' => 'O nameserver não pode ter mais de 255 caracteres.',
        ];
    }
}
