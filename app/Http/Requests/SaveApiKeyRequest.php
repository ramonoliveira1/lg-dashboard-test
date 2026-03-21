<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveApiKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'api_key' => ['required', 'string', 'min:10'],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'api_key.required' => 'Informe a API Key do Gemini.',
            'api_key.min'      => 'A API Key parece ser muito curta.',
        ];
    }
}

