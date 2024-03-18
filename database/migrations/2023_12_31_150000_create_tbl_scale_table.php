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
        Schema::create('tbl_scale', function (Blueprint $table) {
            $table->integer('id_scale', true);
            $table->string('scale', 50);
            $table->string('short_name', 10);
        });

        $scales = [
            [
                'scale' => 'Meter',
                'short_name' => 'M'
            ],
            [
                'scale' => 'Centimeter',
                'short_name' => 'Cm'
            ],

        ];
        foreach ($scales as $scale) {
            DB::table('tbl_scale')->insert($scale);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_scale');
    }
};
