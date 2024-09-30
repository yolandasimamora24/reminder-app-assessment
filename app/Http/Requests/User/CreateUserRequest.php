<?php

namespace App\Http\Requests\User;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\Helper;
use App\Models\User;
use App\Traits\CommonResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{

    use PasswordValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class),],
            'mobile_number' => ['nullable', 'regex:/^\+\d{1,3}\s?\d{1,14}$/'],
            'dob' => ['nullable', 'date', 'date_format:Y-m-d'],
            'gender' => ['nullable', 'string', 'in:' . collect(Helper::genders())->join(',')],
            'race' => ['nullable', 'string', 'in:' . collect(Helper::races())->join(',')],
            'ethnicity' => ['nullable', 'string', 'in:' . collect(Helper::ethnicities())->join(',')],
            'user_type' => ['required', 'in:' . collect(Helper::userTypes())->join(',')],
            //'password' => $this->passwordRules(),
            'avatar' => ['nullable', 'string', 'max:255'],
            'guest' => ['nullable', 'boolean']

        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}