<?php
return [
    'grant_type' => env('OAUTH_GRANT_TYPE'),
    'client_id' => env('OAUTH_CLIENT_ID'),
    'client_secret' => env('OAUTH_CLIENT_SECRET'),
    'scope' => env('OAUTH_SCOPE', '*'),
    /*
    |--------------------------------------------------------------------------
    | Encryption Keys 加密密钥
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys while generating secure access tokens for
    | your application. By default, the keys are stored as local files but
    | can be set via environment variables when that is more convenient.
    | Passport在为应用程序生成安全访问令牌时使用加密密钥。默认情况下，密钥存储为本地文件，但是可以在更方便的时候通过环境变量进行设置。
    | 您也可以使用 php artisan vendor:publish --tag=passport-config 发布 Passport 的配置文件，然后从您的环境变量提供加载加密密钥的选项
    | PASSPORT_PRIVATE_KEY="-----BEGIN RSA PRIVATE KEY-----
    | <此处填写您的私钥>
    | -----END RSA PRIVATE KEY-----"
    |
    | PASSPORT_PUBLIC_KEY="-----BEGIN PUBLIC KEY-----
    | <此处填写您的公钥>
    | -----END PUBLIC KEY-----"
    */

//    'private_key' => env('PASSPORT_PRIVATE_KEY'),
//    'public_key' => env('PASSPORT_PUBLIC_KEY'),
];