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
        Schema::table('tbl_product_color', function (Blueprint $table) {
            $table
                ->foreign(['id_product'], 'product_color_ibfk_1')
                ->references(['id_product'])
                ->on('tbl_product')
                ->onDelete('cascade');
            $table
                ->foreign(['id_color'], 'product_color_ibfk_2')
                ->references(['id_color'])
                ->on('tbl_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_product_color', function (Blueprint $table) {
            $table->dropForeign('product_color_ibfk_1');
            $table->dropForeign('product_color_ibfk_2');
        });
    }
};
