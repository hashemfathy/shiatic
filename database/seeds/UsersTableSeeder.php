<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->truncate();
        // factory(User::class)->create();
        $users = [
            ['id' => 1, 'name' => 'ahmed adel', 'email' => 'ahmed_adel@shiatic.com', 'email_verified_at' => now(), 'password' => bcrypt('Shi@tic'), 'is_admin' => true, 'remember_token' => Str::random(10),],
            ['id' => 2, 'name' => 'admin', 'email' => 'admin@shiatic.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'is_admin' => true, 'remember_token' => Str::random(10),],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
