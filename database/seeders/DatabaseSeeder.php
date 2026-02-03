<?php

namespace database\Seeders;

use Idev\EasyAdmin\app\Models\Role;
// use App\Models\SampleData;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->role();
        $this->user();
       // $this->sampleData();
    }

    public function role()
    {
        Role::updateOrCreate(
            [
                'name' => 'admin'
            ],
            [
                'name' => 'admin',
                'access' => '[{"route":"dashboard","access":["list"]},{"route":"role","access":["list","create","show","edit","delete","import-excel-default","export-excel-default","export-pdf-default"]},{"route":"user","access":["list","create","show","edit","delete","import-excel-default","export-excel-default","export-pdf-default"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'operator'
            ],
            [
                'name' => 'operator',
                'access' => '[{"route":"dashboard","access":["list"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'staff-administrasi'
            ],
            [
                'name' => 'staff-administrasi',
                'access' => '[{"route":"dashboard","access":["list"]}]',
            ]
        );

        Role::updateOrCreate(
            [
                'name' => 'Supervisi'
            ],
            [
                'name' => 'supervisi',
                'access' => '[{"route":"dashboard","access":["list"]}]',
            ]
        );
    }


    

    public function user()
    {
        User::updateOrCreate(
            [
                'email' => 'admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin',
                'password' => bcrypt('prod@123'),
                'role_id' => Role::where('name', 'admin')->first()->id,
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'operator',
            ],
            [
                'name' => 'Operator',
                'email' => 'operator',
                'password' => bcrypt('123'),
                'role_id' => Role::where('name', 'operator')->first()->id,
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'staff-administrasi',
            ],
            [
                'name' => 'Staff Administrasi',
                'email' => 'staff-administrasi',
                'password' => bcrypt('asdasd123'),
                'role_id' => Role::where('name', 'staff-administrasi')->first()->id,
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'supervisi',
            ],
            [
                'name' => 'Supervisi',
                'email' => 'supervisi',
                'password' => bcrypt('asdasd123'),
                'role_id' => Role::where('name', 'supervisi')->first()->id,
            ]
        );
    }

    /*
    public function sampleData()
    {
        SampleData::updateOrCreate(
            [
                'name' => 'Augusta Mauricio',
            ],
            [
                'name' => 'Augusta Mauricio',
                'age' => 19,
                'gender' => 'Male',
                'address' => 'Wolkhadr Street number 20',
            ]
        );

        SampleData::updateOrCreate(
            [
                'name' => 'Melivia Adrenaline',
            ],
            [
                'name' => 'Melivia Adrenaline',
                'age' => 21,
                'gender' => 'Female',
                'address' => 'Hawk House 28 Canada',
            ]
        );

        SampleData::updateOrCreate(
            [
                'name' => 'Indigo Venisa',
            ],
            [
                'name' => 'Indigo Venisa',
                'age' => 20,
                'gender' => 'Female',
                'address' => 'Jitruno Street',
            ]
        );
    }
    */
}
