<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GroupRequest extends FormRequest
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
        return [
            'entity' => 'string',
            'organ' => 'string',
            'council' => 'string',
            'acronym' => 'string|min:2',
            'team' => 'string',
            'unit' => 'string',
            'email' => 'string|email',
            'office_requested' => 'string',
            'office_indicated' => 'string',
            'internal_concierge' => 'string',
            'observations' => 'string|min:5',
            'creator_user_id' => 'exists:users,id',
            'representatives' => 'array|exists:users,id'
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'entity.string' => 'O campo de entidade deve ser uma string.',
            'organ.string' => 'O campo orgão deve ser uma string.',
            'council.string' => 'O campo de conselho deve ser uma string.',
            'acronym.string' => 'O campo da sigla deve ser uma string.',
            'acronym.min' => 'O campo da sigla deve ter no mínimo 4 caracteres.',
            'team.string' => 'O campo do time deve ser uma string.',
            'unit.string' => 'O campo da unidade deve ser uma string.',
            'email.string' => 'O campo do email deve ser uma string.',
            'email.email' => 'Email invalido.',
            'office_requested.string' => 'O campo do oficio solicitado deve ser uma string.',
            'office_indicated.string' => 'O campo do oficio indicado deve ser uma string.',
            'internal_concierge.string' => 'O campo da portaria interna deve ser uma string.',
            'observations.string' => 'O campo de observacoes deve ser uma string.',
            'observations.min' => 'O campo de observacoes deve ter no mínimo 5 caracteres.',
            'representatives.array' => 'O campo de representantes deve ser um array.',
            'creator_user_id.exists' => 'O campo de criador de usuario deve existir na base de dados.',
            'representatives.exists' => 'O campo de representantes deve existir na base de dados.',
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


