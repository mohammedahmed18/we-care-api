<?php

namespace App\Http\Requests;


class UploadImageRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return  [
            'image' => 'required|string',
        ];
    }
}
