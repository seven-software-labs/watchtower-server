<?php

use App\Models\Organization;
use App\Models\Priority;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationPriorityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_priority', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('priority_id')->constrained('priorities');
            $table->boolean('is_default')->default(false);
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
        Schema::dropIfExists('organization_priority');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        $organization = Organization::first();
        $priorities = Priority::pluck('id')->toArray();
        $organization->priorities()->sync($priorities);
    }
}
