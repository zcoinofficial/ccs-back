<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('author');
            $table->string('title');
            $table->unsignedInteger('subaddr_index')->nullable();
            $table->string('address')->nullable();
            $table->string('address_uri')->nullable();
            $table->string('qr_code')->nullable();
            $table->float('target_amount');
            $table->float('raised_amount')->default(0);
            $table->string('state');
            $table->string('filename')->unique();
            $table->unsignedInteger('milestones');
            $table->unsignedInteger('milestones_completed')->default(0);
            $table->string('gitlab_url')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
