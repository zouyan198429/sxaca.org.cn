<?php
namespace App\ModelsVerify\QualityControl;


use App\ModelsVerify\BaseVerify;

class BaseDBVerify extends BaseVerify
{
    public static $model_name = '';// 相对于Models的数据模型名称;在子类中定义，使用时用static::,不用self::
    public static $dbDir = 'QualityControl';// 区分多数据库的数据目录
    // 需要从父的去掉的字段  -- 一维数组
    // 如 ['version_history_id', 'version_num_history']
    public static $delFields = [];
}
