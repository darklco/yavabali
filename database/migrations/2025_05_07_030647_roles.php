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
        schema::create('roles', function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'guard_name']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
