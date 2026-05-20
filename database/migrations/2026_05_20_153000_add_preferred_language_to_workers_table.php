<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('workers', 'preferred_language')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->string('preferred_language', 20)->nullable()->default('ar');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('workers', 'preferred_language')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->dropColumn('preferred_language');
            });
        }
    }
};
