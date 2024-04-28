@extends('layouts.app')

@section('title')
<title>Login | Atte</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login">
    <div class="login__inner">
        <h1 class="login__page-ttl">ログイン</h1>
        <form action="/login" class="form" method="post">
            @csrf
            <div class="form__group">
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス" />
                    </div>
                    <div class="form__error">
                        @error('email')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="form__group-content">
                    <div class="form__input--password">
                        <input type="password" name="password" placeholder="パスワード" />
                    </div>
                    <div class="form__error">
                        @error('password')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">ログイン</button>
            </div>
        </form>
        <p class="login__description">
            アカウントをお持ちでない方はこちらから<br><a href="/register" class="login__description--link">会員登録</a>
        </p>
    </div>
</div>
@endsection