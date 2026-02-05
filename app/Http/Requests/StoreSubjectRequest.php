<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role_id === 1;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:subjects',
            'code' => 'required|string|max:20|unique:subjects',
            'description' => 'nullable|string',
            'credit_hours' => 'required|integer|min:1|max:6',
            'type' => 'required|in:core,elective,practical',
            'is_active' => 'boolean'
        ];
    }
}