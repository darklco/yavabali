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
            $table->integer('year')->nullable();
            $table->integer('tons')->nullable();
            $table->integer('employeses')->nullable();
            $table->decimal('leaders_percentage', 5, 2);
            $table->decimal('workers_percentage', 5, 2);
            $table->integer('glycemic_index');
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
