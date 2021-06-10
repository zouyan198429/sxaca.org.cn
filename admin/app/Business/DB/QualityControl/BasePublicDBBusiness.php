<?php

namespace App\Business\DB\QualityControl;

use App\Business\DB\BaseDBBusiness;
use App\Services\Tool;


/**
 *
 */
class BasePublicDBBusiness extends BaseDBBusiness
{
    public static $database_model_dir_name = 'QualityControl';// 对应的数据库模型目录名称
    public static $model_name = '';// 相对于Models的数据模型名称;在子类中定义，使用时用static::,不用self::
    public static $table_name = '';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

}
