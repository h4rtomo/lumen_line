<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
class WhatsappChat extends Model 
{
    protected $collection = 'whatsapp_chats';

	public $primaryKey = '_id';
    
}