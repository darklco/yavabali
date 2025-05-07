<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema ::create('mission_statistics', function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->year('year')->nullable();
            $table->integer('total_raw_materials')->comment('Total raw materials sourced (in tons)');
            $table->integer('total_employees')->comment('Number of employees');
            $table->decimal('female_leaders_percentage', 5, 2)->comment('Percentage of female leaders');
            $table->decimal('female_workers_percentage', 5, 2)->comment('Percentage of female workers');
            $table->integer('glycemic_index')->comment('Glycemic index of the product');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_statistics');
    }
};