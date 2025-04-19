<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Charge;
use App\Models\Chat;
use App\Models\Ichancy;
use App\Models\IchTransaction;
use App\Models\Withdraw;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\RequestOptions;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttpClient;
use Illuminate\Support\Str;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{

    public function start(Request $request)
    {
        $form = Validator::make($request->all(), [
            "id"=>"required",
            "username"=>"required",
            "first_name"=>"required",
            "last_name"=>"nullable",
        ],[
            'required'=>'الحقل :attribute مطلوب.'
        ]);
        if ($form->fails()) {
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status"=>"validator","errorMessages"=>$errorMessages]);
        }
        $form = $form->validated();
        $chat = Chat::find($form['id']);
        if($chat===null){
            $chat = Chat::create($form);
        }
        return response()->json(["status"=>"success","chat"=>$chat]);
    }


    public function chargeBemo(Request $request)
    {
        $form = Validator::make($request->all(),[
                "amount"=>"required|numeric",
                "processid"=>"required|numeric",
                "chat_id"=>"required",
                "method"=>"required"
        ],[
            "numeric"=>"الرجاء إدخال قيم صحيحة"
        ]);
        if($form->fails()){
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status"=>"validator","message"=>$errorMessages]);
        }
        $form =$form->validate();
        $checkCharge = Charge::where("processid",$form['processid'])->first();
        if($checkCharge){
            if($checkCharge['status']=='complete'){
                return response()->json(["status"=>"failed","message"=>"عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            }else{
                return response()->json(["status"=>"failed","message"=>"عملية التحويل موجودة مسبقاً، الرجاء انتظار التنفيذ أو التواصل مع الدعم في حال التأخير"]);
            }
        }
        $charge = Charge::create($form);
        if($charge){
            $inlineKeyboard = [
                [
                    ['text' => '✅ تنفيذ', 'callback_data' => 'ex_bemo_charge:'.$charge->id],
                    ['text' => '▶️ متابعة', 'callback_data' => 'pending_bemo_charge:'.$charge->chat->id],
                    ['text' => '❌ رفض', 'callback_data' => 'reject_bemo_charge:'.$charge->chat->id.':'.$charge->id],
                ]
            ];
            $keyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
            $subscribers = [842668006];
            foreach ($subscribers as $chatId) {
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    "parse_mode"=>"HTML",
                    'text' => '🚨 هنالك عملية شحن بيمو:'.PHP_EOL.''.PHP_EOL.'معرف المستخدم: <b><code>'.$charge->chat_id.'</code></b>'.PHP_EOL.'رقم العملية: <b><code>'.$charge->processid.'</code></b> '.PHP_EOL.'المبلغ: <b><code>'.$charge->amount.'</code></b> ل.س'.PHP_EOL.' المحفظة: '.$charge->chat->balance.' NSP'.PHP_EOL.' الوقت: '.$charge->created_at.PHP_EOL.' رقم العملية: '.$charge->id  ,
                    'reply_markup' => $keyboard,
                ]);
            }
            return response()->json(["status"=>"success","message"=>"🏷 جاري التحقق من عملية الدفع".PHP_EOL."".PHP_EOL."🏷 ستستغرق العملية بضع دقائق".PHP_EOL."".PHP_EOL."🏷 شكراً لانتظارك"]);
        }else{
            return response()->json(["status"=>"failed","message"=>"حصل خطأ أثناء تنفيذ العملية"]);
        }
    }

    public function charge(Request $request)
    {
        $form = Validator::make($request->all(),[
                "amount"=>"required|numeric",
                "processid"=>"required|numeric",
                "chat_id"=>"required",
                "method"=>"required"
        ],[
            "numeric"=>"الرجاء إدخال قيم صحيحة"
        ]);
        if($form->fails()){
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status"=>"validator","message"=>$errorMessages]);
        }
        $form =$form->validate();
        $checkCharge = Charge::where("processid",$form['processid'])->first();
        if($checkCharge){
            if($checkCharge['status']=='complete'){
                return response()->json(["status"=>"failed","message"=>"عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            }else{
                return response()->json(["status"=>"failed","message"=>"عملية التحويل موجودة مسبقاً، الرجاء التواصل مع الدعم لمعالجة الخطأ"]);
            }
        }
        ///////
        $bodys =collect([
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv11&deviceId=ffffffff-fa8d-e3ca-ffff-ffffef05ac4a&userId=1657180&sortType=1&mobileManufaturer=samsung&mobileModel=SM-A505F&channelName=4&lang=0&hash=cd939479d1e2c5e0dfb93b428825a77e467c1c890131508fe85199c6e6f6ed07&status=1',
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv14&deviceId=00000000-0161-baa6-ffff-ffffef05ac4a&userId=3324251&sortType=1&mobileManufaturer=samsung&mobileModel=SM-A055F&channelName=4&lang=0&hash=4cfd244c5de005d1a80c2121cdbe77728ca4f7cbe9f37bdce5a907cd34cba246&status=2',
            'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv14&deviceId=00000000-0161-baa6-ffff-ffffef05ac4a&userId=4763960&sortType=1&mobileManufaturer=samsung&mobileModel=SM-A055F&channelName=4&lang=0&hash=095e21b4fe2375b77a5c3a63a8757918fe9969fd92826932ef878a97a863b313&status=2'
        ]);
        // $client = new Client(['proxy' => 'http://uc28a3ecf573f05d0-zone-custom-region-sy-asn-AS29256:uc28a3ecf573f05d0@43.153.237.55:2334']);
        $client = new Client();
        $pass = false;
        $iter = 0;
        do{
            try{
                $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                        'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
                        'Host' => 'cash-api.syriatel.sy',
                        'Connection' => 'Keep-Alive',
                        'Accept-Encoding' => 'gzip'
                    ],
                    'body' => $bodys->get($iter),
                    'timeout' => 120
                ]);

                $body = json_decode($response->getBody()->getContents());

                if($body->code==1){
                        $data =  $body->data->data;
                        $desiredAmount = $form['amount'];
                        $desiredTransactionNo = $form['processid'];

                        // تحقق من وجود العنصر الذي يحتوي على القيمتين المحددتين
                        $found = false;
                        $matchedAmount = null;
                        foreach ($data as $item) {
                            if ($item->amount == $desiredAmount && $item->transactionNo == $desiredTransactionNo) {
                                $found = true;
                                $matchedAmount = $item->amount;
                                break;
                            }
                        }
                        if($found){
                            $form['status']='complete';
                            $charge = Charge::create($form);
                                if($charge){
                                    if($desiredAmount>=5000){
                                        $chat = Chat::find($form['chat_id']);
                                        $chat->balance = $chat->balance +$matchedAmount;
                                        $chat->save();
                                        return response()->json(["status"=>"success","message"=>"شكراً لك، تم شحن رصيدك في البوت بنجاح."]);
                                    }else{
                                        return response()->json(["status"=>"success","message"=>"أقل قيمة للشحن هي 5000 وأي قيمة أقل من 5000 لايمكن شحنها أو استرجاعها"]);
                                    }
                                }else{
                                        return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                                }
                            $pass=true;
                        }elseif($iter<2){
                                $iter = $iter+1;
                        }else{
                                return response()->json(["status"=>"failed","message"=>"عملية الدفع غير صحيحة" ]);
                        }

                }
                else{
                        return response()->json(["status"=>"failed","message"=>"فشل التحقق من عملية الدفع، الرجاء التواصل مع الدعم" ]);

                }
            }catch(GuzzleException $e){
                // return response()->json(["status"=>"failedsy","message"=>"فشلت التحقق الآلي من عملية الدفع، الرجاء إعادة المحاولة"]);
            }
        }while(!$pass);
    }























    public function undo_withdraw(Request $request)
    {
        $withdrawId = $request->withdrawId;
        $withdraw = Withdraw::find($withdrawId);
        if (!$withdraw) {
            return response()->json(["status" => "failed","message"=>"حدث خطأ أثناء عملية التراجع"]);
        }
        if($withdraw->status != "requested"){
            return response()->json(["status"=>"failed","message"=>"لايمكن التراجع عن طلبات منفّذة أو ملغية"]);
        }
        $withdraw->status = "canceled";
        $saved = $withdraw->save();
        if($saved){
            $subscribers = [842668006,5144738358];
            foreach ($subscribers as $chatId) {
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    "parse_mode"=>"HTML",
                    'text' => '💡إعلام💡:'.PHP_EOL.'قام المشترك <b><code>'.$withdraw->chat_id.'</code></b> بالتراجع عن الطلب.'.PHP_EOL.''.PHP_EOL.'الوقت: '.$withdraw->updated_at.''.PHP_EOL.'رقم الطلب: '.$withdraw->id  ,
                ]);
            }
           return response()->json(["status"=>"success","message"=>"تمت عملية التراجع"]);
        }else{
            return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء عملية التراجع"]);
        }
    }



    public function ex_ich_charge(Request $request)
    {
        $orderId = $request->orderId;
        $admin_chat_id = $request->chat_id;
        $transacion = IchTransaction::find($orderId);


        $subscribers = [842668006,5144738358];

        if($transacion->status != 'requested'){
            return response()->json(["status"=>"requested","message"=>"🔔 تم معالجة الطلب في وقت سابق"]);
        }

        if($transacion->chat->balance < $transacion->amount){
            foreach ($subscribers as $chatId) {
                if($chatId != $admin_chat_id){
                    $response = Telegram::sendMessage([
                        'chat_id' => $chatId,
                        "parse_mode"=>"HTML",
                        'text' => '❗️النتيجة❗️:'.PHP_EOL.'لايوجد رصيد كافي في محفظة المستخدم لتنفيذ عملية شحن شحاب أيشانسي'.PHP_EOL.'معرف اللاعب:<b><code>'.$transacion->ichancy->identifier.'</code></b>',
                    ]);
                }
            }
            $response = Telegram::sendMessage([
                'chat_id' => $transacion->chat_id,
                "parse_mode"=>"HTML",
                'text' => '❗️النتيجة❗️:'.PHP_EOL.'لايوجد رصيد كافي في محفظتك لتنفيذ عملية شحن حساب أيشانسي،قد يكون جرى عملية سحب من المحفظة خلال فترة معالجة الطلب',
            ]);
            return response()->json(["status"=>"balance","message"=>"لايوجد رصيد كافي في محفظة المستخدم لشحن المبلغ المطلوب"]);
        }
        $client = new Client();
        $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
        $playerId = $transacion->ichancy->identifier;
        $pass = false;
        do{
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/depositToPlayer', [
        'headers' => [
            'Content-Type' => 'application/json',
            'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
            'Accept-Encoding' => 'gzip,deflate,br',
            'Accept' => '*/*',
            'dnt'=> '1',
            'origin'=>'https://agents.ichancy.com',
            'sec-fetch-site: same-origin',
            'sec-fetch-mode: cors',
            'sec-fetch-dest: empty',
            'accept-encoding'=>'gzip, deflate, br',
            'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
            'cookie' => $cookies,
        ],
        'body' => '{"amount":'.$transacion->amount.',"comment":null,"playerId":"'.$playerId.'","currencyCode":"NSP","currency":"NSP","moneyStatus":5}'
        ]);
        $body2 = json_decode($response2->getBody()->getContents());


        if (is_object($body2->result)) {
            $transacion->status = "complete";
            $saved = $transacion->save();
            $pass =true;
            if($saved){
                $transacion->chat->balance=$transacion->chat->balance-$transacion->amount;
                $transacion->chat->save();
                    $response = Telegram::sendMessage([
                        'chat_id' => $transacion->chat_id,
                        'text' => '✅ تم شحن حسابك أيشانسي بنجاح:'.PHP_EOL.'شكراً على انتظارك',
                    ]);
                    foreach ($subscribers as $chatId) {
                        if($chatId != $admin_chat_id){
                            $response = Telegram::sendMessage([
                                'chat_id' => $chatId,
                                "parse_mode"=>"HTML",
                                'text' => '🔔 الأدمن الآخر:'.PHP_EOL.''.PHP_EOL.'✅ تم شحن حساب المستخدم بنجاح'.PHP_EOL.'معرف اللاعب: <b><code>'.$playerId.'</code></b>'.PHP_EOL.' المبلغ:'.$transacion->amount.' NSP',
                            ]);
                        }
                    }
                return response()->json(["status"=>"success","message"=>'✅ تم شحن حساب المستخدم بنجاح'.PHP_EOL.'معرف اللاعب: <code>'.$playerId.'</code>'.PHP_EOL.' المبلغ:'.$transacion->amount.' NSP']);
            }else{
                return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء معالجة الطلب، الرجاء المحاولة في وقت لاحق"]);
            }
        } elseif($body2->result == "ex") {
            $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                'Accept-Encoding' => 'gzip,deflate,br',
                'Accept' => '*/*',
                'dnt'=> '1',
                'origin'=>'https://agents.ichancy.com',
                'sec-fetch-site: same-origin',
                'sec-fetch-mode: cors',
                'sec-fetch-dest: empty',
                'accept-encoding'=>'gzip, deflate, br',
                'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
            ],
            'body' => '{"username": "'.env('AGENT_NAME').'","password": "'.env("AGENT_PWD").'"}'
            ]);
            $incom_cookies = $response->getHeader('Set-Cookie');
            $cookies='';
            foreach($incom_cookies as $cookie) {
                // تقسيم النص بناءً على الفاصلة منقوطة
                $parts = explode(';', $cookie);
                // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                $cookies .= trim($parts[0]) . ';';
            }
            // حذف آخر فاصلة منقوطة
            $cookies = rtrim($cookies, ';');
            }elseif($body2->result == false){
                foreach ($subscribers as $chatId) {
                    if($chatId != $admin_chat_id){
                        $response = Telegram::sendMessage([
                            'chat_id' => $chatId,
                            "parse_mode"=>"HTML",
                            'text' => '🔔 الأدمن الآخر:'.PHP_EOL.''.PHP_EOL.'🔅 حدث خطأ أثناء تنفيذ العملية، تحقق من رصيد الكاشيرة ثم من حركة الحساب في لوحة التحكم',
                        ]);
                    }
                }
                return response()->json(["status"=>"failed","message"=>"🔅 حدث خطأ أثناء تنفيذ العملية، تحقق من رصيد الكاشيرة ثم من حركة الحساب في لوحة التحكم"]);
            }
        }while(!$pass);
    }

    public function reject_bemo_charge(Request $request)
    {
        $orderId = $request->orderId;
        $charge = Charge::find($orderId);
        if($charge->status != 'pending'){
            return response()->json(["status"=>"failed","message"=>"🔔 تمت معالجة الطلب في وقت سابق"]);
        }
        $charge->status = "reject";
        $saved = $charge->save();
        if($saved){
            $response = Telegram::sendMessage([
                'chat_id' => $charge->chat_id,
                "parse_mode"=>"HTML",
                'text' => "🚫 عملية الدفع عبر بيمو غير صحيحة",
            ]);
            return response()->json(["status"=>"success","message"=>"🔔 تم رفض الطلب بنجاح"]);
        }else{
            return response()->json(["status"=>"failed","message"=>"⛔️ حدث خطأ أثناء رفض الطلب"]);
        }
    }

    public function ex_bemo_charge(Request $request)
    {
        $orderId = $request->orderId;
        $charge = Charge::find($orderId);
        if($charge->status != 'pending'){
            return response()->json(["status"=>"failed","message"=>"🔔 تمت معالجة الطلب في وقت سابق"]);
        }
        $charge->chat->balance= $charge->chat->balance + $charge->amount;
        $saved = $charge->chat->save();
        if($saved){
            $charge->status = "complete";
            $charged = $charge->save();
            if($charged){
                $response = Telegram::sendMessage([
                    'chat_id' => $charge->chat_id,
                    'text' => '✅ نجاح:'.PHP_EOL.''.PHP_EOL.'✅ تم تنفيذ عملية الشحن بنجاح عبر بنك بيمو'.PHP_EOL.''.PHP_EOL.'💵 رصيد حسابك في البوت: '.$charge->chat->balance.' NSP',
                ]);
                return response()->json(["status"=>"success","message"=>"✅ تم تنفيذ عملية شحن بنجاح عبر بيمو"]);
            }else{
                return response()->json(["status"=>"failed","message"=>"🔔 تمت عملية شحن محفظة المستخدم، ولكن فشلت عملية تعديل حالة الطلب"]);
            }
        }else{
            return response()->json(["status"=>"failed","message"=>"🔔 فشلت عملية شحن محفظة المستخدم"]);
        }
    }

    public function charge_ichancy(Request $request)
    {
        $form = $request->all();
        $form['type']='charge';
        $chat= Chat::find($form['chat_id']);
        $balance = $chat->balance;
        if($balance < $form["amount"]){
            return response()->json(["status"=>"balance","message"=>"لايوجد رصيد كافي في حسابك لشحن المبلغ المطلوب".PHP_EOL."أدخل مبلغ شحن بكافئ رصيدك الحالي في البوت أو دون:"]);
        }
        $count = IchTransaction::where('chat_id', '=', $form["chat_id"])->where('type','=','charge')->where('status', '=', "requested")->count();
        if($count!=0){
            return response()->json(["status"=>"requested","message"=>"لديك طلب شحن سابق غير معالج، الرجاء الانتظار"]);
        }
        $ichancy = Ichancy::where('chat_id', '=', $form["chat_id"])->first();
        $form["ichancy_id"] = $ichancy->id;
        $client = new Client();
        $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
        $playerId = $ichancy->identifier;
        $pass = false;
        do{
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/depositToPlayer', [
        'headers' => [
            'Content-Type' => 'application/json',
            'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
            'Accept-Encoding' => 'gzip,deflate,br',
            'Accept' => '*/*',
            'dnt'=> '1',
            'origin'=>'https://agents.ichancy.com',
            'sec-fetch-site: same-origin',
            'sec-fetch-mode: cors',
            'sec-fetch-dest: empty',
            'accept-encoding'=>'gzip, deflate, br',
            'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
            'cookie' => $cookies,
        ],
        'body' => '{"amount":'.$form['amount'].',"comment":null,"playerId":"'.$playerId.'","currencyCode":"NSP","currency":"NSP","moneyStatus":5}'
        ]);
        $body2 = json_decode($response2->getBody()->getContents());


        if (is_object($body2->result)) {
            $form["status"] = "complete";
            $transacion = IchTransaction::create($form);
            $pass =true;
            if($transacion){
                $chat->balance=$chat->balance-$form['amount'];
                $chat->save();
                return response()->json(["status"=>"success","message"=>"✅ تم شحن حسابك بنجاح"]);
            }else{
                return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء معالجة الطلب، الرجاء المحاولة في وقت لاحق"]);
            }
        } elseif($body2->result == "ex") {
            $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                'Accept-Encoding' => 'gzip,deflate,br',
                'Accept' => '*/*',
                'dnt'=> '1',
                'origin'=>'https://agents.ichancy.com',
                'sec-fetch-site: same-origin',
                'sec-fetch-mode: cors',
                'sec-fetch-dest: empty',
                'accept-encoding'=>'gzip, deflate, br',
                'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
            ],
            'body' => '{"username": "'.env('AGENT_NAME').'","password": "'.env("AGENT_PWD").'"}'
            ]);
            $incom_cookies = $response->getHeader('Set-Cookie');
            $cookies='';
            foreach($incom_cookies as $cookie) {
                // تقسيم النص بناءً على الفاصلة منقوطة
                $parts = explode(';', $cookie);
                // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                $cookies .= trim($parts[0]) . ';';
            }
            // حذف آخر فاصلة منقوطة
            $cookies = rtrim($cookies, ';');
            }elseif($body2->result == false){
                $transacion = IchTransaction::create($form);
                $inlineKeyboard = [
                    [
                        ['text' => '✅ تنفيذ العملية', 'callback_data' => 'ex_ich_charge:'.$transacion->id],
                        ['text' => '▶️ متابعة الطلب', 'callback_data' => 'pending_ich_charge:'.$chat->id],
                    ]
                ];
                $keyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
                $subscribers = [842668006,5144738358];
                foreach ($subscribers as $chatId) {
                    $response = Telegram::sendMessage([
                        'chat_id' => $chatId,
                        "parse_mode"=>"HTML",
                        'text' => '🚨 هنالك عملية تعبئة فوق قدرة الكاشيرة الرجاء اعادة التعبئة لاتمام عملية الشحن:'.PHP_EOL.''.PHP_EOL.'معرف المستخدم: <b><code>'.$transacion->chat_id.'</code></b>'.PHP_EOL.'حساب اللاعب: <b><code>'.$ichancy->username.'</code></b> '.PHP_EOL.'معرّف اللاعب: <b><code>'.$ichancy->identifier.'</code></b>'.PHP_EOL.' مبلغ الشحن: '.$transacion->amount.' NSP'.PHP_EOL.' المحفظة: '.$chat->balance.' NSP'.PHP_EOL.' الوقت: '.$transacion->created_at.PHP_EOL.' رقم العملية: '.$transacion->id  ,
                        'reply_markup' => $keyboard,
                    ]);
                }
                return response()->json(["status"=>"failed","message"=>"🔅 سيستغرق شحن حساب أيشانسي قليلاً من الوقت, سيتم إعلامك بإتمام العملية بعد قليل"]);
            }
        }while(!$pass);

    }

    public function withdraw(Request $request)
    {
        $form = $request->all();
        $balance = Chat::select('balance')->where('id', '=', $form["chat_id"])->value('balance');
        if($balance < $form["amount"]){
            return response()->json(["status"=>"balance","message"=>"لايوجد رصيد كافي في حسابك لسحب المبلغ المطلوب"]);
        }
        $count = Withdraw::where('chat_id', '=', $form["chat_id"])->where('status', '=', "requested")->count();
        if($count!=0){
            return response()->json(["status"=>"requested","message"=>"لديك طلب سحب سابق غير معالج، الرجاء انتظار المهلة المحددة ومن ثم الاتصال بالدعم للمعالجة"]);
        }
        $amount = $form['amount'];
        if($amount<25000){
            return response()->json(["status"=>"minvalue","message"=>"أقل قيمة يمكن سحبها هي 25,000 "]);
        }
        //حساب قيمة الخصم (10٪)
        $discount = $amount * 0.1;
        // الحصول على المبلغ بعد الخصم
        $finalAmount = $amount - $discount;
        $stringValue = strval($finalAmount);
        // القيمة المخصومة
        $discountAmount = $amount - $finalAmount;
        $form['finalAmount']=$finalAmount;
        $form['discountAmount']=$discountAmount;
        $withdraw = Withdraw::create($form);
        if($withdraw){
            $inlineKeyboard = [
                [
                    ['text' => '✅ تنفيذ العملية', 'callback_data' => 'ex_withdraw:'.$withdraw->id]
                ]
            ];
            $keyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
            $subscribers = [842668006,5144738358];
            $messagetext = '🚨عاجل🚨:'.PHP_EOL.'تم إضافة طلب سحب للمشترك <b><code>'.$form["chat_id"].'</code></b> وبانتظار المعالجة.'.PHP_EOL.''.PHP_EOL.'الوقت: '.$withdraw->created_at.''.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'القيمة النهائية: '.$withdraw->finalAmount.''.PHP_EOL.'عبر: '.$withdraw->method.''.PHP_EOL.'كود التحويل: <b><code>'.$withdraw->code.'</code></b>'.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.'';
            if($withdraw->subscriber!=null){$messagetext=$messagetext.''.PHP_EOL.'المستفيد: '.$withdraw->subscriber;}
            foreach ($subscribers as $chatId) {
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    "parse_mode"=>"HTML",
                    // 'text' => '🚨عاجل🚨:'.PHP_EOL.'تم إضافة طلب سحب للمشترك <b><code>'.$form["chat_id"].'</code></b> وبانتظار المعالجة.'.PHP_EOL.''.PHP_EOL.'الوقت: '.$withdraw->created_at.''.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'القيمة النهائية: '.$withdraw->finalAmount.''.PHP_EOL.'عبر: '.$withdraw->method.''.PHP_EOL.'كود التحويل: <b><code>'.$withdraw->code.'</code></b>'.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.''.PHP_EOL.''.$withdraw->subscriber ,
                    'text' => $messagetext,
                    'reply_markup' => $keyboard,
                ]);
            }
            return response()->json(["status"=>"success","message"=>"✅ تم طلب السحب بنجاح\nسيتم إعلامك بتنفيذ الطلب خلال ساعة\nمعلومات الطلب:\n\nرقم الطلب: ".$withdraw->id."\nالطلب: ".$withdraw->code."\nالقيمة: ".$withdraw->amount."\nنسبة الاقتطاع: 10%\nالمبلغ المقتطع: ".$withdraw->discountAmount."\nالقيمة المستحقة بعد الاقتطاع: ".$withdraw->finalAmount."\nمعرف المستخدم: ".$withdraw->chat_id."\nطريقة السحب: ".$withdraw->method,"withdrawId"=>$withdraw->id]);
        }else{
            return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء معالجة الطلب، الرجاء المحاولة في وقت لاحق"]);
        }
}

    public function ex_withdraw(Request $request){
        $subscribers = [842668006,5144738358];
        $admin_chat_id = $request->chat_id;
        $withdraw = Withdraw::find($request->orderId);
        if($withdraw->status != "requested"){
            return response()->json(["status"=>"failed","message"=>'❗️ تمت معالجة عملية السحب في وقت لاحق']);
        }
        if($withdraw->chat->balance<$withdraw->amount){
            foreach ($subscribers as $chatId) {
                if($chatId != $admin_chat_id){
                    $response = Telegram::sendMessage([
                        'chat_id' => $chatId,
                        "parse_mode"=>"HTML",
                        'text' => '💡 الأدمن الآخر:'.PHP_EOL.'رصيد المستخدم أصبح غير كافي لإتمام عملية السحب'.PHP_EOL.''.PHP_EOL.'معرف المستخدم: <b><code>'.$withdraw->chat_id.'</code></b>'.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.' NSP'.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'المبلغ المطلوب سحبه: '.$withdraw->finalAmount,
                    ]);
                }
            }
            $response = Telegram::sendMessage([
                'chat_id' => $withdraw->chat_id,
                "parse_mode"=>"HTML",
                'text' => "❗️ رصيدك في البوت أصبح غير كافي لإتمام عملية السحب",
            ]);
            return response()->json(["status"=>"failed","message"=>'❗️ رصيد المستخدم أصبح غير كافي لإتمام عملية السحب'.PHP_EOL.''.PHP_EOL.'معرف المستخدم: <b><code>'.$withdraw->chat_id.'</code></b>'.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.' NSP'.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'المبلغ المطلوب سحبه: '.$withdraw->finalAmount]);
        }
        $withdraw->status="complete";
        $saved = $withdraw->save();
        if($saved){
            $withdraw->chat->balance=$withdraw->chat->balance-$withdraw->amount;
            $updated = $withdraw->chat->save();
            if($updated){
                foreach ($subscribers as $chatId) {
                    if($chatId != $admin_chat_id){
                        $response = Telegram::sendMessage([
                            'chat_id' => $chatId,
                            "parse_mode"=>"HTML",
                            'text' => '💡 الأدمن الآخر:'.PHP_EOL.''.PHP_EOL.'✅ تم تنفيذ عملية السحب بنجاح'.PHP_EOL.''.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'معرف المستخدم: '.$withdraw->chat_id.''.PHP_EOL.'القيمة: '.$withdraw->amount.''.PHP_EOL.'القيمة النهائية: '.$withdraw->finalAmount.''.PHP_EOL.'نسبة الحسم: 10%'.PHP_EOL.'القيمة المحسومة: '.$withdraw->discountAmount.''.PHP_EOL.''.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.' NSP',
                        ]);
                    }
                }
                $response = Telegram::sendMessage([
                    'chat_id' => $withdraw->chat_id,
                    'text' => '✅ تم تنفيذ عملية السحب بنجاح'.PHP_EOL.''.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'معرف المستخدم: '.$withdraw->chat_id.''.PHP_EOL.'القيمة: '.$withdraw->amount.''.PHP_EOL.'القيمة النهائية: '.$withdraw->finalAmount.''.PHP_EOL.'نسبة الحسم: 10%'.PHP_EOL.'القيمة المحسومة: '.$withdraw->discountAmount.''.PHP_EOL.''.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.' NSP',
                ]);
                return response()->json(["status"=>"success","message"=>'✅ تم تنفيذ عملية السحب بنجاح'.PHP_EOL.''.PHP_EOL.'رقم الطلب: '.$withdraw->id.''.PHP_EOL.'معرف المستخدم: '.$withdraw->chat_id.''.PHP_EOL.'القيمة: '.$withdraw->amount.''.PHP_EOL.'القيمة النهائية: '.$withdraw->finalAmount.''.PHP_EOL.'نسبة الحسم: 10%'.PHP_EOL.'القيمة المحسومة: '.$withdraw->discountAmount.''.PHP_EOL.''.PHP_EOL.'الرصيد الحالي: '.$withdraw->chat->balance.' NSP']);
            }else{
                return response()->json(["status"=>"failed","message"=>'❗️ فشلت عملية تحديث رصيد المستخدم بعد إتمام السحب']);
            }
        }else{
            return response()->json(["status"=>"failed","message"=>'❗️ فشلت عملية السجب']);
        }
    }

    public function getIchancyBalance(Request $request)
    {
        $form = $request->all();
        $playerId = Ichancy::select('identifier')->where('chat_id', '=', $form["chat_id"])->value('identifier');
        $client = new Client();
        $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
        $pass = false;
        do{
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/getPlayerBalanceById', [
            'headers' => [
            'Content-Type' => 'application/json',
            'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
            'Accept-Encoding' => 'gzip,deflate,br',
            'Accept' => '*/*',
            'dnt'=> '1',
            'origin'=>'https://agents.ichancy.com',
            'sec-fetch-site: same-origin',
            'sec-fetch-mode: cors',
            'sec-fetch-dest: empty',
            'accept-encoding'=>'gzip, deflate, br',
            'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
            'cookie' => $cookies,
        ],
        'body' => '{"playerId":"'.$playerId.'"}'
        ]);
        $body2 = json_decode($response2->getBody()->getContents());


        if (is_array($body2->result)) {
            $ichancyBalance = data_get(($body2->result)[0],"balance",null);
            return response()->json(["status"=>"success","message"=>"🌳 رصيد حساب أيشانسي الخاص بك: ".$ichancyBalance." NSP"]);
        } elseif($body2->result == "ex") {
            $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                'Accept-Encoding' => 'gzip,deflate,br',
                'Accept' => '*/*',
                'dnt'=> '1',
                'origin'=>'https://agents.ichancy.com',
                'sec-fetch-site: same-origin',
                'sec-fetch-mode: cors',
                'sec-fetch-dest: empty',
                'accept-encoding'=>'gzip, deflate, br',
                'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
            ],
            'body' => '{"username": "'.env('AGENT_NAME').'","password": "'.env("AGENT_PWD").'"}'
            ]);
            $incom_cookies = $response->getHeader('Set-Cookie');
            $cookies='';
            foreach($incom_cookies as $cookie) {
                // تقسيم النص بناءً على الفاصلة منقوطة
                $parts = explode(';', $cookie);
                // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                $cookies .= trim($parts[0]) . ';';
            }
            // حذف آخر فاصلة منقوطة
            $cookies = rtrim($cookies, ';');
            }
        }while(!$pass);
    }

    public function withdraw_ichancy(Request $request)
    {
        $form = $request->all();
        $count = IchTransaction::where('chat_id', '=', $form["chat_id"])->where('type','=','withdraw')->where('status', '=', "requested")->count();
        if($count!=0){
            return response()->json(["status"=>"requested","message"=>"لديك طلب سحب سابق غير معالج، الرجاء الانتظار"]);
        }
        $ichancy = Ichancy::where('chat_id', '=', $form["chat_id"])->first();
        $client = new Client();
        $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
        $playerId = $ichancy->identifier;
        $pass = false;
        do{
            $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/getPlayerBalanceById', [
            'headers' => [
            'Content-Type' => 'application/json',
            'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
            'Accept-Encoding' => 'gzip,deflate,br',
            'Accept' => '*/*',
            'dnt'=> '1',
            'origin'=>'https://agents.ichancy.com',
            'sec-fetch-site: same-origin',
            'sec-fetch-mode: cors',
            'sec-fetch-dest: empty',
            'accept-encoding'=>'gzip, deflate, br',
            'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
            'cookie' => $cookies,
        ],
        'body' => '{"playerId":"'.$playerId.'"}'
        ]);
        $body2 = json_decode($response2->getBody()->getContents());


        if (is_array($body2->result)) {
            $ichancyBalance = data_get(($body2->result)[0],"balance",null);
            if($ichancyBalance<$form['amount']){
                return response()->json(["status"=>"success","message"=>"⛔️ لايوجد رصيد كافي في حسابك أيشانسي لسحب المبلغ المطلوب"]);
            }else{
                $response3 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/withdrawFromPlayer', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                        'Accept-Encoding' => 'gzip,deflate,br',
                        'Accept' => '*/*',
                        'dnt'=> '1',
                        'origin'=>'https://agents.ichancy.com',
                        'sec-fetch-site: same-origin',
                        'sec-fetch-mode: cors',
                        'sec-fetch-dest: empty',
                        'accept-encoding'=>'gzip, deflate, br',
                        'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                        'cookie' => $cookies,
                    ],
                    'body' => '{"amount":-'.$form['amount'].',"comment":null,"playerId":"'.$playerId.'","currencyCode":"NSP","currency":"NSP","moneyStatus":5}'
                    ]);
                $body3 = json_decode($response3->getBody()->getContents());
                if($body3->result ==false){
                    return response()->json(["status"=>"success","message"=>"⛔️ حدث خطأ أثناء تنفيذ السحب، الرجاء المحاولة من جديد"]);
                }elseif($body2->result !== "ex"){
                    $form["status"] = "complete";
                    $form["ichancy_id"] = $ichancy->id;
                    $transacion = IchTransaction::create($form);
                    $chat = Chat::find($ichancy->chat_id);
                    $chat->balance = $chat->balance+$transacion->amount;
                    $saved = $chat->save();
                    if($saved){
                       return response()->json(["status"=>"success","message"=>"✅ تم سحب مبلغ: ".$transacion->amount."NSP من حسابك بنجاح."]);
                    }else{
                        return response()->json(["status"=>"success","message"=>"تم السحب لكن فشلت عملية تسجيل العملية لدينا"]);
                    }
                }

            }
        } elseif($body2->result == "ex") {
            $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                'Accept-Encoding' => 'gzip,deflate,br',
                'Accept' => '*/*',
                'dnt'=> '1',
                'origin'=>'https://agents.ichancy.com',
                'sec-fetch-site: same-origin',
                'sec-fetch-mode: cors',
                'sec-fetch-dest: empty',
                'accept-encoding'=>'gzip, deflate, br',
                'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
            ],
            'body' => '{"username": "'.env('AGENT_NAME').'","password": "'.env("AGENT_PWD").'"}'
            ]);
            $incom_cookies = $response->getHeader('Set-Cookie');
            $cookies='';
            foreach($incom_cookies as $cookie) {
                // تقسيم النص بناءً على الفاصلة منقوطة
                $parts = explode(';', $cookie);
                // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                $cookies .= trim($parts[0]) . ';';
            }
            // حذف آخر فاصلة منقوطة
            $cookies = rtrim($cookies, ';');
            }
        }while(!$pass);

    }


    public function getMyBalance(Request $request)
    {
        $form =$request->chat_id;
        $chat = Chat::find($form);
        return response()->json(["status"=>"success","balance"=>$chat["balance"]]);

    }
    public function newichaccount(Request $request)
    {
         $client = new Client();

        // $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
        //     'headers' => [
        //         'Content-Type' => 'application/json',
        //         'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
        //         'Accept-Encoding' => 'gzip,deflate,br',
        //         'Accept' => '*/*',
        //         'dnt'=> '1',
        //         'origin'=>'https://agents.ichancy.com',
        //         'sec-fetch-site: same-origin',
        //         'sec-fetch-mode: cors',
        //         'sec-fetch-dest: empty',
        //         'accept-encoding'=>'gzip, deflate, br',
        //         'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
        //     ],
        //     'body' => '{"username": "xxxxx","password": "xxxx"}'
        // ]);

        // $body = json_decode($response->getBody()->getContents());

        // if($body->result !==false){
        if(true){
            //$incom_cookies = $response->getHeader('Set-Cookie');
            //$incom_cookies = ['PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=b387e774c1076ba60fcc6841c50115e6; Path=/; Domain=agents.ichancy.com; Expires=Wed, 16 Apr 2025 00:23:17 GMT','languageCode=ar_IQ; Path=/','language=English%20%28UK%29; Path=/'];
            // $incom_cookies =[
            //     "PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142; Path=/; Domain=agents.ichancy.com; Expires=Wed, 16 Apr 2025 02:10:14 GMT",
            //     "languageCode=en_GB; Path=/",
            //     "language=English%20%28UK%29; Path=/"
            // ];
            // $cookies = [];
            // foreach ($incom_cookies as $cookie) {
            //     list($key, $value) = explode('=', $cookie, 2);
            //     $cookies[$key] = $value;
            // }
            $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
            $form =$request->all();
            $username = $form['e_username'];
            $password = $form['e_password'];
            $emailExt = "@player.nsp";
            $email = $username.$emailExt;
            $pass = false;
            do{
                $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/registerPlayer', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                        'Accept-Encoding' => 'gzip,deflate,br',
                        'Accept' => '*/*',
                        'dnt'=> '1',
                        'origin'=>'https://agents.ichancy.com',
                        'sec-fetch-site: same-origin',
                        'sec-fetch-mode: cors',
                        'sec-fetch-dest: empty',
                        'accept-encoding'=>'gzip, deflate, br',
                        'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                        'cookie' => $cookies,

                    ],
                    'body' => '{"player":{"email":"'.$email.'","password":"'.$password.'","parentId":"2344226","login":"'.$username.'"}}'
                ]);
                $body2 = json_decode($response2->getBody()->getContents());

                if ($body2->result==1) {
                    $pass = true;
                } elseif($body2->result == "ex") {
                    return response()->json(["status"=>"failede","reason"=>"ex"]);
                }else{
                    $chars = str_shuffle('abcdefghijklmnopqrstuvwxyz123456789'); // يحدد الحروف الممكنة
                    $randomString = substr($chars, 0, 4);
                    $username = $username.$randomString;
                    $email=$username.$emailExt;
                    $password=$password.$randomString;
                    $pass =false;
                }
            } while(!$pass);

            if($pass){
                $form['username'] = $username;
                $form['password'] = $password;
                $form['status'] = "complete";
                $ichancy = Ichancy::create($form);
                if($ichancy){
                    return response()->json(["status"=>"success","message"=>"تم إنشاء الحساب بنجاح".PHP_EOL."اسم المستخدم: ".$username."".PHP_EOL."كلمة المرور: ".$password.""]);
                }else{
                    return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                }
            }else{
                return response()->json(["status"=>"failed"]);
            }
        }else{
            return response()->json(["status"=>"failed"]);
        }
    }

    public function newichaccount1(Request $request)
    {
        $form =$request->all();
        $ichancy = Ichancy::create($form);
        if($ichancy){
            return response()->json(["status"=>"success","message"=>"تم طلب إنشاء الحساب بنجاح، جاري المعالجة وسيتم إرسال اسم المستخدم وكلمة المرور إليك في أسرع وقت "]);
        }else{
            return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
        }
    }

    public function checkbalance(Request $request)
    {
        $form =$request->chat_id;
        $chat = Chat::find($form);
        if($chat["balance"]>=10000){
            return response()->json(["status"=>"success"]);
        }else{
            return response()->json(["status"=>"failed"]);
        }
    }

    public function ichancy(Request $request)
    {
        $form = Validator::make($request->all(),[
            "chat_id"=>"required"
            ],[
                "numeric"=>"فشل الحصول على معرف الشات"
            ]);
            if($form->fails()){
                $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
                return response()->json(["status"=>"validator","message"=>$errorMessages]);
            }
            $form =$form->validate();
            $ichancy = Ichancy::where("chat_Id",$form['chat_id'])->first();
            if($ichancy){
                if($ichancy->identifier==null){
                    $client = new Client();
                    $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
                    $username = $ichancy->username;
                    $pass = false;
                    $playerId='';
                    do{
                        $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/getPlayersForCurrentAgent', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                        'Accept-Encoding' => 'gzip,deflate,br',
                        'Accept' => '*/*',
                        'dnt'=> '1',
                        'origin'=>'https://agents.ichancy.com',
                        'sec-fetch-site: same-origin',
                        'sec-fetch-mode: cors',
                        'sec-fetch-dest: empty',
                        'accept-encoding'=>'gzip, deflate, br',
                        'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                        'cookie' => $cookies,
                    ],
                    'body' => '{"start":0,"limit":20,"filter":{},"isNextPage":false,"searchBy":{"getPlayersFromChildrenLists":"'.$username.'"}}'
                    ]);
                    $body2 = json_decode($response2->getBody()->getContents());


                    if (is_object($body2->result)) {
                        if(!empty($body2->result->records)){
                        $users = collect($body2->result->records);
                        $playerId =  data_get($users->firstWhere('username', $username), 'playerId', null);
                        $ichancy->identifier=$playerId;
                        $saved = $ichancy->save();
                            if($saved){$pass=true;}else{return response()->json(["status"=>"success","message"=>"error_playerId"]);}
                        }else{
                            return response()->json(["status"=>"success","message"=>"error_playerId"]);
                        }
                    } elseif($body2->result == "ex") {
                        $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                            'Accept-Encoding' => 'gzip,deflate,br',
                            'Accept' => '*/*',
                            'dnt'=> '1',
                            'origin'=>'https://agents.ichancy.com',
                            'sec-fetch-site: same-origin',
                            'sec-fetch-mode: cors',
                            'sec-fetch-dest: empty',
                            'accept-encoding'=>'gzip, deflate, br',
                            'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
                        ],
                        'body' => '{"username": "'.env('AGENT_NAME').'","password": "'.env("AGENT_PWD").'"}'
                        ]);
                        $incom_cookies = $response->getHeader('Set-Cookie');
                        $cookies='';
                        foreach($incom_cookies as $cookie) {
                            // تقسيم النص بناءً على الفاصلة منقوطة
                            $parts = explode(';', $cookie);
                            // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                            $cookies .= trim($parts[0]) . ';';
                        }
                        // حذف آخر فاصلة منقوطة
                        $cookies = rtrim($cookies, ';');
                        }
                    }while(!$pass);
                }
                if($ichancy['status']=="requested"){
                    return response()->json(["status"=>"success","message"=>"requested"]);
                }
                return response()->json(["status"=>"success","message"=>"exist","username"=>$ichancy["username"]]);
            }else{
                return response()->json(["status"=>"success","message"=>"notexist"]);
            }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Category::all());
    }


    public function test(Request $request)
    {
        $this->validate($request,[
            "email"=>"required|email|exists:users,email",
            "password"=>"required"
        ]);
        if (!auth()->attempt($request->only("email","password"))){
            throw new AuthenticationException();
        }
        $token = auth()->user()->createToken("web",["categories:delete"])->plainTextToken;
        return ["token" => $token];


    }

    /**
     * Store a newly created resource in storage.
     */
    //or Login action
    public function store(Request $request)
    {
        $this->validate($request,[
            "email"=>"required|email|exists:users,email",
            "password"=>"required"
        ]);
        if (!auth()->attempt($request->only("email","password"))){
            throw new AuthenticationException();
        }
        return ["token" => auth()->user()->createToken("web")->plainTextToken];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


























//////////////////////testing




public function charge1(Request $request)
{
    // $form = Validator::make($request->all(),[
    //         "amount"=>"required|numeric",
    //         "processid"=>"required|numeric",
    //         "chat_id"=>"required"
    // ],[
    //     "numeric"=>"الرجاء إدخال قيم صحيحة"
    // ]);
    // if($form->fails()){
    //     $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
    //     return response()->json(["status"=>"validator","message"=>$errorMessages]);
    // }
    //  $form =$form->validate();
    ///////
    $client = new Client();

    $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory', [
        'headers' => [
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
            'Host' => 'cash-api.syriatel.sy',
            'Connection' => 'Keep-Alive',
            'Accept-Encoding' => 'gzip'
        ],
        'body' => 'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv11&deviceId=ffffffff-fa8d-e3ca-ffff-ffffef05ac4a&userId=1657180&sortType=1&mobileManufaturer=samsung&mobileModel=SM-A505F&channelName=4&lang=0&hash=cd939479d1e2c5e0dfb93b428825a77e467c1c890131508fe85199c6e6f6ed07&status=1'
    ]);

    $body = json_decode($response->getBody()->getContents());
    $data =  $body->data->data;
    $desiredAmount = "1100";
    $desiredTransactionNo = '600195060895';
    $found = (bool) array_filter($data, function($item) use ($desiredAmount, $desiredTransactionNo) {
        return $item->amount == $desiredAmount && $item->transactionNo == $desiredTransactionNo;
    });
    return $found;
    // تحقق من وجود العنصر الذي يحتوي على القيمتين المحددتين
    // $found = false;
    // foreach ($data as $item) {
    //     if ($item->amount == $desiredAmount && $item->transactionNo == $desiredTransactionNo) {
    //         $found = true;
    //         break;
    //     }
    // }
    // if($found){
    // $form['status']='complete';
    // $charge = Charge::create($form);
    //     if($charge){
    //         return response()->json(["status"=>"success","message"=>"شكراً لك، سيتم شحن رصيد في البوت فور التحقق من عملية الدفع وإعلامك على الفور."]);
    //     }else{
    //             return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
    //     }
    // }else{
    //     return response()->json(["status"=>"failed","message"=>"عملية الدفع غير صحيحة" ]);
    // }
}



