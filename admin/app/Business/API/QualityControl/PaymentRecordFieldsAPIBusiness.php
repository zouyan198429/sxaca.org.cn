<?php
// 付款/收款记录字段
namespace App\Business\API\QualityControl;


class PaymentRecordFieldsAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\PaymentRecordFields';
    public static $table_name = 'payment_record_fields';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
