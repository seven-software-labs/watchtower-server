<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->longtext('content');
            $table->foreignId('message_type_id')->constrained('message_types');
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('target_user_id')->nullable();
            $table->string('source_id')->nullable();
            $table->dateTime('source_created_at')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->boolean('is_delivered')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        $this->seed();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        // ...
    }
}
