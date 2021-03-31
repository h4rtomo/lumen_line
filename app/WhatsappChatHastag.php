<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class WhatsappChatHastag extends Model 
{
    protected $collection = 'whatsapp_chat_hastags';

	public $primaryKey = '_id';
}