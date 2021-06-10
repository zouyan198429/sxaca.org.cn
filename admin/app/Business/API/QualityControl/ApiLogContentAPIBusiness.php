<?php
// 接口调用日志数据
namespace App\Business\API\QualityControl;


class ApiLogContentAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\ApiLogContent';
    public static $table_name = 'api_log_content';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
