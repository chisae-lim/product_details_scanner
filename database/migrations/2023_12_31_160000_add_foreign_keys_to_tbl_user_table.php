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
        Schema::table('tbl_user', function (Blueprint $table) {
            $table
                ->foreign(['created_by'], 'users_ibfk_10')
                ->references(['id_user'])
                ->on('tbl_user');
            $table
                ->foreign(['updated_by'], 'users_ibfk_20')
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
        Schema::table('tbl_user', function (Blueprint $table) {
            $table->dropForeign('users_ibfk_10');
            $table->dropForeign('users_ibfk_20');
        });
    }
};
