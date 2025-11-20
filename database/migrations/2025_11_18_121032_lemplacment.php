<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lemplacement', function (Blueprint $table) {
            $table->id('lemp_no'); // primary key
            $table->string('lemp_nom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lemplacement');
    }
};
