<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('usr_no'); // المفتاح الأساسي
            $table->string('usr_nom'); // اسم المستخدم
            $table->string('usr_pas'); // كلمة المرور (hashed)
            $table->integer('usr_pntg')->nullable();
            $table->unsignedBigInteger('usr_lemp')->nullable();

            // الأعمدة المهمة لنظام Laravel Auth + Sanctum
            $table->rememberToken(); // remember_token
            $table->timestamps(); // created_at + updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
