<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleCollection extends Migration
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
        ->create('vehicleTypes', function (Blueprint $collection) {
        });

        $data = [
            [
                'category'=> 'A',
                'size' => 1,
                'tariffs' => [
                    'daily' => 3,
                    'nightly'=> 2
                ]
            ],
            [
                'category'=> 'B',
                'size' => 2,
                'tariffs' => [
                    'daily' => 6,
                    'nightly'=> 4
                ]
            ],
            [
                'category'=> 'C',
                'size' => 4,
                'tariffs' => [
                    'daily' => 12,
                    'nightly'=> 8
                ]
            ],
        ];

        DB::connection($this->connection)->table('vehicleTypes')->raw( function ( $collection ) use ($data) {
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
        ->table('vehicleTypes', function (Blueprint $collection) 
        {
            $collection->drop();
        });
    }
}
