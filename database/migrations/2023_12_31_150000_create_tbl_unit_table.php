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
            $table->string('name', 50);
            $table->string('label', 10);
        });

        $units = [
            [
                'name' => 'Piece',
                'label' => 'Piece'
            ],
            [
                'name' => 'Pack',
                'label' => 'Pack'
            ],
            [
                'name' => 'Set',
                'label' => 'Set'
            ],
            [
                'name' => 'Pair',
                'label' => 'Pair'
            ],
            [
                'name' => 'Dozen',
                'label' => 'Dozen'
            ],
            [
                'name' => 'Case',
                'label' => 'Case'
            ],
            [
                'name' => 'Box',
                'label' => 'Box'
            ],
            [
                'name' => 'Meter',
                'label' => 'M'
            ],
            [
                'name' => 'Centimeter',
                'label' => 'Cm'
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
