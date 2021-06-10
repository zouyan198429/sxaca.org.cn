<?php
// 发票开票模板
namespace App\Business\API\QualityControl;


class InvoiceTemplateAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\InvoiceTemplate';
    public static $table_name = 'invoice_template';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
