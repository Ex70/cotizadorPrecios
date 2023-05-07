<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volumetria', function (Blueprint $table) {
            $table->id();
            $table->string('clave_ct');
            $table->decimal('peso',10,2)->nullable();
            $table->decimal('largo',10,2)->nullable();
            $table->decimal('alto',10,2)->nullable();
            $table->decimal('ancho',10,2)->nullable();
            $table->string('upc')->nullable();
            $table->string('ean')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('volumetria');
    }
};
