<?php

namespace App;

/**
 *  后台怎么解析token拿到用户信息。
 *  还有最后的一个问题， 后台根据怎么根据前端发送的access_token来查询到用户信息发送给前端呢。
 *
 *   打开你的User模型User.php，通常放在Http目录下。 在文件里的上面加上use Laravel\Passport\HasApiTokens;然后User类里，也别忘了加上 use Notifiable,HasApiTokens;这个就是用来根据token来获取user信息的。
 *
 *   #CustomerController@index方法里。
 *       $user = auth('api')->user();
 *      return $user;       //成功将用户信息返回给前端啦。
 *
 */
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
// use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens,MustVerifyEmail,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'confirmation_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

//    自定义用户名
//    Laravel 默认使用 email 字段来认证。如果你想用其他字段认证，可以在 LoginController 里面定义一个 username 方法：
//    public function username()
//    {
//        return 'username';
//    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * 获取会储存到 jwt 声明中的标识，其实就是要我们返回标识用户表的主键字段名称，这里是返回的是主键 'id',
     * @return mixed
     */
//    public function getJWTIdentifier()
//    {
//
//         return $this->getKey();
//    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * 返回一个键值数组，其中包含要添加到jwt的任何自定义声明。
     * 返回包含要添加到 jwt 声明中的自定义键值对数组，这里返回空数组，没有添加任何自定义信息。
     * @return array
     */
//    public function getJWTCustomClaims()
//    {
//
////        $tokenData = [
////            'userData' =>[
////                'name' => 1,
////                'email' => '我是邹燕aa',
////                'password' => '我是邹燕bb',
////            ]
////        ];
//      // return ['id' => 8, 'userData' =>'aaaaaa'];//$tokenData;
//        return [];
//    }

//    public function products()
//    {
//        return $this->hasMany(Product::class);
//    }

    // 如果用手机号进行授权的话,需要修改模型
    /**
     * [findForPassport passport通过手机号/账号验证]
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
//    public function findForPassport($username)
//    {
//        // if (!$this->where('phone', $username)->first()) {
//        //     return $this->where('name', $username)->first();
//        // }
//        // return true;
//
//        return $this->where('phone', $username)->first();
//
//        // return $this->where('phone', $username)->first() || $this->where('name', $username)->first();
//    }
// 对于账号验证不止是数据表中的 emial 字段，还可能是用户名或者手机号字段只需要在 User 模型中添加 findForPassport 方法，
//  示例代码如下:
//    public function findForPassport($login)
//    {
//        return $this->orWhere('email', $login)->orWhere('phone', $login)->first();
//    }
    /**
     * passport 验证用户名
     * @param $username
     * @return mixed
     */
//    public function findForPassport($username) {
//        return $this->where('FName', $username)->first();
//    }
    /**
     * passport 验证密码
     * 使用密码授予进行身份验证时，Passport将使用password模型的属性来验证给定的密码。如果您的模型没有password属性，
     * 或者您希望自定义密码验证逻辑，则可以validateForPassportPasswordGrant在模型上定义一个方法：
     * @param $password
     * @return bool
     */
//    public function validateForPassportPasswordGrant($password)
//    {
//        // 系统自带，需要引入 use Illuminate\Support\Facades\Hash;
//        // return Hash::check($password, $this->password);
//        return $this->FPwd == md5($password);
//    }

    /** Auth2.0 设置用户ID（创建关联关系，如果你本地没有报错，就不需要使用这一句）
     *  使用位置：Laravel\Passport\Bridge\UserRepository
     * @return mixed
     */
//    public function getAuthIdentifier()
//    {
//        return $this->id;
//    }

// 添加一个发送邮件的方法：
//    use App\Notifications\ResetPasswordNotification;
//    public function sendPasswordResetNotification($token)
//    {
//        $this->notify(new ResetPasswordNotification($token));
//    }
}
