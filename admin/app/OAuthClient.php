<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class OAuthClient extends Model
{

    /**
     * The attributes that aren't mass assignable.
     * 所有属性都是可批量赋值
     * @var array
     */
    protected $guarded = [];

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'oauth_clients';
}