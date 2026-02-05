<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role_id === 1;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255|unique:courses',
            'code' => 'required|string|max:20|unique:courses',
            'duration_months' => 'required|integer|min:6|max:48',
            'description' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Please select a department',
            'name.required' => 'Course name is required',
            'duration_months.min' => 'Duration must be at least 6 months'
        ];
    }
}