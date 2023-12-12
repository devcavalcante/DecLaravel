<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MeetingRequest extends FormRequest
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
        $method = $this->request->get('method');
        $isRequired = $method == 'POST' ? 'required' : 'sometimes';
        return [
            'content'   => sprintf('%s|min:5|string', $isRequired),
            'summary'   => sprintf('%s|min:5|string', $isRequired),
            'ata'        => sprintf(
                '%s|mimes:xml,pdf,docx,doc,zip',
                $isRequired
            ),
            'date_meet' => sprintf('%s|date', $isRequired),
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
            'content.required'   => 'O campo content é obrigatório.',
            'content.string'     => 'O campo content deve ser uma string.',
            'content.min'        => 'O campo content deve ter no mínimo 5 caracteres.',
            'summary.required'   => 'O campo summary é obrigatório.',
            'summary.string'     => 'O campo summary deve ser uma string.',
            'summary.min'        => 'O campo summary deve ter no mínimo 5 caracteres.',
            'ata.required'       => 'O campo ata é obrigatório.',
            'ata.string'         => 'O campo ata deve ser uma string.',
            'ata.min'            => 'O campo ata deve ter no mínimo 5 caracteres.',
            'date_meet.date'     => 'O campo data da reunião deve ser uma data válida.',
            'date_meet.required' => 'O campo data da reunião deve ser obrigatório.',
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
