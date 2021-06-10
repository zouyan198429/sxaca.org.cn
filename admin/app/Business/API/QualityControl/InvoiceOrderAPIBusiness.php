<?php
// 订单发票关联表
namespace App\Business\API\QualityControl;


class InvoiceOrderAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\InvoiceOrder';
    public static $table_name = 'invoice_order';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
