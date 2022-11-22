<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GoogleAuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'role' => ['required', 'string', Rule::in(['consumer', 'driver', 'rider', 'merchant', 'superadmin', 'subadmin'])],
            'application_name' => ['required', 'string', Rule::in(['flip', 'cue', 'cueDriver', 'admin'])],
        ];
    }

    public function messages()
    {
        return [
            'role.required' => 'The role field is required',
            'role.in' => "Role name does not exist",
            'application_name.required' => 'The application name is required',
            'application_name.in' => "The application name does not exist"
        ];
    }
}
