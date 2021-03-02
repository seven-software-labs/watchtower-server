<?php

use App\Models\Ticket;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('channel_id')->nullable()->constrained('channels');
            $table->foreignId('ticket_type_id')->constrained('ticket_types');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('status_id')->constrained('statuses');
            $table->foreignId('priority_id')->constrained('priorities');
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
        Schema::dropIfExists('tickets');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        Ticket::create([
            'subject' => 'This is an example ticket',
            'user_id' => 2,
            'ticket_type_id' => 1,
            'department_id' => 1,
            'status_id' => 1,
            'priority_id' => 1,
        ]);
    }
}
