<?php

use App\Models\Organization;
use App\Models\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('status_id')->constrained('statuses');
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
        Schema::dropIfExists('organization_status');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        $organization = Organization::first();
        $statuses = Status::pluck('id')->toArray();
        $organization->statuses()->sync($statuses);
    }
}
