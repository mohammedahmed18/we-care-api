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
        137 => [85, 0],
        136 => [84, 0],
        134 => [83, 0],
        133 => [82, 0],
        131 => [81, 0],
        130 => [80, 0],
        128 => [79, 0],
        127 => [78, 0],
        126 => [0, 55],
        125 => [77, 54],
        124 => [76, 0],
        123 => [0, 53],
        122 => [75, 0],
        121 => [74, 52],
        120 => [73, 0],
        119 => [0, 51],
        118 => [72, 50],
        117 => [71, 0],
        116 => [0, 49],
        115 => [70, 0],
        114 => [69, 48],
        112 => [68, 47],
        111 => [67, 46],
        109 => [66, 45],
        108 => [65, 0],
        107 => [0, 44],
        106 => [64, 43],
        105 => [63, 0],
        104 => [0, 42],
        103 => [62, 0],
        102 => [61, 41],
        100 => [60, 40],
        99  => [59, 39],
        97  => [58, 38],
        96  => [57, 0],
        95  => [0, 37],
        94  => [56, 0],
        93  => [55, 36],
        92  => [54, 35],
        90  => [53, 34],
        89  => [52, 0],
        88  => [0, 33],
        87  => [51, 0],
        86  => [50, 32],
        85  => [0, 31],
        84  => [49, 0],
        83  => [48, 30],
        81  => [47, 29],
        80  => [46, 0],
        79  => [0, 28],
        78  => [45, 27],
        77  => [44, 0],
        76  => [0, 26],
        75  => [43, 0],
        74  => [42, 25],
        73  => [0, 24],
        72  => [41, 0],
        71  => [40, 23],
        69  => [39, 22],
        68  => [38, 0],
        67  => [0, 21],
        66  => [37, 20],
        65  => [36, 0],
        64  => [0, 19],
        63  => [35, 0],
        62  => [34, 18],
        61  => [33, 0],
        60  => [0, 17],
        59  => [32, 16],
        58  => [31, 0],
        57  => [0, 15],
        56  => [30, 0],
        55  => [29, 14],
        53  => [28, 13],
        52  => [27, 12],
        50  => [26, 12],
        49  => [25, 0],
        47  => [24, 0],
        46  => [23, 0],
        44  => [22, 0],
        43  => [21, 0]
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
        return Answer::where([
            ["user_id", $userId],
            ["type", 'exam'],
        ])->with('testExam')->get();
    }
}
