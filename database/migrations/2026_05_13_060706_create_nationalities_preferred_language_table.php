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
        Schema::create('nationalities_prefered_language', function (Blueprint $table) {
            $table->foreignId('worker_id')

                ->constrained('workers')
                ->cascadeOnDelete();

            $table->foreignId('nationality_id')
                ->constrained('nationalities')
                ->cascadeOnDelete();

            $table->foreignId('prefered_language_id')
                ->constrained('prefered_languages')
                ->cascadeOnDelete();

            $table->primary(
                ['worker_id', 'nationality_id', 'prefered_language_id'],
                'npl_primary'
            );
            $table->unique('worker_id', 'npl_worker_unique');

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
        Schema::dropIfExists('nationalities_prefered_language');
    }
};
