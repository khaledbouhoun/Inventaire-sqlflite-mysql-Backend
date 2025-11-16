<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('prd_no'); // primary key
            $table->string('prd_nom');
            $table->text('prd_qr')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
