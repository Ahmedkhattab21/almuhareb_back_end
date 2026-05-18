<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('worker_login_otps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('worker_id')
                ->constrained('workers')
                ->cascadeOnDelete();

            $table->string('phone')->index();

            $table->string('code_hash');

            $table->unsignedTinyInteger('attempts')->default(0);

            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();

            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['worker_id', 'expires_at']);
            $table->index(['worker_id', 'used_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('worker_login_otps');
    }
};
