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
        Schema::create('lawyers', function (Blueprint $table) {
            $table->id();
            // الأدمن المسؤول عن المحامي لو محتاج تربطه بإدارة معينة
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('admin')
                ->nullOnDelete();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            // لو المحامي هيسجل دخول من لوحة المحامي
            $table->string('password');

            $table->string('status')->default('active');
            $table->string('preferred_language')->default('ar');

              $table->string('license_number')->nullable()->unique() ;
            $table->string('specialization')->nullable() ;
            $table->string('avatar')->nullable() ;

            $table->decimal('rating', 3, 1)->default(0) ;
            $table->unsignedInteger('avg_response_minutes') ;
            $table->unsignedInteger('active_cases_count')->default(0) ;


            // مين الأدمن اللي أنشأ المحامي
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('admin')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lawyers', function (Blueprint $table) {
            $table->dropColumn([
                'license_number',
                'specialization',
                'avatar',
                'rating',
                'avg_response_minutes',
                'active_cases_count',
            ]);
        });
    }
};
