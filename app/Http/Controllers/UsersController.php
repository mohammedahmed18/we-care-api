<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Mail\ForgotPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class UsersController extends Controller
{

    function getMyself(Request $request)
    {
        return $request->user();
    }

    //
    function create(CreateUserRequest $request)
    {
        $apiToken = Str::random(60);
        return User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'api_token' => $apiToken,
        ]);
    }


    function login(LoginRequest $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        if (!$user)
            return Response::json([
                "success" => "false",
                "message" => "no user found"
            ], 404);

        //check the password

        $valid = Hash::check($request->input("password"), $user->password);
        if (!$valid) {
            return Response::json([
                "success" => "false",
                "message" => "password is not correct"
            ], 401);
        }

        $token = $user->createToken("api_token");

        return ['accessToken' => $token->plainTextToken];
    }

    function logout(Request $request)
    {
        // this will revoke all of the user tokens
        $request->user()->tokens()->delete();
        return true;
    }


    function forgetPassword(ForgetPasswordRequest $request)
    {

        $token = Str::random(64);

        $toEmail = $request->email;

        DB::table('password_reset_tokens')->insert([
            'email' => $toEmail,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::to($toEmail)->send(new ForgotPasswordRequest($token));

        return response()->json(["success" => true, "msg" => 'We have e-mailed your password reset link!'], 201);
    }


    // Browser not api
    function verifyForgotPasswordToken(Request $request, $token)
    {
        $resetToken = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (isset($resetToken)) {
            $request->session()->put(['email' => $resetToken->email]);
            $request->session()->put(['token' => $resetToken->token]);

            //if token is valid, redirect to reset form
            return view("passwordResetForm", compact('resetToken'));
        }
        return "no request found";
    }

    function performResetPassword(Request $request)
    {

        $newDetails = $request->all();
        $newDetails["email"] = session('email');
        $token = session('token');
        $validator = Validator::make($newDetails, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $user = User::where('email', $newDetails['email'])->first();
            $user['password'] = bcrypt($newDetails['password']);
            $user->save();
            DB::table('password_reset_tokens')->where('token', $token)->delete();
            return view("passwordChangedSuccessfully");
        }
    }
}
