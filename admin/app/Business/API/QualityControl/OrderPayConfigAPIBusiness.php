<?php
// 收款帐号配置
namespace App\Business\API\QualityControl;


class OrderPayConfigAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\OrderPayConfig';
    public static $table_name = 'order_pay_config';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
