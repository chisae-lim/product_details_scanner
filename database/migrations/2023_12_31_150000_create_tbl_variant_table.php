<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_variant', function (Blueprint $table) {
            $table->integer('id_variant', true);
            $table->integer('id_product')->index('id_product');
            $table->integer('id_color')->index('id_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_variant');
    }
};
