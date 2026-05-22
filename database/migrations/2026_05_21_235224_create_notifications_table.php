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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
   /*
             * الشخص اللي هيوصله التنبيه
             * ممكن يكون Admin أو Company أو Lawyer أو Worker
             */
            $table->string('recipient_type', 100);
            $table->unsignedBigInteger('recipient_id');

            /*
             * الشخص اللي عمل الحدث
             * مثال: عامل فتح تذكرة، محامي رد، شركة أضافت عامل
             */
            $table->string('actor_type', 100)->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();

            /*
             * الشيء المرتبط بالتنبيه
             * مثال: Ticket, Worker, Company, Lawyer
             */
            $table->string('entity_type', 100)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();


                   /*
             * نوع التنبيه
             * مثال: ticket_created, ticket_message_created, worker_created
             */
            $table->string('type', 100);




            /*
             * محتوى التنبيه
             */
            $table->string('title');
            $table->text('body')->nullable();



             /*
             * الرابط اللي يفتح لما المستخدم يضغط على التنبيه
             */
            $table->string('url')->nullable();

            /*
             * أي بيانات إضافية
             */
            $table->json('data')->nullable();

            /*
             * لو null يبقى التنبيه غير مقروء
             */
            $table->timestamp('read_at')->nullable();


            $table->index(['recipient_type', 'recipient_id', 'read_at'], 'recipient_read_index');
            $table->index(['recipient_type', 'recipient_id', 'created_at'], 'recipient_created_index');
            $table->index(['actor_type', 'actor_id'], 'actor_index');
            $table->index(['entity_type', 'entity_id'], 'entity_index');
            $table->index('type');
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
        Schema::dropIfExists('notifications');
    }
};
