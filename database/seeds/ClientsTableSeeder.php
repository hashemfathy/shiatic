<?php

use App\Client;
use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        \DB::table('clients')->truncate();

        $file = 'database/seeds/sql/clients.sql';

        \DB::unprepared(file_get_contents($file));

        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
