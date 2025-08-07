<?php

namespace Tools;

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
            'name' => '名前',
            'Name' => '名前',
            'Please input your name.' => '名前を入力してください',
            'Password' => 'パスワード',
            'Confirm password' => 'パスワード確認',
            'Please repeat your password.' => 'パスワードを再入力してください',
            'Already have an account?' => 'すでにアカウントをお持ちですか？',
            'Login Page' => 'ログインページ',
            'Register Page' => '登録ページ',
            'Registration failed. Please contact administrator.' => '',
            'Registration successful.' => '登録が成功しました。',
            'Email already registered. Please login.' => 'このメールアドレスは既に登録されています。ログインしてください。',
            'Login successful' => 'ログイン成功',
            'Incorrect password' => 'パスワードが間違っています',
            'Invalid email format' => 'メールアドレスの形式が正しくありません',
            'Email not registered. Please register first.' => 'このメールアドレスは登録されていません。まず登録してください。',
            'Account locked. Try again in 10 minutes.' => 'アカウントがロックされました。10分後に再度お試しください。',
            'Please input %s' => '\wを入力してください',
            'Password confirmation does not match.' => 'パスワードの確認が一致しません。',
            '%s must be at least %d characters' => '%sは%d文字以上必要です',
            '%s must be less than %d characters' => '%sは%d文字未満で入力してください',
            'Chat Room' => 'チャットルーム',
            'New Chat' => '新しいチャット',
            'Send' => '送信',
            'Input your message here' => 'ここにメッセージを入力してください',
            'Save' => '保存します',
        ],
    ];

    public static function setLang($config)
    {
        self::$config = self::Languages[$config] ?? [];
    }

    public static function show($text)
    {
        if (is_numeric($text)) {
            return $text;
        }

        if (is_string($text)) {
            if (isset(self::$config[$text])) {
                return self::$config[$text];
            }

            if (empty(self::$config)) {
                return $text;
            }

            foreach (self::$config as $k => $v) {
                if (strpos($k, '%d') === false && strpos($k, '%w') === false) {
                    continue;
                }

                $pattern = str_replace('%s', '(.+)', $k);
                $pattern = str_replace('%d', '(\d+)', $pattern);
                $pattern = '/^' . $pattern . '$/';

                if (preg_match($pattern, $text, $matches)) {
                    array_shift($matches);
                    $result = self::show($matches);
                    return vsprintf($v, $result);
                }
            }

            return $text;
        }

        foreach ($text as $k => $item) {
            $text[$k] = self::show($item);
        }

        return $text;
    }
}