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
        Schema::create('tbl_user_permission', function (Blueprint $table) {
            $table->integer('id_user_permission', true);
            $table->integer('id_user')->index('id_user');
            $table->integer('id_permission')->index('id_permission');
        });
        $user_permissions = [
            [
                'id_user' => 1,
                'id_permission' => 1,
            ],
            [
                'id_user' => 2,
                'id_permission' => 1,
            ],
        ];
        foreach ($user_permissions as $user_permission) {
            DB::table('tbl_user_permission')->insert($user_permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_user_permission');
    }
};
