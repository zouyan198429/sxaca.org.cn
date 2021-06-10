<?php
// 限次配置
namespace App\Business\API\QualityControl;


class SmsLimitAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\SmsLimit';
    public static $table_name = 'sms_limit';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
