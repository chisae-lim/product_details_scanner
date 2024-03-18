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
        Schema::create('tbl_unit', function (Blueprint $table) {
            $table->integer('id_unit', true);
            $table->string('unit', 50);
            $table->string('short_name', 10);
        });

        $units = [
            [
                'unit' => 'Piece',
                'short_name' => 'Piece'
            ],
            [
                'unit' => 'Pack',
                'short_name' => 'Pack'
            ],
            [
                'unit' => 'Set',
                'short_name' => 'Set'
            ],
            [
                'unit' => 'Pair',
                'short_name' => 'Pair'
            ],
            [
                'unit' => 'Dozen',
                'short_name' => 'Dozen'
            ],
            [
                'unit' => 'Case',
                'short_name' => 'Case'
            ],
            [
                'unit' => 'Box',
                'short_name' => 'Box'
            ],
            [
                'unit' => 'Meter',
                'short_name' => 'M'
            ],
            [
                'unit' => 'Centimeter',
                'short_name' => 'Cm'
            ],
        ];
        foreach ($units as $unit) {
            DB::table('tbl_unit')->insert($unit);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_unit');
    }
};
