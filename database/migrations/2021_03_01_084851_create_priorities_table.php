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
            $table->foreignId('organization_id')->constrained('organizations');
            $table->boolean('is_default');
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
                'organization_id' => 1,
                'is_default' => false,
            ],
            [
                'name' => 'Medium',
                'color' => 'blue',
                'organization_id' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'High',
                'color' => 'yellow',
                'organization_id' => 1,
                'is_default' => false,
            ],
            [
                'name' => 'Critical',
                'color' => 'red',
                'organization_id' => 1,
                'is_default' => false,
            ],
        ]);
        
        $priorities->each(function($priority) {
            Priority::create($priority);
        });
    }
}
