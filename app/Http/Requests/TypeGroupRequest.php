<?php

namespace App\Http\Requests;

use App\Helpers\GetKeys;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class TypeGroupRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $method = request()->method;
        $isRequired = $method == 'POST' ? 'required':'sometimes';

        return [
            'name'         => sprintf('%s|min:4|string', $isRequired),
            'type_group' => [
                sprintf('%s|min:4|string', $isRequired),
                Rule::in(GetKeys::listOfKeysTypeGroupEnum())
            ]
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
            'name.required' => 'O campo nome é obrigatório.',
            'name.string'   => 'O campo nome deve ser uma string.',
            'name.min'      => 'O campo nome deve ter no mínimo 4 caracteres.',
            'type_group.required' => 'O campo tipo de grupo é obrigatório.',
            'type_group.string'   => 'O campo tipo de grupo deve ser uma string.',
            'type_group.in'   => 'O campo tipo de grupo deve ser interno ou externo',
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
