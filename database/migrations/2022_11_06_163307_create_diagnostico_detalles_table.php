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
        Schema::create('diagnostico_detalles', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->string('descripcion');
            $table->unsignedBigInteger('diagnostico_id');
            $table->unsignedBigInteger('medicamento_id');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();
            $table->foreign('diagnostico_id')
                    ->references('id')->on('diagnosticos');
            $table->foreign('medicamento_id')
                    ->references('id')->on('medicamentos');
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
        Schema::dropIfExists('diagnostico_detalles');
    }
};
