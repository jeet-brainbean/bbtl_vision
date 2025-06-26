<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\detectionResult;
use App\Models\UserWiseAgeSetting;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $total_uploads = detectionResult::where('user_id',Auth()->user()->id)->get()->count();

        $total_consumed =  detectionResult::where('user_id',Auth()->user()->id)->get()->sum('credited');
        return view('home',with(compact('total_uploads','total_consumed')));
    }

   public function settings()
    {
        $userId = Auth()->user()->id;
        $age_setting = UserWiseAgeSetting::where('user_id', $userId)->first();

        // If no settings exist for the user, provide default values
        if (!$age_setting) {
            $age_setting = (object) [
                'success_min_age' => 0,
                'success_max_age' => 0,
                'error_min_age' => 0,
                'error_max_age' => 0,
            ];
        }

        return view('settings', compact('age_setting')); // Replace 'your.view.path.age-settings' with the actual path to your blade view
    }

    /**
     * Handles the update of age settings via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'success_min_age' => 'required|integer|min:0',
            'success_max_age' => 'required|integer|min:0',
            'error_min_age' => 'required|integer|min:0',
            'error_max_age' => 'required|integer|min:0',
        ]);

        $user_id = auth()->user()->id;

        UserWiseAgeSetting::updateOrCreate(
            ['user_id' => $user_id],
            [
                'success_min_age' => $request->success_min_age,
                'success_max_age' => $request->success_max_age,
                'error_min_age' => $request->error_min_age,
                'error_max_age' => $request->error_max_age,
            ]
        );

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }


}