<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestqr', function (Blueprint $table) {

            $table->unsignedInteger('gqr_no');
            $table->unsignedBigInteger('gqr_lemp_no');
            $table->unsignedBigInteger('gqr_usr_no');
            $table->string('gqr_prd_no');
            $table->dateTime('gqr_date')->useCurrent();

            $table->primary(['gqr_lemp_no', 'gqr_usr_no', 'gqr_no']);

            $table->foreign('gqr_lemp_no')->references('lemp_no')->on('lemplacement')->onDelete('cascade');
            $table->foreign('gqr_usr_no')->references('usr_no')->on('users')->onDelete('cascade');
            $table->foreign('gqr_prd_no')->references('prd_no')->on('products')->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('gestqr');
    }
};
