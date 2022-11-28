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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento');
            $table->string('num_documento');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email');
            $table->string('direccion');
            $table->string('telefono');
            $table->unsignedBigInteger('municipio_id');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();
            $table->foreign('municipio_id')
                    ->references('id')->on('municipios');
            $table->foreign('estado_id')
                    ->references('id')->on('estados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
};
