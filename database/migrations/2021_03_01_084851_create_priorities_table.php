<?php

use App\Models\Priority;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrioritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('priorities', function (Blueprint $table) {
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
        Schema::dropIfExists('priorities');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        $priorities = collect([
            [
                'name' => 'Low',
                'color' => 'gray',
            ],
            [
                'name' => 'Medium',
                'color' => 'blue',
            ],
            [
                'name' => 'High',
                'color' => 'yellow',
            ],
            [
                'name' => 'Critical',
                'color' => 'red',
            ],
        ]);
        
        $priorities->each(function($priority) {
            Priority::create($priority);
        });
    }
}
