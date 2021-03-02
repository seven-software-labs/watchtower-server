<?php

use App\Models\Message;
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
        $messages = collect([
            // Customer Reply
            [
                'content' => "Sweat equity is the most valuable equity there is. Know your business and industry better than anyone else in the world. Love what you do or don't do it.",
                'message_type_id' => 1,
                'ticket_id' => 1,
                'user_id' => 2,
            ],
            // Operator Reply
            [
                'content' => "I'm not trying to make friends, I'm trying to make money.",
                'message_type_id' => 1,
                'ticket_id' => 1,
                'user_id' => 1,
            ],
        ]);

        $messages->each(function($message) {
            Message::create($message);
        });
    }
}
