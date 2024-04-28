@extends('layouts.app')

@section('title')
<title>Atte</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="timestamp">
    <div class="timestamp__inner">
        <p class="timestamp__message">{{ Auth::user()->name }}さんお疲れ様です！</p>
        <div class="timestamp__btn-wrap">
            <div class="timestamp__btn">
                <form action="/work-start" class="work-start-form" method="post">
                    @csrf
                    <button type="submit" class="timestamp__btn-submit" {{ $status != 0 ? 'disabled' : '' }}>勤務開始</button>
                </form>
            </div>
            <div class="timestamp__btn">
                <form action="/work-end" class="work-end-form" method="post">
                    @csrf
                    <button type="submit" class="timestamp__btn-submit" {{ $status != 1 ? 'disabled' : '' }}>勤務終了</button>
                </form>
            </div>
            <div class="timestamp__btn">
                <form action="/break-start" class="break-startt-form" method="post">
                    @csrf
                    <button type="submit" class="timestamp__btn-submit" {{ $status != 1 ? 'disabled' : '' }}>休憩開始</button>
                </form>
            </div>
            <div class="timestamp__btn">
                <form action="/break-end" class="break-end-form" method="post">
                    @csrf
                    <button type="submit" class="timestamp__btn-submit" {{ $status != 2 ? 'disabled' : '' }}>休憩終了</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection