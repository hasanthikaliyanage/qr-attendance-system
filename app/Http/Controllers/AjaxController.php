<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Lecturer;

// app/Http/Controllers/AjaxController.php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subject;

class AjaxController extends Controller
{
    public function courses($departmentId) {
        return Course::where('department_id', $departmentId)->get();
    }

    public function subjects($courseId) {
        return Subject::whereHas('courses', fn($q)=> $q->where('course_id',$courseId))->get();
    }
}
