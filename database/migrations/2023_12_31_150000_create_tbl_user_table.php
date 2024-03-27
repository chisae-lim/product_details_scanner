<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_user', function (Blueprint $table) {
            $table->integer('id_user', true);
            $table->string('name', 50);
            $table->string('username', 50)->unique('username');
            $table->string('password', 50);
            $table->string('auth_token', 50);
            $table->string('rem_token', 50)->nullable();
            $table->string('rem_expiry', 50)->nullable();
            $table->enum('acc_status', ['ENABLED', 'DISABLED'])->default('ENABLED');
            $table->enum('act_status', ['ALLOWED', 'DENIED'])->default('ALLOWED');
            $table
                ->integer('created_by')
                ->index('created_by');
            $table
                ->integer('updated_by')
                ->index('updated_by');
            $table->timestamps();
        });

        $users = [
            [
                'name' => 'Master Master',
                'username' => md5(md5(md5('chisae123'))),
                'password' => md5(md5(md5('chisae123'))),
                'auth_token' => md5(md5(md5(md5(md5(md5('chisae'))) . md5(md5(md5('chisae')))))),
                // 'level' => 'Admin',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon\Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'Admin',
                'username' => md5(md5(md5('admin'))),
                'password' => md5(md5(md5('cdf@8888'))),
                'auth_token' => md5(md5(md5(md5(md5(md5('admin'))) . md5(md5(md5('admin')))))),
                // 'level' => 'Admin',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon\Carbon::now()->toDateTimeString(),
            ],
        ];
        foreach ($users as $user) {
            DB::table('tbl_user')->insert($user);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_user');
    }
};
