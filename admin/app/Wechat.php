<?php

namespace App;

// Laravel Passport + 微信小程序完成OAuth2.0认证登录
// https://liwei2.com/2018/07/18/2902.html
// 然后将你的API保护起来，因为我的route写在api.php，使用了api路由组，所以：
// Route::group(['middleware' => 'auth:wechat_api'] , function () {
// Route::group(['middleware' => 'auth:api'] , function () {
//    ...
// });
// 这样配置完成后，在被保护的API中，即可使用
//
//\Auth::user();
//或者
//$request->user();

use Illuminate\Support\Facades\Crypt;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Wechat extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // 并且在其中定义用户名和密码处理函数，方便Passport进行验证
    // 比如我的Wechat表中使用了openId作为主键，可以定义：
    public function findForPassport($username) {
        return $this->where('openId', $username)->first();
    }

    // 为了保障安全，密码为加密后的openId，所以我定义了密码验证函数为
    public function validateForPassportPasswordGrant($password)
    {
        $decrypted = Crypt::decryptString($password);
        if ($decrypted == $this->openId) {
            return true;
        }
        return false;
    }

}