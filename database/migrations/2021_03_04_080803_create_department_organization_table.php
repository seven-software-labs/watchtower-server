<?php

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentOrganizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_organization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('organization_id')->constrained('organizations');
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
        Schema::dropIfExists('department_organization');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        $organization = Organization::first();
        $departments = Department::pluck('id')->toArray();
        $organization->departments()->sync($departments);
    }
}
