<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLecturerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nic' => 'required|string|unique:users,nic',
            'address' => 'required|string',
            'phone' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'employee_id' => 'required|unique:lecturers,employee_id',
            'qualification' => 'nullable|string',
            'specialization' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'employment_type' => 'required|in:full-time,part-time,visiting',
            'joined_date' => 'nullable|date',
        ];
    }
}