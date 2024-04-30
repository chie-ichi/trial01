<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Models\Timestamp;
use App\Models\Breaktime;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class TimestampController extends Controller
{
    public function index(Request $request)
    {
        $user_id = $request->user()->id; //現在のログインユーザーのIDを取得
        $status = 0;

        if($user_id) {
            //ログイン中のユーザーのstart_timeが最新のレコードを取得
            $timestamp = Timestamp::where('user_id', $user_id)
            ->latest('start_time')
            ->first();

            if($timestamp) {
                //抽出されたレコードのstart_timeの日時情報を取得
                $latest_start_time = Carbon::parse($timestamp->start_time);
                $today = Carbon::today();

                if($latest_start_time->lt($today) && $timestamp->status == 3) {
                    //抽出されたレコードのstart_timeが昨日以前でかつstatusが退勤済み(3)の場合はビューに渡す$statusの値に、0(未出勤)をセットする。
                    $status = 0;
                } else {
                    //それ以外の場合ビューに渡す$statusの値に、レコードから取得したstatusをセットする。
                    $status = $timestamp->status;
                }
            }
        }

        return view('index', compact('status'));
    }

    public function workStart(Request $request){
        $current_time = Carbon::now();   //現在時刻を取得
        $user_id = $request->user()->id; //現在のログインユーザーのIDを取得

        //レコードを新規作成して打刻
        Timestamp::create([
            'user_id' => $user_id,
            'status' => 1, //ステータス：勤務中
            'start_time' => $current_time->toDateTimeString(), //勤務開始時刻：現在時刻
            'break_duration' => '00:00:00',  //休憩時間：0:00
        ]);

        return redirect('/');
    }

    public function workEnd(Request $request){
        $current_time = Carbon::now();  //現在時刻を取得
        $user_id = $request->user()->id; //現在のログインユーザーのIDを取得

        //ログイン中のユーザーのstart_timeが最新のレコードを取得
        $timestamp = Timestamp::where('user_id', $user_id)
        ->latest('start_time')
        ->first();

        if($timestamp) {
            //抽出されたレコードからstart_timeの日時情報を取得
            $latest_start_time = Carbon::parse($timestamp->start_time);

            //抽出されたレコードからbreak_durationの時間情報を取得
            $break_duration = Carbon::parse($timestamp->break_duration);
            $break_duration_seconds = $break_duration->secondsSinceMidnight();

            if($latest_start_time->isToday()) {
                //抽出されたレコードのstart_timeが今日の場合

                //今日の勤務時間を算出
                $work_duration_seconds = $current_time->diffInSeconds($latest_start_time);
                $work_duration_seconds -= $break_duration_seconds;
                $work_duration = gmdate('H:i:s', $work_duration_seconds);

                //勤務終了時刻を現在時刻として算出
                $end_time = $current_time->toDateTimeString();

                //勤務終了時刻と勤務時間を記録
                $timestamp->update([
                    'status' => 3, //ステータス：退勤済
                    'work_duration' => $work_duration,  //勤務時間
                    'end_time' => $end_time, //勤務終了時刻：現在時刻
                ]);
            } else {
                //それ以外=勤務が日をまたいでいる場合

                //----------
                //▼勤務開始日についての処理
                //----------

                //勤務開始日の勤務時間を算出
                $work_duration_seconds = (clone $latest_start_time)->startOfDay()->diffInSeconds($latest_start_time);
                $work_duration_seconds -= $break_duration_seconds;
                $work_duration = gmdate('H:i:s', $work_duration_seconds);

                //勤務開始日の勤務終了時刻を一日の最後の時刻として算出
                $end_time = (clone $latest_start_time)->endOfDay()->toDateTimeString();

                //一日の最後の時刻を打刻
                $timestamp->update([
                    'status' => 3, //ステータス：退勤済
                    'work_duration' => $work_duration,  //勤務時間
                    'end_time' => $end_time, //勤務終了時刻：一日の最後
                ]);

                $today = Carbon::today(); //今日の日付
                $next_day = (clone $latest_start_time)->addDay(); //抽出されたレコードのstart_timeの翌日

                //----------
                //▼抽出されたレコードのstart_timeの翌日〜昨日についての処理
                //----------

                //レコードを新規作成し、丸一日勤務として打刻
                while($next_day->lt($today)){

                    //勤務開始時刻を一日の最初の時刻として算出
                    $start_time = (clone $next_day)->startOfDay()->toDateTimeString();
                    //勤務終了時刻を一日の最後の時刻として算出
                    $end_time = (clone $next_day)->endOfDay()->toDateTimeString(); 

                    Timestamp::create([
                        'user_id' => $user_id,
                        'status' => 3, //ステータス：退勤済
                        'start_time' => $start_time, //勤務開始時刻：一日の最初
                        'end_time' => $end_time, //勤務終了時刻：一日の最後
                        'work_duration' => '23:59:59', //勤務時間：一日中
                        'break_duration' => '00:00:00', //休憩時間：なし
                    ]);

                    $next_day->addDay();  //翌日を求める
                }

                //----------
                //▼今日についての処理
                //----------

                //今日の勤務時間(一日の最初〜現在時刻)を算出
                $today_start = clone $today->startOfDay(); //一日の最初
                $work_duration_seconds = $today_start->diffInSeconds($current_time);
                $work_duration = gmdate('H:i:s', $work_duration_seconds);

                //レコードを新規作成して打刻
                Timestamp::create([
                    'user_id' => $user_id,
                    'status' => 3, //ステータス：退勤済
                    'start_time' => $today_start->toDateTimeString(), //日付が変わった直後の時刻を打刻
                    'end_time' => $current_time->toDateTimeString(), //現在時刻を打刻
                    'work_duration' => $work_duration, //勤務時間：一日の最初〜現在
                    'break_duration' => '00:00:00', //休憩時間：なし
                ]);
            }
        }

        return redirect('/');
    }

    public function breakStart(Request $request){
        $current_time = Carbon::now();   //現在時刻を取得
        $user_id = $request->user()->id; //現在のログインユーザーのIDを取得
        $today = Carbon::today(); //今日の日付

        //勤務開始時間が今日である最新のtimestampを取得
        $timestamp = Timestamp::where('user_id', $user_id)
        ->whereDate('start_time', $today)
        ->first();

        if($timestamp) {
            $timestamp->update(['status' => '2']); //ステータスを2(休憩中)に更新
            $timestamp_id = $timestamp->id; //Timestampのレコードのidをtimestamp_idとして取得

            //Breaktimeのレコードを作成
            Breaktime::create([
                'timestamp_id' => $timestamp_id,
                'start_time' => $current_time->toDateTimeString(), //休憩開始時刻を打刻
            ]);
        }

        return redirect('/');
    }

    private function getBreaktimeTotal($breaktimes){
        $breaktime_total_sec = 0; //初期値0秒

        foreach($breaktimes as $breaktime) {
            $start_time = Carbon::parse($breaktime->start_time); //休憩開始時間
            $end_time = Carbon::parse($breaktime->end_time); //休憩終了時間
            $duration = $end_time->diffInSeconds($start_time); //休憩時間を算出
            $breaktime_total_sec += $duration; //休憩時間累計を算出
        }

        return $breaktime_total_sec; //休憩時間の合計を返す
    }

    public function breakEnd(Request $request){
        $current_time = Carbon::now();
        $user_id = $request->user()->id;
        $today = Carbon::today();

        $timestamp = Timestamp::where('user_id', $user_id)
        ->latest('start_time')
        ->first();

        if($timestamp) {
            //抽出されたレコードからstart_timeの日時情報を取得
            $latest_start_time = Carbon::parse($timestamp->start_time);

            //抽出されたレコードからbreak_durationの時間情報を取得
            $break_duration = Carbon::parse($timestamp->break_duration);
            $break_duration_seconds = $break_duration->secondsSinceMidnight();

            $timestamp_id = $timestamp->id;

            if($latest_start_time->isToday()) {
                //抽出されたレコードのstart_timeが今日の場合

                //Breaktimesテーブルに休憩終了時刻を記録
                $breaktimes = Breaktime::where('timestamp_id', $timestamp_id)
                ->where('end_time', null)
                ->whereDate('start_time', $today)
                ->first();

                if($breaktimes) {
                    $breaktimes->update([
                        'end_time' => $current_time->toDateTimeString(), //休憩終了時刻を打刻
                    ]);
                }

                //今日の休憩時間合計を算出
                $breaktimes = Breaktime::where('timestamp_id', $timestamp_id)
                ->whereDate('start_time', $today)
                ->get();
                $breaktime_total_sec = $this->getBreaktimeTotal($breaktimes);
                $breaktime_total = gmdate("H:i:s", $breaktime_total_sec);

                //タイムスタンプを更新
                $timestamp->update([
                    'status' => '1', //勤務中
                    'break_duration' => $breaktime_total, //当日の休憩時間合計
                ]);
            } else {
                //それ以外=休憩が日をまたいでいる場合

                //----------
                //▼休憩開始日についての処理
                //----------

                //勤務終了時刻・休憩終了時刻を休憩開始日の日付が変わる直前の時刻として算出
                $end_time = (clone $latest_start_time)->endOfDay()->toDateTimeString();

                //休憩開始日の日付
                $date = (clone $latest_start_time)->toDateString();

                //Breaktimesテーブルに、休憩開始日の休憩終了時刻を日付が変わる直前として記録
                $breaktime = Breaktime::where('timestamp_id', $timestamp_id)
                ->where('end_time', null)
                ->latest('start_time')
                ->first();

                if($breaktime) {
                    $breaktime->update([
                        'end_time' => $end_time, //休憩終了時刻：一日の最後
                    ]);

                    //休憩開始日の休憩時間合計を算出
                    $breaktimes = Breaktime::where('timestamp_id', $timestamp_id)
                    ->whereDate('start_time', $date)
                    ->get();
                    $breaktime_total_sec = $this->getBreaktimeTotal($breaktimes);
                    $breaktime_total = gmdate("H:i:s", $breaktime_total_sec);

                    //休憩開始日の勤務時間を算出
                    $work_duration_seconds = (clone $latest_start_time)->diffInSeconds((clone $latest_start_time)->endOfDay()); //勤務終了時間と勤務開始時間の差分（単位：秒）
                    $work_duration_seconds -= $breaktime_total_sec; //さらに合計休憩時間を差し引く（単位：秒）
                    $work_duration = gmdate('H:i:s', $work_duration_seconds); //勤務時間を秒→'H:i:s'にフォーマット

                    //休憩開始日の日付が変わる直前の時刻を打刻
                    $timestamp->update([
                        'status' => 3, //ステータス：退勤済
                        'end_time' => $end_time, //勤務終了時刻：一日の最後
                        'work_duration' => $work_duration,  //勤務時間
                        'break_duration' => $breaktime_total, //休憩時間
                    ]);
                }

                $today = Carbon::today(); //今日の日付
                $next_day = (clone $latest_start_time)->addDay(); //抽出されたレコードのstart_timeの翌日


                //----------
                //▼休憩開始日の翌日〜昨日についての処理
                //----------

                while($next_day->lt($today)){

                    //勤務終了時刻・休憩終了時刻を日付が変わった直後の時刻として算出
                    $start_time = (clone $next_day)->startOfDay()->toDateTimeString();
                    //勤務終了時刻・休憩終了時刻を日付が変わる直前の時刻として算出
                    $end_time = (clone $next_day)->endOfDay()->toDateTimeString(); 

                    //丸一日休憩としてその日のTimestampのレコードを作成
                    $timestamp = Timestamp::create([
                        'user_id' => $user_id,
                        'status' => 3, //ステータス：退勤済
                        'start_time' => $start_time, //勤務開始時刻：一日の最初
                        'end_time' => $end_time, //勤務終了時刻：一日の最後
                        'work_duration' => '00:00:00', //勤務時間：なし
                        'break_duration' => '23:59:59',  //休憩時間：一日中
                    ]);

                    if($timestamp) {
                        $timestamp_id = $timestamp->id;

                        //丸一日休憩としてその日のBreaktimeのレコードを作成
                        Breaktime::create([
                            'timestamp_id' => $timestamp_id,
                            'start_time' => $start_time, //休憩開始時刻：一日の最初
                            'end_time' => $end_time, //休憩終了時刻：一日の最後
                        ]);
                    }

                    $next_day->addDay();  //翌日を求める
                }

                //----------
                //▼今日についての処理
                //----------

                //今日の休憩時間(一日の最初〜現在時刻)を算出
                $today_start = clone $today->startOfDay(); //一日の最初
                $break_duration_seconds = $today_start->diffInSeconds($current_time);
                $break_duration = gmdate('H:i:s', $break_duration_seconds);

                //勤務開始時刻・休憩開始時刻を一日の最初の時刻として算出
                $start_time = $today_start->toDateTimeString();
                //休憩終了時刻を現在時刻として算出
                $end_time = $current_time->toDateTimeString();

                //Timestampのレコードを新規作成して打刻
                $timestamp = Timestamp::create([
                    'user_id' => $user_id,
                    'status' => 1, //ステータス：勤務中
                    'start_time' => $start_time, //勤務開始時刻：一日の最初
                    'work_duration' => '00:00:00', //勤務時間：なし
                    'break_duration' => $break_duration, //休憩時間：一日の最初〜現在時刻
                ]);

                if($timestamp) {
                    //作成したTimestampのレコードのidをtimestamp_idとして取得
                    $timestamp_id = $timestamp->id;

                    //Breaktimeのレコードを作成し、休憩時間を記録
                    Breaktime::create([
                        'timestamp_id' => $timestamp_id,
                        'start_time' => $start_time, //休憩開始時刻：一日の最初
                        'end_time' => $end_time, //休憩終了時刻：現在時刻
                    ]);
                }

            }

        }

        return redirect('/');
    }

    public function attendance($date = null)
    {
        if($date) {
            //引数$dateが存在する場合はその日付を使用
            $day = Carbon::parse($date);
        } else {
            //引数$dateが存在しない場合は今日の日付を使用
            $day = Carbon::today();
            $date = $day->format('Y-m-d');
        }

        //$dayの翌日を算出
        $next_day = (clone $day)->addDay();
        if($next_day->isFuture()) {
            $next_date = null;
        } elseif($next_day->eq(Carbon::today())) {
            $next_date = "today";
        } else {
            $next_date = $next_day->format('Y-m-d');
        }

        //$dayの前日を算出
        $previous_day = (clone $day)->subDay();
        $previous_date = $previous_day->format('Y-m-d');

        //タイムスタンプのうち、'start_time'が$dayの日付になっているレコードを抽出
        $timestamps = Timestamp::whereDate('start_time', $day->toDateString())->paginate(5);

        //ユーザー情報を取得
        $users = User::all();

        return view('attendance', compact('timestamps', 'users', 'date', 'next_date', 'previous_date'));
    }

}
