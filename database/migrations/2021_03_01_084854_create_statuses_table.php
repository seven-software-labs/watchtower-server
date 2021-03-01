<?php

use App\Models\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('gray');
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
        Schema::dropIfExists('statuses');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        $statuses = collect([
            [
                'name' => 'Open',
                'color' => 'green',
            ],
            [
                'name' => 'Pending',
                'color' => 'yellow',
            ],
            [
                'name' => 'Closed',
                'color' => 'black',
            ],
        ]);

        $statuses->each(function($status) {
            Status::create($status);
        });
    }
}
