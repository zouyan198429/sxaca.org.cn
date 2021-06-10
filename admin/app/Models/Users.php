<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Symfony\Contracts\Translation\TranslatorTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements Transformable
{
    use  HasApiTokens, SoftDeletes, Notifiable;// TransformableTrait,

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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
     * Get the identifier that will be stored in the subject claim of the JWT.
     * 获取会储存到 jwt 声明中的标识，其实就是要我们返回标识用户表的主键字段名称，这里是返回的是主键 'id',
     * @return mixed
     */
    public function getJWTIdentifier()
    {

        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * 返回一个键值数组，其中包含要添加到jwt的任何自定义声明。
     * 返回包含要添加到 jwt 声明中的自定义键值对数组，这里返回空数组，没有添加任何自定义信息。
     * @return array
     */
    public function getJWTCustomClaims()
    {

//        $tokenData = [
//            'userData' =>[
//                'name' => 1,
//                'email' => '我是邹燕aa',
//                'password' => '我是邹燕bb',
//            ]
//        ];
        // return ['id' => 8, 'userData' =>'aaaaaa'];//$tokenData;
        return [];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

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

//    public function findForPassport($login)
//    {
//        return $this->orWhere('email', $login)->orWhere('phone', $login)->first();
//    }

    public function findForPassport($login)
    {
        return $this->orWhere('email', $login)->orWhere('phone', $login)->first();
    }
}