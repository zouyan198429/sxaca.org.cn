<?php
// 短信日志
namespace App\Business\API\QualityControl;


class SmsLogAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\SmsLog';
    public static $table_name = 'sms_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
