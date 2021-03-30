<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class LineChatHastag extends Model 
{
    protected $collection = 'line_chat_hastags';

	public $primaryKey = '_id';
}