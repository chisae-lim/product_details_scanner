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
        Schema::create('tbl_permission', function (Blueprint $table) {
            $table->integer('id_permission', true);
            $table->string('permission', 50);
            $table->enum('status', ['ENABLED', 'DISABLED'])->default('ENABLED');
        });

        $permissions = [
            [
                'id_permission' => 1,
                'permission' => 'God Mode',
            ],
            [
                'id_permission' => 2,
                'permission' => 'Manage Users',
            ],
            [
                'id_permission' => 3,
                'permission' => 'Manage Products',
            ],
        ];
        foreach ($permissions as $permission) {
            DB::table('tbl_permission')->insert($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_permission');
    }
};
