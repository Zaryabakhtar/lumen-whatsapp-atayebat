<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $mode = $request->hub_mode;
        $token = $request->hub_verify_token;
        $challnge = (int)$request->hub_challenge;

        if(isset($mode) && isset($token)){
            if ($mode === "subscribe" && $token === env('VERIFY_TOKEN')) {
                return response()->json($challnge , 200);
            }else{
                return response()->json(NULL , 403);
            }
        }
    }

    public function handleWebhook(Request $request){
        $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $request);
        fclose($myfile);
    }
}
