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
        Schema::create('tickets', function (Blueprint $table) {
              $table->id();

            $table->foreignId('worker_id')
                ->constrained('workers')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->foreignId('lawyer_id')
                ->nullable()
                ->constrained('lawyers')
                ->nullOnDelete();

            $table->string('title');

            $table->string('status')->default('open');
            // open, pending, in_progress, closed

            $table->string('priority')->default('medium');
            // low, medium, high, urgent

            $table->string('last_message_preview')->nullable();
            $table->timestamp('last_message_at')->nullable();

            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index('worker_id');
            $table->index('company_id');
            $table->index('lawyer_id');
            $table->index('status');
            $table->index('priority');
            $table->index('last_message_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
