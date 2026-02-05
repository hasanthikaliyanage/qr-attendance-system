<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role_id === 1;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments')->ignore($this->department)
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('departments')->ignore($this->department)
            ],
            'description' => 'nullable|string'
        ];
    }
}