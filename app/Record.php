<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Record extends Eloquent
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'records';
    
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'plateNumber', 'entryTime', 'vehicleType', 'discount'
    ];

    public $timestamps = false;

    public function vehicleType()
    {
        return $this->embedsOne(Vehicle::class);
    }

    public function discount()
    {
        return $this->embedsOne(Discount::class);
    }
}
