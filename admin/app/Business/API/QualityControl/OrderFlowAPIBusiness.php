<?php
// 收款订单财务流水
namespace App\Business\API\QualityControl;


class OrderFlowAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\OrderFlow';
    public static $table_name = 'order_flow';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
