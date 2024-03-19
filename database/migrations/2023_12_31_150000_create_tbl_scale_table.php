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
            $table->string('name', 50);
            $table->string('label', 10);
        });

        $scales = [
            [
                'name' => 'Meter',
                'label' => 'M'
            ],
            [
                'name' => 'Centimeter',
                'label' => 'Cm'
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
