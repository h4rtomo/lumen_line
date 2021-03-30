<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
class Line extends Model 
{

    public static function saveChat($data){
        if(isset($data['message']) && isset($data['message']['text'])){
            $chat = new LineChat();
            $chat->text_chat = $data['message']['text'];
            $chat->chat_id = isset($data['message']['id']) ? $data['message']['id'] : "";
            $chat->group_id = isset($data['source']['groupId']) ? $data['source']['groupId'] : "";
            $chat->user_id = isset($data['source']['userId']) ? $data['source']['userId'] : "";
            $chat->source_type = isset($data['source']['type']) ? $data['source']['type'] : "";
            $chat->chat_date = isset($data['timestamp']) ? $data['timestamp'] : "";
            $chat->save();

            Line::saveHastagChat($chat->text_chat, $chat->chat_id);

        }
    }

    public static function saveHastagChat($text_chat, $chat_id){
        $list_hastag = Line::getHastag($text_chat);

        foreach ($list_hastag as $hastag) {
            $hastag = strtolower($hastag);
            $hastag_chat = LineChatHastag::where('chat_id', $chat_id)->where('hastag', $hastag)->first();
            if(!$hastag_chat){
                $hastag_chat = new LineChatHastag();
                $hastag_chat->chat_id = $chat_id;
                $hastag_chat->hastag = $hastag;
                $hastag_chat->save();
            }
        }
    }

    public static function saveAnalytic($data){
        $analytic = new LineAnalytic();
        
        $analytic->hastag = $data['hastag'];
        $analytic->total_chat = $data['total_chat'];
        $analytic->total_user = $data['total_user'];
        $analytic->total_group = $data['total_group'];
        $analytic->count_chat_7day = $data['count_chat_7day'];
        $analytic->total_user_7day = $data['total_user_7day'];
        $analytic->save();
    }

    public static function getHastag($string){
        preg_match_all("/(#\w+)/", $string, $matches);
        $hastag = array();
        if ($matches) {
            $hashtagsArray = array_count_values($matches[0]);
            $hashtags = array_keys($hashtagsArray);
        }
        return $hashtags;
    }
}