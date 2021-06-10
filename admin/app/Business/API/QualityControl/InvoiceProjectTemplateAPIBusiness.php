<?php
// 发票商品项目模板
namespace App\Business\API\QualityControl;


class InvoiceProjectTemplateAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\InvoiceProjectTemplate';
    public static $table_name = 'invoice_project_template';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
