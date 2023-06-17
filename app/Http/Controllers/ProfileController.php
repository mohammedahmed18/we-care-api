<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UploadImageRequest;

class ProfileController extends Controller
{
    function update(UpdateProfileRequest $request){
        $request->user()->update($request->all());

        return $request->user();
    }

    public function uploadChildImage(UploadImageRequest $request)
    {

        // Get the base64-encoded image from the request
        $base64Image = $request->input('image');


        $base64Image = str_replace('data:image/jpg'.';base64,', "", $base64Image);
        $base64Image = str_replace('data:image/png'.';base64,', "", $base64Image);
        $base64Image = str_replace('data:image/jpeg'.';base64,', "", $base64Image);
        $base64Image = str_replace(' ', '+', $base64Image);

        // Decode the base64 image
        $decodedImage = base64_decode($base64Image);

        // Generate a unique filename for the image
        $filename = $request->user()->id . '.jpg';

        $filePath = public_path('children-images/' . $filename);

        // Store the image on the server
        // Storage::disk('public')->put($filename, $decodedImage);

        // public_path('uploads')->file_put_contents($filename , $decodedImage);
        file_put_contents($filePath, $decodedImage);


        $url = asset('children-images/' . $filename);

        // update the child image
        $request->user()->update([
            'child_image_src' => $url
        ]);

        return response()->json(['url' => $url]);
    }
}
