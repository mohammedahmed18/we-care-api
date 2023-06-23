<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitPracticeAnswerRequest;
use App\Models\Answer;
use App\Models\TestExam;
use Illuminate\Support\Facades\Response;


class PracticesController extends Controller
{

    function submitAnswer(SubmitPracticeAnswerRequest $request)
    {
        $answers = $request->input("answers");
        $order = $request->input("order");
        $userId = $request->user()->id;

        $practice = TestExam::where([
            ['type', 'practice'],
            ['order', $order],
        ])->first();

        if (! $practice)
            return Response::json([
                "success" => "false",
                "message" => "no practice found"
            ], 404);

        return Answer::create([
            "user_id" => $userId,
            "test_exam_id" => $practice->id,
            "answers_map" => json_encode($answers),
            'type' => 'practice'
        ]);
    }
}
