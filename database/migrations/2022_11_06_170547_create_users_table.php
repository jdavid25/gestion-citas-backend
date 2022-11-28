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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('persona_id');
            $table->unsignedBigInteger('perfil_id');
            $table->unsignedBigInteger('estado_id');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('persona_id')
                    ->references('id')->on('personas');
            $table->foreign('perfil_id')
                    ->references('id')->on('perfils');
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
        Schema::dropIfExists('users');
    }
};
