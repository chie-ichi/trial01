@extends('layouts.app')

@section('title')
<title>Register | Atte</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register">
    <div class="register__inner">
        <h1 class="register__page-ttl">会員登録</h1>
        <form action="/register" class="form" method="post">
            @csrf
            <div class="form__group">
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="名前" />
                    </div>
                    <div class="form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
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
                <div class="form__group-content">
                    <div class="form__input--password">
                        <input type="password" name="password_confirmation" placeholder="確認用パスワード" />
                    </div>
                    <div class="form__error">
                        @error('password_confirmation')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">会員登録</button>
            </div>
        </form>
        <p class="register__description">
            アカウントをお持ちの方はこちらから<br><a href="/login" class="register__description--link">ログイン</a>
        </p>
    </div>
</div>
@endsection