<?php

use App\Models\Channel;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelOrganizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_organization', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('channel_id')->constrained('channels');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('organization_id')->constrained('organizations');
            $table->boolean('is_active')->default(false);
            $table->json('settings');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_organization');
    }
}
