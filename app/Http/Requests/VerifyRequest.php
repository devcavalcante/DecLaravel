<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyRequest extends FormRequest
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
            'token'      => 'required',
            'email'      => 'required|email|string',
            'password'   => 'required|min:8|string',
            'c_password' => 'required|same:password|min:8|string',
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
            'token.required'      => 'O campo token é obrigatório.',
            'email.email'         => 'Email invalido.',
            'email.required'      => 'O campo e-mail e obrigatório.',
            'email.string'        => 'O campo email deve ser uma string.',
            'password.required'   => 'O campo password e obrigatório.',
            'password.min'        => 'O campo password deve ter no mínimo 8 caracteres.',
            'password.string'     => 'O campo password deve ser uma string.',
            'c_password.min'      => 'O campo password deve ter no mínimo 8 caracteres.',
            'c_password.required' => 'O campo c_password e obrigatório.',
            'c_password.same'     => 'O campo c_password deve ser igual ao campo password.',
            'c_password.string'   => 'O campo c_password deve ser uma string.',
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
