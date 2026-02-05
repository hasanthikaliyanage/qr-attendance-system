<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQRSessionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lecturer_id' => ['nullable', 'exists:lecturers,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'session_title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'session_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_global_one_time' => ['boolean'],
        ];
    }

    public function messages()
    {
        return [
            'session_date.after_or_equal' => 'Session date cannot be in the past.',
            'end_time.after' => 'End time must be after start time.',
        ];
    }
}