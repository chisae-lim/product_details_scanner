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
        Schema::create('tbl_product_color', function (Blueprint $table) {
            $table->integer('id_product_color', true);
            $table->integer('id_product')->index('id_product');
            $table->integer('id_color')->index('id_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_product_color');
    }
};
