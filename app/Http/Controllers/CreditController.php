<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function purchaseCredit(){
        return view('credits.purchase');
    }
}
