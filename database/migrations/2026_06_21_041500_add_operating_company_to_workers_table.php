<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('workers', function (Blueprint $table) {
            if (! Schema::hasColumn('workers', 'operating_company')) {
                $table->string('operating_company')->nullable()->after('company_id');
            }
        });
    }

    public function down()
    {
        Schema::table('workers', function (Blueprint $table) {
            if (Schema::hasColumn('workers', 'operating_company')) {
                $table->dropColumn('operating_company');
            }
        });
    }
};
