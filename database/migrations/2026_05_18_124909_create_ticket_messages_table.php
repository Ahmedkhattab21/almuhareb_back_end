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
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            $table->string('sender_type', 30);
            // worker, company, lawyer, admin, ai

            $table->unsignedBigInteger('sender_id')->nullable();

            $table->unsignedInteger('message_order');

            $table->longText('message_original');
            $table->longText('message_translated')->nullable();

            $table->string('original_language', 10)->nullable();
            $table->string('translated_language', 10)->nullable();

            $table->boolean('is_ai_generated')->default(false);

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->unique(['ticket_id', 'message_order']);

            $table->index(['ticket_id', 'created_at']);
            $table->index(['sender_type', 'sender_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_messages');
    }
};
