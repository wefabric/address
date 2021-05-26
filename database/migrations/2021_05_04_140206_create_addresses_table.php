<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('street');
            $table->integer('housenumber');
            $table->string('housenumber_addition')->nullable();
            $table->string('postcode');
            $table->string('city');
            $table->string('country_id')->default('NL');
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->decimal('latitude',8,6)->nullable();
            $table->decimal('longitude',9,6)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
