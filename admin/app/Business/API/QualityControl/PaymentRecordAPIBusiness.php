<?php
// 付款/收款记录
namespace App\Business\API\QualityControl;


class PaymentRecordAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\PaymentRecord';
    public static $table_name = 'payment_record';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
