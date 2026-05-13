<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
               ->constrained('companies')
               ->cascadeOnDelete();

            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('iqama_number')->nullable()->unique();

            $table->foreignId('position_id')
    ->nullable()
    ->constrained('positions')
    ->nullOnDelete();
            $table->string('image')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workers');
    }
};
