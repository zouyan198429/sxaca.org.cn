<?php
// 付款/收款记录流水
namespace App\Business\API\QualityControl;


class PaymentRecordFlowAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\PaymentRecordFlow';
    public static $table_name = 'payment_record_flow';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
