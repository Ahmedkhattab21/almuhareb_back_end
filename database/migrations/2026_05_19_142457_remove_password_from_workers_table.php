<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('workers', 'password')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->dropColumn('password');
            });
        }
    }

    public function down()
    {
        if (! Schema::hasColumn('workers', 'password')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->string('password')->nullable()->after('phone');
            });
        }
    }
};
