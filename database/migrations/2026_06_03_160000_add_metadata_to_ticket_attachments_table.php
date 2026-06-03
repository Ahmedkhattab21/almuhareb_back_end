<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    Schema::table('ai_suggestions', function (Blueprint $table) {
        if (! Schema::hasColumn('ai_suggestions', 'audio_path')) {
            $table->string('audio_path')->nullable()->after('suggested_reply');
        }

        if (! Schema::hasColumn('ai_suggestions', 'audio_generated_at')) {
            $table->timestamp('audio_generated_at')->nullable()->after('audio_path');
        }
    });
}

public function down(): void
{
    Schema::table('ai_suggestions', function (Blueprint $table) {
        if (Schema::hasColumn('ai_suggestions', 'audio_path')) {
            $table->dropColumn('audio_path');
        }

        if (Schema::hasColumn('ai_suggestions', 'audio_generated_at')) {
            $table->dropColumn('audio_generated_at');
        }
    });
}

};
