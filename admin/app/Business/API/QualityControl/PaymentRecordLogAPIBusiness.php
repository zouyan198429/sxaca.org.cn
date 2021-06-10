<?php
// 付款/收款记录操作日志
namespace App\Business\API\QualityControl;


class PaymentRecordLogAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\PaymentRecordLog';
    public static $table_name = 'payment_record_log';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
