<?php

use App\Models\Department;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('gray');
            $table->foreignId('organization_id')->constrained('organizations');
            $table->boolean('is_default');
            $table->boolean('is_removeable')->default(false);
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
        Schema::dropIfExists('departments');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        Department::create([
            'name' => 'Client Success',
            'color' => 'gray',
            'organization_id' => 1,
            'is_default' => true,
        ]);
    }
}
