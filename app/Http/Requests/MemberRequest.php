<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MemberRequest extends FormRequest
{


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $method = request()->method;
        $isRequired = $method == 'POST' ? 'required' : 'sometimes';
        $isForbidden = $method !== 'POST' ? 'prohibited' : 'required';

        return array(
            '*.role'           => sprintf('%s|string', $isRequired),
            '*.phone'          => sprintf('%s|min:11|max:11|string', $isRequired),
            '*.entry_date'     => sprintf('%s|date', $isRequired),
            '*.departure_date' => sprintf('%s|date|after_or_equal:entry_date', $isRequired),
            '*.user_id'        => sprintf('%s|string|exists:App\Models\User,id', $isForbidden),
        );
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.required'                 => 'O campo papel é obrigatório.',
            'role.string'                   => 'O campo papel deve ser uma string.',
            'phone.required'                => 'O campo telefone é obrigatório.',
            'phone.string'                  => 'O campo telefone deve ser uma string.',
            'phone.min'                     => 'O campo telefone deve ter 11 caracteres.',
            'phone.max'                     => 'O campo telefone deve ter 11 caracteres.',
            'departure_date.date'           => 'O campo data de saida deve ser uma data válida.',
            'departure_date.required'       => 'O campo de saida deve ser obrigatório',
            'entry_date.date'               => 'O campo data de entrada deve ser uma data válida.',
            'entry_date.required'           => 'O campo data de entrada deve ser obrigatório.',
            'user_id.required'              => 'O campo user_id é obrigatório.',
            'user_id.string'                => 'O campo user_id deve ser uma string.',
            'user_id.exists'                => 'O campo user_id não existe na tabela de usuários.',
            'departure_date.after_or_equal' => 'A data de partida deve ser uma data posterior ou igual à data de entrada.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
