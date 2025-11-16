<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('usr_id'); // primary key
            $table->string('usr_nom');
            $table->string('usr_pas');
            $table->string('usr_pntg')->nullable(); // pointage
            $table->string('usr_dpot')->nullable(); // depot
            $table->rememberToken(); // for "remember me" login
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
