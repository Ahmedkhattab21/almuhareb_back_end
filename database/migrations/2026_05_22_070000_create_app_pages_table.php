<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admin')->nullOnDelete();
            $table->string('type')->unique();
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_pages');
    }
};
