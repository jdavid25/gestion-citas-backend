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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->unsignedBigInteger('persona_id');
            $table->unsignedBigInteger('agenda_id');
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();
            $table->foreign('persona_id')
                    ->references('id')->on('personas');
            $table->foreign('agenda_id')
                    ->references('id')->on('agendas');
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
        Schema::dropIfExists('citas');
    }
};
