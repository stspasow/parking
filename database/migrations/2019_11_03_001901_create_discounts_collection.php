<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsCollection extends Migration
{
    protected $connection = 'mongodb';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)
        ->create('discounts', function (Blueprint $collection) 
        {
        });

        $data = [
            [
                'type' => 'silver',
                'percent' => 0.1
            ],
            [
                'type' => 'gold',
                'percent' => 0.15
            ],
            [
                'type' => 'platinum',
                'percent' => 0.2
            ],
        ];

        DB::connection($this->connection)->table('discounts')->raw( function ( $collection ) use ($data) {
            return $collection->insertMany($data);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)
        ->table('discounts', function (Blueprint $collection) 
        {
            $collection->drop();
        });
    }
}
