<?php

namespace App\Http\Controllers;

use App\Line;
use App\LineChat;
use Carbon\Carbon;
use App\LineChatHastag;


class LineController extends Controller
{

    public function getChat(){
        $body 	   = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

        // log body and signature
        file_put_contents('php://stderr', 'Body: '.$body);

        // is LINE_SIGNATURE exists in request header?
        if (empty($signature)){
            return $response->withStatus(400, 'Signature not set');
        }

        // // is this request comes from LINE?
        // if(env('PASS_SIGNATURE') == false && ! SignatureValidator::validateSignature($body, $_ENV['LINE_BOT_CHANNEL_SECRET'], $signature)){
        // 	return $response->withStatus(400, 'Invalid signature');
        // }

        // init bot
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => env('LINE_BOT_CHANNEL_SECRET')]);
        $fp = fopen("/var/www/html/linebot/storage/logs/event_line.log", "a");
        fwrite($fp, "body : " . $body . "\n");
        $data = json_decode($body, true);
        foreach ($data['events'] as $event)
        {
            Line::saveChat($event);
            //$userMessage = $event['message']['text'];
            // if(strtolower($userMessage) == 'halo')
            // {
            // 	$message = "Halo juga";
            //     $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
            // 	$result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
            // 	return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            
            // }
        }
    }

    public function listHastag(){
        $data = LineChatHastag::groupBy('hastag')->get()->pluck('hastag');
        return response()->json([
            'data' => $data,
            'count' => count($data)
        ]);
    }

    public function getAnalitycsByHastag(Request $request){
        $hastag = "#".$request->get('hastag');
        $list = LineChatHastag::where('hastag', $hastag)->get()->pluck('chat_id');
        
        $list_chat = LineChat::whereIn('chat_id', $list)->get();

        $total_chat = count($list_chat);
        $array_group = array();
        $array_user = array();

        $count_chat_7day = 0;
        $array_user_7day = array();

        $date_now = Carbon::now()->timezone('Asia/Jakarta')->subDay(7)->format('Y-m-d');
        foreach ($list_chat as $chat) {
            if(!in_array($chat->group_id, $array_group)){
                array_push($array_group, $chat->group_id);
            }
            if(!in_array($chat->user_id, $array_user)){
                array_push($array_user, $chat->user_id);
            }
            try {
                $timestamp = $chat->chat_date;
                $chat_date = gmdate("Y-m-d H:i:s", $timestamp/1000);
                $chat_date = Carbon::parse($chat_date)->addHour(7)->format('Y-m-d');
                if($chat_date >= $date_now){
                    $count_chat_7day++;
                    
                    if(!in_array($chat->user_id, $array_user_7day)){
                        array_push($array_user_7day, $chat->user_id);
                    }
                }
            } catch (\Throwable $th) {}
        }

        $total_group = count($array_group);
        $total_user = count($array_user);

        $total_user_7day = count($array_user_7day);

        $data = array(
            'hastag' => $hastag,
            'total_chat' => $total_chat,
            'total_user' => $total_user,
            'total_group' => $total_group,
            'count_chat_7day' => $count_chat_7day,
            'total_user_7day' => $total_user_7day,
        );

        Line::saveAnalytic($data);

        return response()->json([
            'data' => $data,
            'hastag' => $hastag
        ]);
    }
    
}