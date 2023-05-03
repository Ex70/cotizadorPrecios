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
        Schema::table('woocommerce', function (Blueprint $table) {
            $table->decimal('precio_venta',10,2)->after('clave_ct')->nullable();
            $table->decimal('precio_venta_rebajado',10,2)->after('precio_venta')->nullable();
            $table->date('fecha_inicio')->after('precio_venta_rebajado')->nullable();
            $table->date('fecha_fin')->after('fecha_inicio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('woocommerce', function (Blueprint $table) {
            $table->dropColumn('precio_venta');
            $table->dropColumn('precio_venta_rebajado');
            $table->dropColumn('fecha_inicio');
            $table->dropColumn('fecha_fin');
        });
    }
};
