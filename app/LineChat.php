<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class LineChat extends Model 
{
    protected $collection = 'line_chats';

	public $primaryKey = '_id';
}