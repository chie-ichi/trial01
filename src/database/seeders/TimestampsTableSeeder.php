<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimestampsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $today = Carbon::today();
        $params = [];

        for($i = 11; $i > 1; $i--) {
            for($j = 1; $j <= 10; $j++) {
                $dummy_start_time = $today->copy()->subDays($i)->setTime(9,0)->toDateTimeString(); //i日前の9:00
                $dummy_end_time = $today->copy()->subDays($i)->setTime(17,0)->toDateTimeString(); //i日前の17:00
                $param = [
                    'user_id' => $j,
                    'status' => 3,
                    'start_time' => $dummy_start_time,
                    'end_time' => $dummy_end_time,
                    'break_duration' => '00:30:00',
                    'work_duration' => '07:30:00',
                    'created_at' => $dummy_start_time,
                    'updated_at' => $dummy_end_time,
                ];
                array_push($params, $param);
            }
        }

        foreach($params as $param){
            DB::table('timestamps')->insert($param);
        }
    }
}
