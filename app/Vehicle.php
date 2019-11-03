<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Vehicle extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'vehicleTypes';

    protected $fillable = [
        'category', 'size', 'tariffs'
    ];
    
    public $timestamps = false;

}
