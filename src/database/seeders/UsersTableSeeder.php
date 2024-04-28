<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [
            [
                'name' => '田中一郎',
                'email' => 'test1@test.jp',
                'password' => Hash::make('password1'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '田中二郎',
                'email' => 'test2@test.jp',
                'password' => Hash::make('password2'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '田中三郎',
                'email' => 'test3@test.jp',
                'password' => Hash::make('password3'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '田中四郎',
                'email' => 'test4@test.jp',
                'password' => Hash::make('password4'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '田中五郎',
                'email' => 'test5@test.jp',
                'password' => Hash::make('password5'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '田中六郎',
                'email' => 'test6@test.jp',
                'password' => Hash::make('password6'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '田中七郎',
                'email' => 'test7@test.jp',
                'password' => Hash::make('password7'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '田中八郎',
                'email' => 'test8@test.jp',
                'password' => Hash::make('password8'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '田中九郎',
                'email' => 'test9@test.jp',
                'password' => Hash::make('password9'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '田中十郎',
                'email' => 'test10@test.jp',
                'password' => Hash::make('password10'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach($params as $param){
            DB::table('users')->insert($param);
        }
    }
}
