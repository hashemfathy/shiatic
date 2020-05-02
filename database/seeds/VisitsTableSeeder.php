<?php

use App\Visit;
use Illuminate\Database\Seeder;

class VisitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        \DB::table('visits')->truncate();

        $file = 'database/seeds/sql/visits.sql';

        \DB::unprepared(file_get_contents($file));

        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
