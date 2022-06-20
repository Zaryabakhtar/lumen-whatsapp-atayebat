<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use App\Library\Utilities;
use Illuminate\Http\Request;
use App\Models\TblReplyWords;
use App\Models\TblWAContacts;
use Illuminate\Support\Facades\DB;

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
        $preSetWordsList = [];
        // 29117122191833 Customer Contact Group
        $object = $request->object;
        $entry = $request->entry;
        $responseId = $entry[0]['id'];
        $changes = $entry[0]['changes'];
        $mainData = $changes[0]['value'];

        $metaData = $mainData['metadata'];
        $displayPhoneNumber = $metaData['display_phone_number'];
        $displayName = $mainData['contacts'][0]['profile']['name'];
        $phoneNumberId = $metaData['phone_number_id'];
        $messages = $mainData['messages'];

        // Pre Set Words
        // $wordsFromDb = TblReplyWords::get();

        // foreach ($wordsFromDb as $word) {
        //     array_push($preSetWordsList , strtolower($word->word_name));    
        // }

        foreach ($messages as $message) {
            if($message['type'] == 'text'){
                $from = $message['from'];
                $text = strtolower($message['text']['body']);
                $messageId = $message['id'];

                $customer = TblWAContacts::where('phone_no' , $from)->first();
                if(!isset($customer)){
                    DB::beginTransaction();
                        $customer = new TblWAContacts;
                        $customer->cnt_id = Utilities::uuid();
                        $customer->grp_id = 29117122191833; // Customer Group
                        $customer->cnt_name = $displayName;
                        $customer->is_verified = 1;
                        $customer->is_active = 1;
                        $customer->phone_no = $from;
                        $customer->country_id = 1;
                        $customer->business_id = 1;
                        $customer->company_id = 1;
                        $customer->branch_id = 1;
                        $customer->save();
                    DB::commit();
                }

                if($text == 'add me' || $text == 'اضافتي' || $text == 'اضافتى'){
                    
                    $this->sendWhatsAppTemplate('add_me' , $from , 'en_US');
                    $components = [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type"=> "IMAGE",
                                    "image"=> [
                                        "link"=> 'https://api.atayebatgroup.com/media/coupon.jpeg'
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type"=> "text",
                                    "text" => time()
                                ]
                            ]
                        ]

                    ];
                    $this->sendWhatsAppTemplate('promotion_on_first_add' , $from , 'en' , $components);
                    $this->markMessageRead($messageId);
                    return true;
                }
                if($text == 'location'){

                    return true;
                }
                $this->sendWhatsAppTemplate('send_selected_word' , $from , 'en_US');
                return false;
            }  
        }
    }

    private function sendWhatsAppTemplate($template , $to , $lang = 'en_US' , $params = []){
        if(count($params) > 0){
            $components = '"components" : ' . json_encode($params);
        }else{
            $components = '"components" : []';
        }
        
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://graph.facebook.com/v13.0/'. env('PHONE_NUMBER_ID') .'/messages',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "messaging_product": "whatsapp",
                    "to": "'.$to.'",
                    "type": "template",
                    "template": {
                        "name": "'.$template.'",
                        "language": {
                            "code": "'.$lang.'"
                        },
                        '.$components.'
                    },
                }',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('WHATSAPP_TOKEN'),
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
    }

    private function markMessageRead($id){
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v13.0/'. env('PHONE_NUMBER_ID') .'/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS =>'{
                "messaging_product": "whatsapp",
                "status": "read",
                "message_id": "'.$id.'"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . env('WHATSAPP_TOKEN'),
            ),
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            return  $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
