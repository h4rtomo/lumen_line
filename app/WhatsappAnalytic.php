<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class WhatsappAnalytic extends Model 
{
    protected $collection = 'whatsapp_analytics';

	public $primaryKey = '_id';
}