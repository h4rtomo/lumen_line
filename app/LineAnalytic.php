<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class LineAnalytic extends Model 
{
    protected $collection = 'line_analytics';

	public $primaryKey = '_id';
}