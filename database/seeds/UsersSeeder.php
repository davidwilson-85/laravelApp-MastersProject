<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Arturo Pérez',
            'role' => 'prof',
            'whatsapp' => '647758341',
            'email' => 'arturo@gmail.com',
            'password' => bcrypt('12-Epsilon'),
        ]);

        DB::table('users')->insert([
            'name' => 'María Fernández',
            'role' => 'prof',
            'whatsapp' => '647758341',
            'email' => 'maria@gmail.com',
            'password' => bcrypt('12-Epsilon'),
        ]);

        DB::table('users')->insert([
            'name' => 'Ester Guirao',
            'role' => 'prof',
            'whatsapp' => '647758341',
            'email' => 'ester@gmail.com',
            'password' => bcrypt('12-Epsilon'),
        ]);

        DB::table('users')->insert([
            'name' => 'Toni Antón',
            'role' => 'prof',
            'whatsapp' => '647758341',
            'email' => 'toni@gmail.com',
            'password' => bcrypt('12-Epsilon'),
        ]);

        DB::table('users')->insert([
            'name' => 'Raquel Cervera',
            'role' => 'alum',
            'whatsapp' => '647758341',
            'email' => 'raquel@gmail.com',
            'password' => bcrypt('12-Epsilon'),
        ]);

        DB::table('users')->insert([
            'name' => 'Segundo Ríos',
            'role' => 'alum',
            'whatsapp' => '647758341',
            'email' => 'segundo@gmail.com',
            'password' => bcrypt('12-Epsilon'),
        ]);

        DB::table('users')->insert([
            'name' => 'Eva Calderón',
            'role' => 'alum',
            'whatsapp' => '647758341',
            'email' => 'eva@gmail.com',
            'password' => bcrypt('12-Epsilon'),
        ]);

        DB::table('users')->insert([
            'name' => 'Pablo Jiménez',
            'role' => 'alum',
            'whatsapp' => '647758341',
            'email' => 'pablo@gmail.com',
            'password' => bcrypt('12-Epsilon'),
        ]);

    }
}
