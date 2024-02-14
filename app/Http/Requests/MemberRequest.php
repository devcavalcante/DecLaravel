<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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

        return array(
            '*.email'          => [
                $isRequired,
                'email',
                'distinct',
                Rule::unique('members', 'email')->ignore(request()->route('id')),
            ],
            '*.role'           => sprintf('%s|string', $isRequired),
            '*.phone'          => sprintf('%s|min:11|max:11|string', $isRequired),
            '*.entry_date'     => sprintf('%s|date_format:Y-m-d', $isRequired),
            '*.departure_date' => sprintf('%s|date_format:Y-m-d|after_or_equal:entry_date', $isRequired),
        );
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
