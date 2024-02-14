<?php

namespace App\Http\Requests;

use App\Helpers\GetValues;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

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
        $method = request()->method;
        $isRequired = $method == 'POST' ? 'required' : 'sometimes';
        return [
            'name'       => sprintf('%s|min:4|string', $isRequired),
            'email'      => sprintf('%s|email|string', $isRequired),
            'password'   => sprintf('%s|min:8|string', $isRequired),
            'c_password' => sprintf('%s|same:password|min:8|string', $isRequired),
            'file_url'   => [
                File::image()
                    ->min(1024) // Tamanho mínimo do arquivo em kilobytes (1MB)
                    ->max(12 * 1024) // Tamanho máximo do arquivo em kilobytes (12MB)
                    ->dimensions(Rule::dimensions()->maxWidth(1000)->maxHeight(500)), // Dimensões máximas da imagem
            ],
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
