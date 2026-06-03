<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ai_suggestions', 'audio_path')) {
            Schema::table('ai_suggestions', function (Blueprint $table) {
                $table->string('audio_path')->nullable()->after('suggested_reply');
            });
        }

        if (! Schema::hasColumn('ai_suggestions', 'audio_language')) {
            Schema::table('ai_suggestions', function (Blueprint $table) {
                $table->string('audio_language', 10)->nullable()->after('audio_path');
            });
        }

        if (! Schema::hasColumn('ai_suggestions', 'audio_generated_at')) {
            Schema::table('ai_suggestions', function (Blueprint $table) {
                $table->timestamp('audio_generated_at')->nullable()->after('audio_language');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ai_suggestions', function (Blueprint $table) {
            if (Schema::hasColumn('ai_suggestions', 'audio_generated_at')) {
                $table->dropColumn('audio_generated_at');
            }

            if (Schema::hasColumn('ai_suggestions', 'audio_language')) {
                $table->dropColumn('audio_language');
            }

            if (Schema::hasColumn('ai_suggestions', 'audio_path')) {
                $table->dropColumn('audio_path');
            }
        });
    }
};
