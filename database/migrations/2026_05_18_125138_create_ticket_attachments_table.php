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
        Schema::create('ticket_attachments', function (Blueprint $table) {
                 $table->id();

            $table->foreignId('message_id')
                ->constrained('ticket_messages')
                ->cascadeOnDelete();

            $table->string('file_name')->nullable();
            $table->string('file_path');
            $table->string('file_type')->nullable();

            $table->timestamps();

            $table->index('message_id');
            $table->index('file_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
