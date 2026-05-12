<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lawyers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('admin')
                ->nullOnDelete();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');

            $table->string('status')->default('active');

            $table->string('avatar')->nullable();
            $table->string('preferred_language')->default('ar');
            $table->decimal('rating', 3, 1)->default(0);
            $table->unsignedInteger('avg_response_minutes')->default(0);
            $table->unsignedInteger('active_cases_count')->default(0);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('admin')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lawyers');
    }
};
