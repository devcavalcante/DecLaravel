<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ActivityRequest extends FormRequest
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
            'description' => sprintf('%s|string|min:5', $isRequired),
            'name'        => sprintf('%s|string|min:5', $isRequired),
            'start_date'  => sprintf('%s|required_with:end_date|date_format:Y-m-d', $isRequired),
            'end_date'    => sprintf(
                '%s|required_with:start_date|after_or_equal:start_date|date_format:Y-m-d',
                $isRequired
            ),
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
            'name.string'              => 'O campo de nome deve ser uma string.',
            'name.required'            => 'O campo de nome é obrigatório.',
            'description.string'       => 'O campo de descrição deve ser uma string.',
            'description.required'     => 'O campo de descrição é obrigatório.',
            'start_date.date_format'   => 'O campo data de inicio deve ser no formato Y-m-d.',
            'end_date.date_format'     => 'O campo data final deve ser no formato Y-m-d.',
            'start_date.required_with' => 'O campo data final deve estar presente.',
            'end_data.required_with'   => 'O campo data inicial deve estar presente.',
            'start_date.required'      => 'O campo de data inicial é obrigatório.',
            'end_date.required'        => 'O campo de data final é obrigatório.',
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
