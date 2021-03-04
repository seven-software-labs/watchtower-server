<?php

use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_organization_id')->nullable()->constrained('organizations');
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
        Schema::dropIfExists('organizations');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        $organizations = collect([
            [
                'name' => 'Watchtower',
            ],
            [
                'name' => 'Dallas Mavericks, Inc.'
            ],
        ]);

        $organizations->each(function($organization) {
            Organization::create($organization);
        });
    }
}
