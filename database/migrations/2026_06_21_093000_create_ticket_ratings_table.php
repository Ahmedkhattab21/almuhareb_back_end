<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ticket_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('lawyer_id')->nullable()->constrained('lawyers')->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->unique('ticket_id');
            $table->index(['worker_id', 'created_at']);
            $table->index(['lawyer_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_ratings');
    }
};
