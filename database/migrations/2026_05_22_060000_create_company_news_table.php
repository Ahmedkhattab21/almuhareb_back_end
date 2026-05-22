<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admin')->nullOnDelete();
            $table->foreignId('created_by_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('title');
            $table->string('image')->nullable();
            $table->text('description');
            $table->timestamps();

            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_news');
    }
};
