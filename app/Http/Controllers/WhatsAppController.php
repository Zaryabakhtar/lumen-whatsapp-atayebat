<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsAppController extends Controller
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
        die("This is Atayebat Hypermarket Group.");
    }

    public function handleWebhook(Request $request){
        $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $request);
        fclose($myfile);
    }
}
