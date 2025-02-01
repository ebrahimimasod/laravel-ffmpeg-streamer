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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name');
            $table->string('path');
            $table->timestamp('converted_for_streaming_at')->nullable();
            $table->enum('visibility', ['public', 'private'])->default('private');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
