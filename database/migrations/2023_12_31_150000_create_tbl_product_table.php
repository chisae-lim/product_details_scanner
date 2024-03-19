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
        Schema::create('tbl_product', function (Blueprint $table) {
            $table->integer('id_product', true);
            $table->string('p_code', 250);
            $table->string('bar_code', 250);
            $table->string('name_en', 250);
            $table->string('name_ch', 250);
            $table->decimal('price', 10, 3);
            $table->string('description', 500);
            // $table->integer('length');
            // $table->integer('width');
            // $table->integer('height');
            // $table->integer('id_scale')->index('id_scale');
            $table->integer('id_unit')->index('id_unit');
            $table->integer('id_category')->index('id_category');
            $table->integer('id_brand')->index('id_brand');
            $table->integer('created_by')->index('created_by');
            $table->integer('updated_by')->index('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_product');
    }
};
