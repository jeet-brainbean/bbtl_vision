<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Rekognition\RekognitionClient;
use Storage;
use App\Models\detectionResult;
use App\Models\User;
use App\Models\Setting;
use App\Models\UserWiseAgeSetting;
use File;

class FaceDetectionController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function uploadImage(){
        $credit_require_per_image = Setting::where('key','credit_require_per_image')->value('value');
        $user_id = Auth()->user()->id;
        $age_setting = UserWiseAgeSetting::where('user_id', $user_id)->first();
        return view('face-detection.upload-image.index',with(compact('credit_require_per_image', 'age_setting')));
    }

    public function postUploadImage(Request $request){
        if(Auth()->user()->credits<=0){
            return response()->json(['error' => 'Credit not sufficient ' ], 500);
        }
       // Validate and upload image
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        // Get image from request
        $image = $request->file('photo');

        //Detect face function
        $result= $this->detectFaceDetails($image);
        if( is_array($result) && array_key_exists("error", $result)){
            return response()->json(['error' => $result['error']], 500);
        } else{
            //upload image to folder
            $uploadPath = public_path('uploads');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if ($request->file('photo')) {
                $imageName = time() . '.' . $request->photo->extension();
                $request->photo->move($uploadPath, $imageName);
            }

            //Redeem credits
            $credit_require_per_image = Setting::where('key','credit_require_per_image')->value('value');
            $new_credits = Auth()->user()->credits -  $credit_require_per_image;
            $user = User::where('id',Auth()->user()->id)->update(['credits'=>$new_credits]);

            //Store detection result
            detectionResult::create([
                'user_id' => auth()->user()->id ,
                'photo' => $imageName,
                'result' =>json_encode($result),
                'credited'=> $credit_require_per_image
                ]);
            return $result;
        }
    }

    public function liveCapture(Request $request){
        $user_id = Auth()->user()->id;
        $age_setting = UserWiseAgeSetting::where('user_id', $user_id)->first();
        return view('face-detection.live-capture.index' ,with(compact('age_setting')));
    }

    public function proceedLiveCapture(Request $request){
         if(Auth()->user()->credits<=0){
            return response()->json(['error' => 'Credit not sufficient ' ], 500);
        }
        $image = $request->image;
        $result = $this->detectFaceDetails($image);
        if( is_array($result) && array_key_exists("error", $result)){
            return response()->json(['error' => $result['error']], 500);
        } else{
            $folderPath = "uploads/";
            $image_parts = explode(";base64,", $image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $imageData = base64_decode($image_parts[1]);

            $fileName = time() . '.png';
            $file = $folderPath . $fileName;

            Storage::put($file, $imageData);
            $imageurl = Storage::url($file);

            // File::move(storage_path('/app/'.$file), public_path('uploads/'.$fileName));


            //Redeem credits
            $credit_require_per_image = Setting::where('key','credit_require_per_image')->value('value');
            $new_credits = Auth()->user()->credits -  $credit_require_per_image ;
            $user = User::where('id',Auth()->user()->id)->update(['credits'=>$new_credits]);

            //Store detection result
            detectionResult::create([
                'user_id' => auth()->user()->id ,
                'photo' => $imageurl,
                'result' =>json_encode($result),
                'credited'=> $credit_require_per_image
                ]);
            return $result;
        }
    }

    public function detectFaceDetails($image){
          // Convert the image to base64
        $imageData = file_get_contents($image);
        $base64Image = base64_encode($imageData);
        // Call Rekognition service for face detection
        $rekognition = new RekognitionClient([
            'region' => 'us-east-1',
            'version' => 'latest',
        ]);

        try {
            $result = $rekognition->detectFaces([
                'Image' => [
                    'Bytes' => base64_decode($base64Image),  // Send image as raw binary data
                ],
                'Attributes' =>['ALL']
                //  'MinConfidence' => 50
            ]);

            // Process the response
            $faces = $result->get('FaceDetails');


            // Store or process face details as needed
            return response()->json(['faces' => $faces]);

        } catch (\Exception $e) {
            return array('error' => 'Face detection failed: ' . $e->getMessage());
        }
    }

}