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
        Schema::create('news', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('title')->nullable();
            $table->string('slug')->nullable();
            $table->json('excerpt')->nullable();
            $table->json('content')->nullable();
            $table->string('media_url')->nullable();
            $table->json('author')->nullable();
            $table->boolean('is_highlight')->default(0);
            $table->timestamps(); 
            $table->softDeletes(); 

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
