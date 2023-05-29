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
        Schema::create('especificaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('atributo_id');
            $table->foreign('atributo_id')->references('id')->on('atributos');
            $table->string('valor');
            $table->string('clave_ct');
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
        Schema::dropIfExists('especificaciones');
    }
};
