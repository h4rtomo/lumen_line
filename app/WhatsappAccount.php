<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
class WhatsappAccount extends Model 
{
    protected $collection = 'whatsapp_accounts';

	public $primaryKey = '_id';
    
}