// Testing auto withdrow
public function withdraw_auto(Request $request)
    {
        $form = $request->all();
        $balance = Chat::select('balance')->where('id', '=', $form["chat_id"])->value('balance');
        if($balance < $form["amount"]){
            return response()->json(["status"=>"balance","message"=>"لايوجد رصيد كافي في حسابك لسحب المبلغ المطلوب"]);
        }
        $count = Withdraw::where('chat_id', '=', $form["chat_id"])->where('status', '=', "requested")->count();
        if($count!=0){
            return response()->json(["status"=>"requested","message"=>"لديك طلب سحب سابق غير معالج، الرجاء الاتصال بالدعم للمعالجة"]);
        }
        $client = new Client();
        $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/features/ePayment/refresh_balance', [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
                'Host' => 'cash-api.syriatel.sy',
                'Connection' => 'Keep-Alive',
                'Accept-Encoding' => 'gzip'
            ],
            'body' => '{"appVersion":"5.5.2","mobileManufaturer":"samsung","mobileModel":"SM-A505F","lang":"0","systemVersion":"Android+v11","deviceId":"ffffffff-fa8d-e3ca-ffff-ffffef05ac4a","userId":"1657180","hash":"5611b0377dfe37a88541f4aa8eaa3b4f795e08fdfe1af02fdf907cda47326205"}'
        ]);

        $body = json_decode($response->getBody()->getContents());
        if($body->code==1){
            $mySyrialeCashBalance =  $body->data->data[0]->CUSTOMER_BALANCE;
            $amount = $form['amount'];

            // return response()->json(["discount"=>$discount,"finalAmount"=>$finalAmount,"discountAmount"=>$discountAmount,"amount"=>$amount]);
            if($amount>$mySyrialeCashBalance){
                return response()->json(["status"=>"failed","message"=>"لا يتوفر لدينا حالياً المبلغ المطلوب للسحب، الرجاء التواصل مع الدعم أو المحاولة في وقت لاحق"]);
            }else{
                //حساب قيمة الخصم (10٪)
                $discount = $amount * 0.1;

                // الحصول على المبلغ بعد الخصم
                $finalAmount = $amount - $discount;
                $stringValue = strval($finalAmount);
                // القيمة المخصومة
                $discountAmount = $amount - $finalAmount;

                $reqcheckbody = 'appVersion=5.5.2&mobileManufaturer=samsung&mobileModel=SM-A505F&lang=0&customerCodeOrGSM='.$form['code'].'&systemVersion=Android%2Bv11&deviceId=ffffffff-fa8d-e3ca-ffff-ffffef05ac4a&userId=1657180&transactAmount='.$finalAmount.'&hash=bf2032ac5155c820b05c8288e3b4f6bf7b59b6527111d655540b50f0e484a4fd&';
                $response_check = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/checkCustomer', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                        'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
                        'Host' => 'cash-api.syriatel.sy',
                        'Connection' => 'Keep-Alive',
                        'Accept-Encoding' => 'gzip'
                    ],
                    'body' =>$reqcheckbody
                ]);
                $body_check_trans = json_decode($response_check->getBody()->getContents());
                $feeAmount =null;
                $billcode =null;
                if($body_check_trans->code==1){
                   $feeAmount =  $body->data->data[0]->feeAmount;
                    $billcode =  $body->data->data[0]->billcode;
                }

                if($feeAmount && $billcode){
                    $reqTransbody = 'appVersion=5.5.2&amount=1200&fee='.$feeAmount.'&systemVersion=Android%2Bv11&deviceId=ffffffff-fa8d-e3ca-ffff-ffffef05ac4a&userId=1657180&toGSM='.$form['code'].'&mobileManufaturer=samsung&mobileModel=SM-A505F&pinCode=1234&billcode='.$billcode.'&lang=0&secretCodeOrGSM='.$form['code'].'&hash=af21aa4561da93f07045d1b567c71ca2145954ab284affa636ed38b2ff6d3e97&';
                    return $reqTransbody;
                    $response_trans = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/transfer', [
                        'headers' => [
                            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                            'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
                            'Host' => 'cash-api.syriatel.sy',
                            'Connection' => 'Keep-Alive',
                            'Accept-Encoding' => 'gzip'
                        ],
                        'body' =>$reqTransbody
                    ]);
                    $body_trans = json_decode($response_trans->getBody()->getContents());
                    if($body_trans->code==1 && $body_trans->message == "تمت العملية بنجاح"){
                        $form['finalAmount']=$finalAmount;
                        $form['discountAmount']=$discountAmount;
                        $form['status']='complete';
                        $withdraw = Withdraw::create($form);
                        if($withdraw){
                            $chat = Chat::find($form['chat_id']);
                            $chat->balance = $chat->balance - $amount;
                            $chat->save();
                            return response()->json(["status"=>"success","message"=>"تم تحويل المبلغ: ".$finalAmount."NSP إلى الرقم: ".$form['code']." وذلك بعد خصم نسبة 10% من المبلغ الكلي المطلوب، بما يعادل: ".$discountAmount." NSP"]);
                        }else{
                        return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء معالجة الطلب، الرجاء المحاولة في وقت لاحق"]);
                        }
                    }else{
                        return response()->json(["status"=>"failed","message"=>"نواجه مشكلة أثناء تنفيذ عملية السحب الآلي، الرجاء التواصل مع الدعم","msg"=>$body_trans]);
                    }
                }
            }

        }

    }



    public function newichaccount_v2(Request $request)
    {
         $client = new Client();



        // $body = json_decode($response->getBody()->getContents());

        // if($body->result !==false){
        if(true){
            //$incom_cookies = $response->getHeader('Set-Cookie');
            //$incom_cookies = ['PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=b387e774c1076ba60fcc6841c50115e6; Path=/; Domain=agents.ichancy.com; Expires=Wed, 16 Apr 2025 00:23:17 GMT','languageCode=ar_IQ; Path=/','language=English%20%28UK%29; Path=/'];
            // $incom_cookies =[
            //     "PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142; Path=/; Domain=agents.ichancy.com; Expires=Wed, 16 Apr 2025 02:10:14 GMT",
            //     "languageCode=en_GB; Path=/",
            //     "language=English%20%28UK%29; Path=/"
            // ];
            // $cookies = [];
            // foreach ($incom_cookies as $cookie) {
            //     list($key, $value) = explode('=', $cookie, 2);
            //     $cookies[$key] = $value;
            // }
            $cookies = 'PHPSESSID_3a07edcde6f57a008f3251235df79776a424dd7623e40d4250e37e4f1f15fadf=aa2ab69ccb1f2b68fca02aef93d66142;__cf_bm=.pMpbMYZAN8Wu8_D4EnBpcKKx9s_qUYavyo8uuURoS8-1744164614-1.0.1.1-spQ8HNpMG9NSxUM3m06M2j.ZwghTt.wczinH49gvylJMkvrqve5DDpXsdZV3WMcIdjOaWviwNNCduJHAzB4qYzLiBdZDaK7CcfuyENaMhqo;languageCode=ar_IQ';
            $form =$request->all();
            $username = $form['e_username'];
            $password = $form['e_password'];
            $emailExt = "@player.nsp";
            $email = $username.$emailExt;
            $pass = false;
            do{
                $response2 = $client->request('POST', 'https://agents.ichancy.com/global/api/Player/registerPlayer', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                        'Accept-Encoding' => 'gzip,deflate,br',
                        'Accept' => '*/*',
                        'dnt'=> '1',
                        'origin'=>'https://agents.ichancy.com',
                        'sec-fetch-site: same-origin',
                        'sec-fetch-mode: cors',
                        'sec-fetch-dest: empty',
                        'accept-encoding'=>'gzip, deflate, br',
                        'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6',
                        'cookie' => $cookies,
                    ],
                    'body' => '{"player":{"email":"'.$email.'","password":"'.$password.'","parentId":"2344226","login":"'.$username.'"}}'
                ]);
                $body2 = json_decode($response2->getBody()->getContents());

                if ($body2->result==1) {
                    $pass = true;
                } elseif($body2->result == "ex") {
                    $response = $client->request('POST', 'https://agents.ichancy.com/global/api/User/signIn', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'User-Agent' => ' Mozilla/5.0 (Linux; Android 11; SAMSUNG SM-A505F) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36',
                            'Accept-Encoding' => 'gzip,deflate,br',
                            'Accept' => '*/*',
                            'dnt'=> '1',
                            'origin'=>'https://agents.ichancy.com',
                            'sec-fetch-site: same-origin',
                            'sec-fetch-mode: cors',
                            'sec-fetch-dest: empty',
                            'accept-encoding'=>'gzip, deflate, br',
                            'accept-language'=> 'ar-AE,ar;q=0.9,en-GB;q=0.8,en;q=0.7,en-US;q=0.6'
                        ],
                        'body' => '{"username": "'.env('AGENT_NAME').'","password": "'.env("AGENT_PWD").'"}'
                        ]);
                        $incom_cookies = $response->getHeader('Set-Cookie');
                        $cookies='';
                        foreach($incom_cookies as $cookie) {
                            // تقسيم النص بناءً على الفاصلة منقوطة
                            $parts = explode(';', $cookie);
                            // اضافة الجزء الأول فقط إلى النص النهائي مع حذف المسافات الزائدة
                            $cookies .= trim($parts[0]) . ';';
                        }
                        // حذف آخر فاصلة منقوطة
                        $cookies = rtrim($cookies, ';');
                }else{
                    $chars = str_shuffle('abcdefghijklmnopqrstuvwxyz123456789'); // يحدد الحروف الممكنة
                    $randomString = substr($chars, 0, 4);
                    $username = $username.$randomString;
                    $email=$username.$emailExt;
                    $password=$password.$randomString;
                    $pass =false;
                }
            } while(!$pass);

            if($pass){
                $form['username'] = $username;
                $form['password'] = $password;
                $form['status'] = "complete";
                $ichancy = Ichancy::create($form);
                if($ichancy){
                    return response()->json(["status"=>"success","message"=>"✅ تم إنشاء الحساب بنجاح".PHP_EOL."".PHP_EOL."👤 اسم المستخدم: <code>".$username."</code>".PHP_EOL."🔐 كلمة المرور: <code>".$password."</code>"]);
                }else{
                    return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                }
            }else{
                return response()->json(["status"=>"failed"]);
            }
        }else{
            return response()->json(["status"=>"failed"]);
        }
    }

}













