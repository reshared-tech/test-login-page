<?php

namespace App\Tools;

class Language
{
    static $config;

    const JP = 'japanese';

    const Languages = [
        'japanese' => [
            'Welcome' => 'ようこそ',
            'Welcome Back' => 'おかえりなさい',
            'Please log in your account' => 'アカウントにログインしてください',
            'Hi' => 'こんにちは',
            'Log out' => 'ログアウト',
            'Email address' => 'メールアドレス',
            'Please input your email address.' => 'メールアドレスを入力してください',
            'Please input your password.' => 'パスワードを入力してください',
            'Log in' => 'ログイン',
            'Don\'t have an account?' => 'アカウントをお持ちでないですか？',
            'Sign up' => 'サインアップ',
            'Register a new account' => '新しいアカウントを登録',
            'Name' => '名前',
            'Please input your name.' => '名前を入力してください',
            'Password' => 'パスワード',
            'Confirm password' => 'パスワード確認',
            'Please repeat your password.' => 'パスワードを再入力してください',
            'Already have an account?' => 'すでにアカウントをお持ちですか？',
            'Login Page' => 'ログインページ',
            'Register Page' => '登録ページ',
        ],
    ];

    public static function setLang($config)
    {
        self::$config = self::Languages[$config] ?? [];
    }

    public static function show($text)
    {
        return self::$config[$text] ?? $text;
    }
}