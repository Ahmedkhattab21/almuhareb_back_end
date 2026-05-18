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
        Schema::create('ai_suggestions', function (Blueprint $table) {
                   $table->id();

            $table->foreignId('message_id')
                ->constrained('ticket_messages')
                ->cascadeOnDelete();

            $table->longText('suggested_reply');

            $table->string('suggested_language', 10)->default('ar');

            $table->string('status')->default('pending');
            // pending, accepted, rejected, edited

            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->index('message_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_suggestions');
    }
};
