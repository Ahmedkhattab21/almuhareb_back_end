<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('admin')
                ->nullOnDelete();
            $table->string('name');
            $table->string('status', 30)->default('active');
            $table->timestamps();

            $table->unique('name');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
