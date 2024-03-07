<?php

namespace App\Http\Requests;

use App\Helpers\GetValues;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class GroupRequest extends FormRequest
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
        $isRequired = $method == 'POST' ? 'required':'sometimes';

        return [
            'entity'             => 'string',
            'organ'              => 'string',
            'council'            => 'string',
            'acronym'            => 'string|min:2',
            'team'               => 'string',
            'unit'               => 'string',
            'email'              => 'string|email',
            'office_requested'   => 'string',
            'office_indicated'   => 'string',
            'internal_concierge' => 'string',
            'observations'       => 'string|min:5',
            'status'             => sprintf('%s|string|in:EM ANDAMENTO,FINALIZADO', $isRequired),
            'creator_user_id'    => 'exists:users,id',
            'representative'     => sprintf('%s|string|email', $isRequired),
            'representative.name'=> sprintf('%s|min:4|string', $isRequired),
            'name'               => sprintf('%s|min:4|string', $isRequired),
            'type_group'         => [$isRequired, 'string', 'min:4', Rule::in(GetValues::listOfValuesTypeGroupEnum())],
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
