<?php

use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Project::class, 20)->create()->each(function ($p) {
            $p->deposits()->saveMany(factory(\App\Deposit::class, rand(0,12))->make(['subaddr_index' => $p->subaddr_index]));
        });
    }
}
