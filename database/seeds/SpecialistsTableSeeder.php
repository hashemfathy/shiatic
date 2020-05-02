<?php

use App\Specialist;
use Illuminate\Database\Seeder;

class SpecialistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        \DB::table('specialists')->truncate();

        $file = 'database/seeds/sql/specialists.sql';

        \DB::unprepared(file_get_contents($file));

        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
