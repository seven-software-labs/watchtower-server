<?php

use App\Models\MessageType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('message_types');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        MessageType::create([
            [
                'name' => 'Reply',
                'description' => 'A reply sent to the other person.',
            ],
            [
                'name' => 'Note',
                'description' => 'A message for internal purposes only.',
            ],
        ]);
    }
}
