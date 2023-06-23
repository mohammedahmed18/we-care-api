<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MedicalHistoryController extends Controller
{
    //

    function getMyMedicalHistory(Request $req)
    {
        $currentUser = $req->user();

        // exams , practices & their answers , profile data
        return $currentUser;
    }
}
