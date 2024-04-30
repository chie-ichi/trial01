@extends('layouts.app')

@section('title')
<title>Attendance | Atte</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance">
    <div class="attendance__inner">
        <div class="attendance__head">
            @if($previous_date)
            <a href="/attendance/{{ $previous_date }}" class="attendance__head-prev">&lt;</a>
            @else
            <span class="attendance__head-prev attendance__head-prev--disabled">&lt;</span>
            @endif
            <span class="attendance__head-date">
                @if($date)
                    {{ $date }}
                @else
                    {{ date('Y-m-d') }}
                @endif
            </span>
            @if($next_date == "today")
            <a href="/attendance/" class="attendance__head-next">&gt;</a>
            @elseif($next_date)
            <a href="/attendance/{{ $next_date }}" class="attendance__head-next">&gt;</a>
            @else
            <span class="attendance__head-next attendance__head-next--disabled">&gt;</span>
            @endif
        </div>
        <table class="attendance__table">
            <tr class="attendance__table-row">
                <th class="attendance__table-heading">名前</th>
                <th class="attendance__table-heading">勤務開始</th>
                <th class="attendance__table-heading">勤務終了</th>
                <th class="attendance__table-heading">休憩時間</th>
                <th class="attendance__table-heading">勤務時間</th>
            </tr>
            @foreach($timestamps as $timestamp)
            <tr class="attendance__table-row">
                @php
                    foreach($users as $user){
                        if($user->id == $timestamp->user_id){
                            $user_name = $user->name;
                            break;
                        }
                    }
                @endphp
                @if($user_name)
                    <td class="attendance__table-data">{{ $user_name }}</td>
                @else
                    <td class="attendance__table-data">ユーザー不明</td>
                @endif
                <td class="attendance__table-data">
                    @if($timestamp->start_time)
                        {{ $timestamp->start_time->format('H:i:s') }}
                    @else
                        -
                    @endif
                </td>
                <td class="attendance__table-data">
                    @if($timestamp->end_time)
                        {{ $timestamp->end_time->format('H:i:s') }}
                    @else
                        -
                    @endif
                </td>
                <td class="attendance__table-data">
                    @if($timestamp->break_duration)
                        {{ $timestamp->break_duration->format('H:i:s') }}
                    @else
                        -
                    @endif
                </td>
                <td class="attendance__table-data">
                    @if($timestamp->status != 3)
                        -
                    @elseif($timestamp->work_duration)
                        {{ $timestamp->work_duration->format('H:i:s') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
        <div class="pagination-wrap">
        {{ $timestamps->links('vendor.pagination.default2') }}
        </div>
    </div>
</div>
@endsection