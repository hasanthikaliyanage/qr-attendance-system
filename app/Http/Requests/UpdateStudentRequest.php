<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($studentId->user_id)
            ],
            'nic' => [
                'required',
                'string',
                Rule::unique('users', 'nic')->ignore($studentId->user_id)
            ],
            'address' => 'required|string',
            'phone' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
            'student_id' => [
                'required',
                Rule::unique('students', 'student_id')->ignore($studentId)
            ],
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact' => 'nullable|string',
            'enrollment_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,graduated,suspended',
        ];
    }
}