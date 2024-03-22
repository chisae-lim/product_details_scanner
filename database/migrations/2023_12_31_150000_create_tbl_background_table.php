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
        Schema::create('tbl_background', function (Blueprint $table) {
            $table->integer('id_background', true);
            $table->string('name', 50);
            $table->enum('status', ['ENABLED', 'DISABLED'])->default('DISABLED');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_background');
    }
};
