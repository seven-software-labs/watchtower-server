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
                'organization_id' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'Pending',
                'color' => 'yellow',
                'organization_id' => 1,
                'is_default' => false,
            ],
            [
                'name' => 'Closed',
                'color' => 'black',
                'organization_id' => 1,
                'is_default' => false,
            ],
        ]);

        $statuses->each(function($status) {
            Status::create($status);
        });
    }
}
