<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class LineAccount extends Model 
{
    protected $collection = 'line_accounts';

	public $primaryKey = '_id';

}