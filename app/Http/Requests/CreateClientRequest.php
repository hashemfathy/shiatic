<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:clients,name',
            'gender' => 'required',
            'phone' => 'required|unique:clients,phone',
            'code' => 'required|unique:clients,code',
            'blood' => 'nullable',
            'age' => 'nullable',
            'weight' => 'nullable',
        ];
    }
}
