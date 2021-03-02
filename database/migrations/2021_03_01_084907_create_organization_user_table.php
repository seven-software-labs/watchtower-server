<?php

use App\Models\Organization;
use App\Models\User;
use App\Models\Pivot\OrganizationUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('is_primary')->default(false);
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
        Schema::dropIfExists('organization_users');
    }

    /**
     * Seed initial data.
     */
    public function seed()
    {
        $user = User::firstOrFail();
        $organization = Organization::first();

        $user->organizations()->attach($organization->getKey(), [
            'is_primary' => true,
        ]);

        $secondUser = User::find(2);
        $secondOrganization = Organization::find(2);

        $secondUser->organizations()->attach($secondOrganization->getKey(), [
            'is_primary' => true,
        ]);
    }
}
