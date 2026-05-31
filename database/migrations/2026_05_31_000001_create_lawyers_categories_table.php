<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lawyers_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->foreignId('lawyer_id')
                ->constrained('lawyers')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['lawyer_id', 'category_id']);
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lawyers_categories');
    }
};
