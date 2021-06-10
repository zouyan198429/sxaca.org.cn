<?php
// 订单发票流水
namespace App\Business\API\QualityControl;


class InvoiceOrderFlowAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\InvoiceOrderFlow';
    public static $table_name = 'invoice_order_flow';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
