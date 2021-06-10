<?php
// 付款/收款项目
namespace App\Business\API\QualityControl;


class PaymentProjectAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\PaymentProject';
    public static $table_name = 'payment_project';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
