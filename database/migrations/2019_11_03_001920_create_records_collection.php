<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsCollection extends Migration
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
        ->create('records', function (Blueprint $collection) 
        {
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
        ->table('records', function (Blueprint $collection) 
        {
            $collection->drop();
        });
    }
}
