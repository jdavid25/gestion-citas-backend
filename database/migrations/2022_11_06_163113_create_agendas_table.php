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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora');
            $table->unsignedBigInteger('persona_id');
            $table->unsignedBigInteger('consultorio_id');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();
            $table->foreign('persona_id')
                    ->references('id')->on('personas');
            $table->foreign('consultorio_id')
                    ->references('id')->on('consultorios');
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
        Schema::dropIfExists('agendas');
    }
};
