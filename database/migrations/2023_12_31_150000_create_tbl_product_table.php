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
            $table->string('p_code', 50);
            $table->string('bar_code', 50);
            $table->string('name_en', 150);
            $table->string('name_ch', 150);
            $table->decimal('price', 10, 3);
            $table->string('description', 500)->nullable();
            // $table->integer('length');
            // $table->integer('width');
            // $table->integer('height');
            // $table->integer('id_scale')->index('id_scale');
            $table->integer('id_unit')->index('id_unit');
            $table->integer('id_category')->index('id_category')->nullable();
            $table->integer('id_brand')->index('id_brand')->nullable();
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
