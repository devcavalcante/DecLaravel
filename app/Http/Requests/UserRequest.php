<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class UserRequest extends FormRequest
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
        return[
            'name' => 'required|min:4|string',
            'email' => 'required|email|string',
            'password' => 'required|min:8|string',
            'c_password' => 'required|same:password|min:8|string'
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
            'email.email'   => 'Email invalido.',
            'email.required'   => 'O campo e-mail é obrigatório.',
            'email.string'   => 'O campo email deve ser uma string.',
            'password.required' => 'O campo password é obrigatório.',
            'password.min' => 'O campo password deve ter no mínimo 8 caracteres.',
            'password.string'   => 'O campo password deve ser uma string.',
            'c_password.min' => 'O campo password deve ter no mínimo 8 caracteres.',
            'c_password.required' => 'O campo c_password é obrigatório.',
            'c_password.same' => 'O campo c_password deve ser igual ao campo password.',
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
