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
