<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_product', function (Blueprint $table) {
            $table
                ->foreign(['id_category'], 'product_ibfk_1')
                ->references(['id_category'])
                ->on('tbl_category');
            $table
                ->foreign(['id_brand'], 'product_ibfk_2')
                ->references(['id_brand'])
                ->on('tbl_brand');
            $table
                ->foreign(['id_scale'], 'product_ibfk_3')
                ->references(['id_scale'])
                ->on('tbl_scale');
            $table
                ->foreign(['id_unit'], 'product_ibfk_4')
                ->references(['id_unit'])
                ->on('tbl_unit');
            $table
                ->foreign(['created_by'], 'product_ibfk_10')
                ->references(['id_user'])
                ->on('tbl_user');
            $table
                ->foreign(['updated_by'], 'product_ibfk_20')
                ->references(['id_user'])
                ->on('tbl_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_product', function (Blueprint $table) {
            $table->dropForeign('product_ibfk_1');
            $table->dropForeign('product_ibfk_2');
            $table->dropForeign('product_ibfk_3');
            $table->dropForeign('product_ibfk_4');
            $table->dropForeign('product_ibfk_10');
            $table->dropForeign('product_ibfk_20');
        });
    }
};
