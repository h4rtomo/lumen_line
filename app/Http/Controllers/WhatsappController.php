<?php

namespace App\Http\Controllers;

use App\Whatsapp;
use App\WhatsappChat;
use Carbon\Carbon;
use App\WhatsappChatHastag;
use Illuminate\Http\Request;


class WhatsappController extends Controller
{

    public function getChat(Request $request){
        $data = $request->get('chat', null);
        $body 	   = file_get_contents('php://input');
        $fp = fopen("/var/www/html/linebot/storage/logs/chat_wa.log", "a");
        fwrite($fp, "body : " . $body . "\n");
        if($data != null){
            Whatsapp::saveChat($data);
        }
    }

    public function listHastag(){
        $data = WhatsappChatHastag::groupBy('hastag')->get()->pluck('hastag');
        return response()->json([
            'data' => $data,
            'count' => count($data)
        ]);
    }

    public function getAnalitycsByHastag(Request $request){
        $hastag = "#".$request->get('hastag');
        $list = WhatsappChatHastag::where('hastag', $hastag)->get()->pluck('chat_id');
        
        $list_chat = WhatsappChat::whereIn('chat_id', $list)->get();

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

        Whatsapp::saveAnalytic($data);

        return response()->json([
            'data' => $data,
            'hastag' => $hastag
        ]);
    }
    
}