<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;

class MedicalHistoryController extends Controller
{
    function getMyMedicalHistory(Request $req)
    {
        $currentUser = $req->user();

        // exams , practices & their answers , profile data

        $allTestExamsAndPracticesAnswers = Answer::where('user_id', $currentUser->id)
            ->with('testExam')
            ->latest()
            ->get();

        return [
            'exams_answers' => $allTestExamsAndPracticesAnswers->filter(function ($answer) {
                return $answer->type === 'exam';
            }),
            'practices_answers' => $allTestExamsAndPracticesAnswers->filter(function ($answer) {
                return $answer->type === 'practice';
            }),
            'profile' => $currentUser,
        ];
        // return $currentUser;
    }
}
