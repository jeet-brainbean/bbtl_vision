<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\detectionResult;
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
}
