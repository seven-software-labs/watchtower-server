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
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('master_organization_id')->constrained('organizations');
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('priority_id')->constrained('priorities');
            $table->foreignId('status_id')->constrained('statuses');
            $table->foreignId('ticket_type_id')->constrained('ticket_types');
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
            'department_id' => 1,
            'organization_id' => 1,
            'priority_id' => 1,
            'status_id' => 1,
            'subject' => 'This is an example ticket',
            'ticket_type_id' => 1,
            'user_id' => 2,
        ]);
    }
}
