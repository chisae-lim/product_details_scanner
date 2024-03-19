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
        Schema::table('tbl_variant', function (Blueprint $table) {
            $table
                ->foreign(['id_product'], 'variant_ibfk_1')
                ->references(['id_product'])
                ->on('tbl_product')
                ->onDelete('cascade');
            $table
                ->foreign(['id_color'], 'variant_ibfk_2')
                ->references(['id_color'])
                ->on('tbl_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_variant', function (Blueprint $table) {
            $table->dropForeign('variant_ibfk_1');
            $table->dropForeign('variant_ibfk_2');
        });
    }
};
