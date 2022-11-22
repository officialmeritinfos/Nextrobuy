<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankRequest extends FormRequest
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
            'bank_id' =>  ['required', 'integer', 'exists:banks,id'],
            'account_name' =>  ['required', 'string', 'max:255'],
            'account_number' =>  ['required', 'numeric', 'digits:10'],
            'bvn_number' =>  ['required', 'numeric', 'digits:11'],
        ];
    }
}
