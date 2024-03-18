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
        Schema::table('tbl_user_permission', function (Blueprint $table) {
            $table
                ->foreign(['id_user'], 'permission_ibfk_1')
                ->references(['id_user'])
                ->on('tbl_user')
                ->onDelete('cascade');
            $table
                ->foreign(['id_permission'], 'permission_ibfk_2')
                ->references(['id_permission'])
                ->on('tbl_permission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_user_permission', function (Blueprint $table) {
            $table->dropForeign('permission_ibfk_1');
            $table->dropForeign('permission_ibfk_2');
        });
    }
};
