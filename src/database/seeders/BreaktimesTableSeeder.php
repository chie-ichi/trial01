<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Timestamp;

class BreaktimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [];

        $timestamps = Timestamp::latest() //最新
            ->take(50) //50件
            ->get()
            ->sortBy('id'); //id順に並べ替え
        
        foreach($timestamps as $timestamp){
            $date = new Carbon($timestamp->start_time);
            $dummy_start_time = $date->copy()->setTime(12,0)->toDateTimeString();
            $dummy_end_time = $date->copy()->setTime(12,15)->toDateTimeString();

            $param = [
                'timestamp_id' => $timestamp->id,
                'start_time' => $dummy_start_time,
                'end_time' => $dummy_end_time,
                'created_at' => $dummy_start_time,
                'updated_at' => $dummy_end_time,
            ];
            array_push($params, $param);

            $dummy_start_time = $date->copy()->setTime(13,0)->toDateTimeString();
            $dummy_end_time = $date->copy()->setTime(13,15)->toDateTimeString();

            $param = [
                'timestamp_id' => $timestamp->id,
                'start_time' => $dummy_start_time,
                'end_time' => $dummy_end_time,
                'created_at' => $dummy_start_time,
                'updated_at' => $dummy_end_time,
            ];
            array_push($params, $param);
        }

        foreach($params as $param){
            DB::table('breaktimes')->insert($param);
        }
    }
}
