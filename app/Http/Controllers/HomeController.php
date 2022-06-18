<?php

namespace App\Http\Controllers;

use App\Models\TblReplyWords;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(Request $request){
        return redirect('https://atayebatgroup.com');
    }
}
