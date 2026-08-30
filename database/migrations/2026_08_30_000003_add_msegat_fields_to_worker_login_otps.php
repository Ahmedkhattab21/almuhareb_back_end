<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_login_otps', function (Blueprint $table) {
            if (! Schema::hasColumn('worker_login_otps', 'provider')) {
                $table->string('provider')->default('msegat')->after('code_hash');
            }

            if (! Schema::hasColumn('worker_login_otps', 'provider_request_id')) {
                $table->string('provider_request_id')->nullable()->after('provider');
            }

            if (! Schema::hasColumn('worker_login_otps', 'language')) {
                $table->string('language', 5)->default('Ar')->after('provider_request_id');
            }

            if (! Schema::hasColumn('worker_login_otps', 'status')) {
                $table->string('status')->default('pending')->after('language');
            }

            if (! Schema::hasColumn('worker_login_otps', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('used_at');
            }

            if (! Schema::hasColumn('worker_login_otps', 'invalidated_at')) {
                $table->timestamp('invalidated_at')->nullable()->after('verified_at');
            }

            if (! Schema::hasColumn('worker_login_otps', 'metadata')) {
                $table->json('metadata')->nullable()->after('invalidated_at');
            }
        });

        Schema::table('worker_login_otps', function (Blueprint $table) {
            $table->index(['phone', 'status', 'created_at'], 'worker_login_otps_phone_status_created_index');
            $table->index(['provider', 'provider_request_id'], 'worker_login_otps_provider_request_index');
        });
    }

    public function down(): void
    {
        Schema::table('worker_login_otps', function (Blueprint $table) {
            $table->dropIndex('worker_login_otps_phone_status_created_index');
            $table->dropIndex('worker_login_otps_provider_request_index');
            $table->dropColumn([
                'provider',
                'provider_request_id',
                'language',
                'status',
                'verified_at',
                'invalidated_at',
                'metadata',
            ]);
        });
    }
};
