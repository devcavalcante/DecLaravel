<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserControllerRequest extends FormRequest
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
        return [
            'name'         => 'required|string|min:4',
            'password'     => 'required|string|min:8',
            'email'        => 'required|email|string',
            'type_user_id' => 'required',
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
            'name.required'         => 'O campo nome é obrigatório.',
            'name.string'           => 'O campo nome deve ser uma string.',
            'name.min'              => 'O campo nome deve ter no mínimo 4 caracteres.',
            'password.required'     => 'O campo senha é obrigatório.',
            'password.string'       => 'O campo senha deve ser uma string.',
            'password.min'          => 'O campo senha deve ter no mínimo 8 caracteres.',
            'email.required'        => 'O campo e-mail é obrigatório.',
            'email.email'           => 'O campo e-mail deve ser um e-mail válido.',
            'email.string'          => 'O campo e-mail deve ser uma string.',
            'type_user_id.required' => 'O campo tipo de usuário é obrigatório.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
