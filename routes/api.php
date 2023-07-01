<?php

use App\Http\Controllers\AnswersController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\PracticesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestExamsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//////  /api/v1/auth/login
/////   /api/v1/answers/submit


Route::prefix('/v1')->group(function () {


    //==================== Auth Routes
    Route::prefix('/auth')->group(function () {
        Route::post("/sign-up", [UsersController::class, "create"]);
        Route::post("/login", [UsersController::class, "login"]);
        Route::post('/forgot-password', [UsersController::class, 'forgetPassword']);


        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [UsersController::class, "getMyself"]);
            Route::delete("/logout", [UsersController::class, "logout"]);
        });
    });

    //====================


    //==================== Test Exams Routes
    Route::middleware('auth:sanctum')->prefix("test-exams")->group(function () {
        Route::get("/", [TestExamsController::class, "getAll"]);
        Route::get("/{test_exam_order}", [TestExamsController::class, "getTestExamDetails"]);
        Route::post("/", [TestExamsController::class, "create"]); //FIXME: this is a dev only route
    });
    //====================



    //==================== Answers Routes

    Route::middleware('auth:sanctum')->prefix("answers")->group(function () {
        Route::post("/submit", [AnswersController::class, "submit"]);
        Route::post("/standard-score", [AnswersController::class, "calcStandardScore"]);
        Route::post("/severity-of-autism", [AnswersController::class, "calcSeverityOfAutism"]);
        Route::get("/get-my-answers", [AnswersController::class, "getMyAnswers"]);
    });

    //====================

    //==================== practices Routes

    Route::middleware('auth:sanctum')->prefix("practice")->group(function () {
        Route::post("/", [PracticesController::class, "submitAnswer"]);
    });
    //====================


    //==================== Medical history Routes

    Route::middleware('auth:sanctum')->prefix("medical-history")->group(function () {
        Route::get("/", [MedicalHistoryController::class, "getMyMedicalHistory"]);
    });
    //====================


    //==================== Profile Routes

    Route::middleware('auth:sanctum')->prefix("profile")->group(function () {
        Route::put("/", [ProfileController::class, "update"]);
        Route::put("/child-image", [ProfileController::class, "uploadChildImage"]);
    });
});
