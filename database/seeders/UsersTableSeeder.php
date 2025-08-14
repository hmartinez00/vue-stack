<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;


class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        // \DB::table('users')->delete();
        
        //Creamos el usuario Administrador
        $admin = User::create([ 
            'name' => 'Hector Martinez',
            'email' => 'hmartinez@pregosoft.com',
            'password' => Hash::make('Dd241121*AA')
        ]);

        //Asignamos el Rol de Admin al usuario administrador creado
        $admin->assignRole('admin');

        //Creamos el usuario client
        $client = User::create([ 
            'name' => 'Hector Martinez',
            'email' => 'hectoralonzomartinez00@gmail.com',
            'password' => Hash::make('Pyp8r5correoucv!')
        ]);

        //Asignamos el Rol de Client al usuario cliente creado
        $client->assignRole('client');
        
        
    }
}