////////////////////////////charge orginal version for one code, next update to enable multi code
/*
public function charge(Request $request)
    {
        $form = Validator::make($request->all(),[
                "amount"=>"required|numeric",
                "processid"=>"required|numeric",
                "chat_id"=>"required",
                "method"=>"required"
        ],[
            "numeric"=>"الرجاء إدخال قيم صحيحة"
        ]);
        if($form->fails()){
            $errorMessages = $form->errors()->all(); // الحصول على جميع رسائل الخطأ
            return response()->json(["status"=>"validator","message"=>$errorMessages]);
        }
        $form =$form->validate();
        $checkCharge = Charge::where("processid",$form['processid'])->first();
        if($checkCharge){
            if($checkCharge['status']=='complete'){
                return response()->json(["status"=>"failed","message"=>"عملية التحويل منفّذة مسبقاً، الرجاء إدخال عملية تحويل جديدة"]);
            }else{
                return response()->json(["status"=>"failed","message"=>"عملية التحويل موجودة مسبقاً، الرجاء التواصل مع الدعم لمعالجة الخطأ"]);
            }
        }
        ///////

        // $client = new Client(['proxy' => 'http://uc28a3ecf573f05d0-zone-custom-region-sy-asn-AS29256:uc28a3ecf573f05d0@43.153.237.55:2334']);
        $client = new Client();
        try{
        $response = $client->request('POST', 'https://cash-api.syriatel.sy/Wrapper/app/7/SS2MTLGSM/ePayment/customerHistory', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'User-Agent' => 'Dalvik/2.1.0 (Linux; U; Android 11; SM-A505F Build/RP1A.200720.012)',
                'Host' => 'cash-api.syriatel.sy',
                'Connection' => 'Keep-Alive',
                'Accept-Encoding' => 'gzip'
            ],
            'body' => 'appVersion=5.5.2&pageNumber=1&searchGsmOrSecret=&type=2&systemVersion=Android%2Bv11&deviceId=ffffffff-fa8d-e3ca-ffff-ffffef05ac4a&userId=1657180&sortType=1&mobileManufaturer=samsung&mobileModel=SM-A505F&channelName=4&lang=0&hash=cd939479d1e2c5e0dfb93b428825a77e467c1c890131508fe85199c6e6f6ed07&status=1'
        ]);
    }catch(GuzzleException $e){
        return response()->json(["status"=>"failedsy","message"=>"فشلت التحقق الآلي من عملية الدفع، الرجاء إعادة المحاولة"]);
    }
        $body = json_decode($response->getBody()->getContents());
        if($body->code==1){
                $data =  $body->data->data;
                $desiredAmount = $form['amount'];
                $desiredTransactionNo = $form['processid'];

                // تحقق من وجود العنصر الذي يحتوي على القيمتين المحددتين
                $found = false;
                $matchedAmount = null;
                foreach ($data as $item) {
                    if ($item->amount == $desiredAmount && $item->transactionNo == $desiredTransactionNo) {
                        $found = true;
                        $matchedAmount = $item->amount;
                        break;
                    }
                }
                if($found){
                    $form['status']='complete';
                    $charge = Charge::create($form);
                        if($charge){
                            if($desiredAmount>=5000){
                                $chat = Chat::find($form['chat_id']);
                                $chat->balance = $chat->balance +$matchedAmount;
                                $chat->save();
                                return response()->json(["status"=>"success","message"=>"شكراً لك، تم شحن رصيدك في البوت بنجاح."]);
                            }else{
                                return response()->json(["status"=>"success","message"=>"أقل قيمة للشحن هي 5000 وأي قيمة أقل من 5000 لايمكن شحنها أو استرجاعها"]);
                            }
                        }else{
                                return response()->json(["status"=>"failed","message"=>"حدث خطأ أثناء الطلب الرجاء المحاولة مرة أخرى"]);
                        }
                }else{
                    return response()->json(["status"=>"failed","message"=>"عملية الدفع غير صحيحة" ]);
                }
        }else{
            return response()->json(["status"=>"failed","message"=>"فشل التحقق من عملية الدفع، الرجاء التواصل مع الدعم" ]);
        }
    }
*/
