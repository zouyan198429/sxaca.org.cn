<?php
namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Login extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // 表名
     protected $table = 'data_login_info';

     // 主键
     protected $primaryKey = 'guid';

    // 主键类型
    protected $keyType = 'string';

    /**
     * 说明:自定义授权用户名（默认为email）
     *
     * @param $username
     * @return \Illuminate\Database\Eloquent\Model|null|static
     * @author 郭庆
     */
    public function findForPassport($username)
    {
        return CompanyLogin::where('username', $username)->first();
    }

}

