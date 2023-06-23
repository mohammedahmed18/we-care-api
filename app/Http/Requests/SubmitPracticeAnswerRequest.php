<?php

namespace App\Http\Requests;


class SubmitPracticeAnswerRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return  [
            'order' => 'numeric|required',
            'answers' => 'required|array',
            'answers.*.response' => 'required|boolean',
            'answers.*.startDate' => 'required|date',
            'answers.*.masteryDate' => 'required|date',
        ];
    }
    // public function messages()
    // {
    //     return [
    //         'name.required' => "name is required",
    //         'email.required' => "email is required",
    //         'email.unique' => "sorry this email is already registered",
    //         'password.required' => "password is required",
    //         // "password.min:6" => "The password must be at least 6 characters",
    //         // 'password.max:30' => "password can't be longer than 30 characters",
    //     ];
    // }
}
