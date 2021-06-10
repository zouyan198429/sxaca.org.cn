<?php
// 注册记录
namespace App\Business\API\QualityControl;


class RegLogAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\RegLog';
    public static $table_name = 'reg_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
