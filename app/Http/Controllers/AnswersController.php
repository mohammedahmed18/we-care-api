<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitAnswerRequest;
use App\Models\Answer;
use App\Models\TestExam;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class AnswersController extends Controller
{
    public $maxSpeakingExamsCount = 6;
    public $maxNonSpeakingExamsCount = 4;
    public $standardScore = [
        "1" => [
            // rules
            "1" => "0",
            "2" => "0",
            "3" => "0",
            "4" => "0",
            "5" => "0",
            "6" => "0",
        ],

        "2" => [
            "1" => "0",
            "2" => "0",
            "3" => "0,1",
            "4" => "0",
            "5" => "0",
            "6" => "0",
        ],
        "3" => [
            "1" => "0",
            "2" => "0",
            "3" => "2,4",
            "4" => "0,1",
            "5" => "0",
            "6" => "0"
        ],
        "4" => [
            "1" => "0,3",
            "2" => "1,4",
            "3" => "5,8",
            "4" => "2,4",
            "5" => "0",
            "6" => "0"
        ],
        "5" => [
            "1" => "4,6",
            "2" => "5,8",
            "3" => "9,11",
            "4" => "5,6",
            "5" => "0",
            "6" => "0"
        ],
        "6" => [
            "1" => "7,9",
            "2" => "9,12",
            "3" => "12,13",
            "4" => "7,8",
            "5" => "1",
            "6" => "1,2"
        ],
        "7" => [
            "1" => "10,13",
            "2" => "13,15",
            "3" => "14,16",
            "4" => "9,10",
            "5" => "2,3",
            "6" => "3,4"
        ],
        "8" => [
            "1" => "14,16",
            "2" => "16,19",
            "3" => "17,18",
            "4" => "11,12",
            "5" => "4,6",
            "6" => "5"
        ],
        "9" => [
            "1" => "17,19",
            "2" => "20,23",
            "3" => "19,21",
            "4" => "13,14",
            "5" => "7,8",
            "6" => "6,7"
        ],
        "10" => [
            "1" => "20,22",
            "2" => "24,27",
            "3" => "22,23",
            "4" => "15,16",
            "5" => "9,10",
            "6" => "8,9"
        ],
        "11" => [
            "1" => "23,26",
            "2" => "28,30",
            "3" => "24,25",
            "4" => "17,18",
            "5" => "11,13",
            "6" => "10,11"
        ],
        "12" => [
            "1" => "27,29",
            "2" => "31,34",
            "3" => "26,27",
            "4" => "19,20",
            "5" => "14,15",
            "6" => "12,13"
        ],
        "13" => [
            "1" => "30,32",
            "2" => "35,38",
            "3" => "0",
            "4" => "21,22",
            "5" => "16,17",
            "6" => "14,15"
        ],
        "14" => [
            "1" => "33,36",
            "2" => "39,42",
            "3" => "0",
            "4" => "23,24",
            "5" => "18,19",
            "6" => "16"
        ],
        "15" => [
            "1" => "37,39",
            "2" => "0",
            "3" => "0",
            "4" => "0",
            "5" => "20,21",
            "6" => "17,18"
        ],
        "16" => [
            "1" => "0",
            "2" => "0",
            "3" => "0",
            "4" => "0",
            "5" => "0",
            "6" => "19,20"
        ],
        "17" => [
            "1" => "0",
            "2" => "0",
            "3" => "0",
            "4" => "0",
            "5" => "0",
            "6" => "21"
        ],


    ];

    public $severityOfAutismMap = [
        140 => [87, 0],
        139 => [86, 0],
    ];
    function submit(SubmitAnswerRequest $request)
    {

        $testExamId = $request->input("test_exam_id");
        $userId = $request->user()->id;

        //check if the user answer this exam before or not

        // $answer = Answer::where("test_exam_id", $testExamId)->where("user_id", $userId)->first();
        // if ($answer) {
        //     return Response::json([
        //         "success" => "false",
        //         "message" => "you answered this before"
        //     ], 401);
        // }

        // make sure the exam is in the db
        $exam = TestExam::find($testExamId);

        if (!$exam)
            return Response::json([
                "success" => "false",
                "message" => "no exam found"
            ], 404);

        $score = 0;
        $answers_map = $request->input("answers_map");



        foreach ($answers_map as $qId => $qV) {
            $score += $qV;
        }


        return Answer::create([
            "user_id" => $userId,
            "test_exam_id" => $testExamId,
            "answers_map" => json_encode($answers_map),
            "score" => $score
        ]);
    }



    public function calcStandardScore(Request $request)
    {
        $allCurrentUserAnswers = $this->getMyAnswers($request);

        $answeredExamsCount = count($allCurrentUserAnswers);
        if (
            $answeredExamsCount !== $this->maxSpeakingExamsCount &&
            $answeredExamsCount !== $this->maxNonSpeakingExamsCount
        ) {
            return response()->json([
                'error' =>
                "you only answered "
                    . $answeredExamsCount
            ], 400);
        };

        foreach ($this->standardScore as $standardScoreNumber => $rules) {

            // check if the user answers match the current standard score rules
            $isMatch = true;

            foreach ($allCurrentUserAnswers as $answer) {
                $answerOrder = $answer->testExam()->first()->order;
                $matchingRule = $rules[$answerOrder];
                // if($matchingRule->)
                if (strpos($matchingRule, ",") !== false) {
                    $range = explode(",", $matchingRule);
                    $min = $range[0];
                    $max = $range[1];

                    // this is a range score
                    if ($answer->score < $min || $answer->score > $max) {
                        $isMatch = false;
                        break;
                    }
                } else {
                    // this is an exact score
                    if ($answer->score != $matchingRule) {
                        $isMatch = false;
                        break;
                    }
                }
            }


            if ($isMatch) {
                // one of the cases matched
                return response()->json(['standard_score' => $standardScoreNumber], 200);
            };
        }


        // no case matched
        return response()->json(['error' => 'no case matched'], 400);
    }


    public function calcSeverityOfAutism(Request $request)
    {
        $answers = $this->getMyAnswers($request);

        $totalScore = 0;

        foreach ($answers as $a) {
            $totalScore += $a->score;
        }


        $speaking = true;

        // $answeredTestExams = count($answers);
        // if ($answeredTestExams == $this->maxSpeakingExamsCount) {
        // speaking
        //     $speaking = true;
        // } else if ($answeredTestExams == $this->maxNonSpeakingExamsCount) {
        // non-speaking
        //     $speaking = false;
        // } else {

        //     return response()->json([
        //         'error' =>
        //         "you only answered "
        //             . $answeredTestExams
        //     ], 400);
        // }


        foreach ($this->severityOfAutismMap as $severity => [$speakingValue, $nonSpeakingValue]) {

            $valueToCheck = $speaking ? $speakingValue : $nonSpeakingValue;

            if ($totalScore == $valueToCheck) {
                return response()->json(['severity_of_autism' => $severity], 200);
            }
        }

        // no case matched
        return response()->json(['error' => 'no case matched'], 400);
    }


    public function getMyAnswers(Request $request)
    {
        $userId = $request->user()->id;
        return Answer::where("user_id", $userId)->with('testExam')->get();
    }
}
