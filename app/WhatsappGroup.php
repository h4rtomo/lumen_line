<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
class WhatsappGroup extends Model 
{
    protected $collection = 'whatsapp_groups';

	public $primaryKey = '_id';
    
}