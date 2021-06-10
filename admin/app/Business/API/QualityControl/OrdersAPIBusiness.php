<?php
// 收款订单
namespace App\Business\API\QualityControl;


class OrdersAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\Orders';
    public static $table_name = 'orders';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
