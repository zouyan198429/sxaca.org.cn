<?php
// 付款/收款类型
namespace App\Business\API\QualityControl;


class PaymentTypeAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\PaymentType';
    public static $table_name = 'payment_type';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
