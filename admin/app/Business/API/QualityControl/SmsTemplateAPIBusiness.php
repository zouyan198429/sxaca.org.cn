<?php
// 短信模板
namespace App\Business\API\QualityControl;


class SmsTemplateAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\SmsTemplate';
    public static $table_name = 'sms_template';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
