<?php

namespace App\Http\Requests;

use App\Helpers\GetValues;
use App\Policies\MemberPolicy;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class NoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Use a política MemberPolicy para garantir que apenas usuários do tipo de usuário REPRESENTANTE LIGADOS AO GRUPO possam criar notas dentro do grupo.
        // Chame o método isAuthorized() da política MemberPolicy, passando a nota como parâmetro.
        // Retorne true se o usuário estiver autorizado a criar ou atualizar a nota, caso contrário false.
        return MemberPolicy::isAuthorized($this->note);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $method = request()->method;
        $isRequired = $method == 'POST' ? 'required':'sometimes';

        return [
            'title' => sprintf('%s|min:5|string', $isRequired),
            'description' => sprintf('%s|min:5|string', $isRequired),
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
            'title.required' => 'O campo título é obrigatório.',
            'title.string'   => 'O campo título deve ser uma string.',
            'title.min'      => 'O campo título deve ter no mínimo 5 caracteres.',
            'description.required' => 'O campo descrição é obrigatório.',
            'description.string'   => 'O campo descrição deve ser uma string.',
            'description.min'      => 'O campo descrição deve ter no mínimo 5 caracteres.',
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

        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
