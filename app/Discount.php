<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Discount extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'discounts';
    
    protected $fillable = [
        'type', 'percent'
    ];

    public $timestamps = false;
}
