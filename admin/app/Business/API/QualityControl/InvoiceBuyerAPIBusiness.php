<?php
// 发票配置购买方
namespace App\Business\API\QualityControl;


class InvoiceBuyerAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\InvoiceBuyer';
    public static $table_name = 'invoice_buyer';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
