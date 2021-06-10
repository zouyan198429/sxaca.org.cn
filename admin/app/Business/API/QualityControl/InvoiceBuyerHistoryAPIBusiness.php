<?php
// 发票配置购买方历史
namespace App\Business\API\QualityControl;


class InvoiceBuyerHistoryAPIBusiness extends InvoiceBuyerAPIBusiness
{
    public static $model_name = 'QualityControl\InvoiceBuyerHistory';
    public static $table_name = 'invoice_buyer_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
