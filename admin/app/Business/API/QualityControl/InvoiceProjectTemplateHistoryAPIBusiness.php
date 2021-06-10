<?php
// 发票商品项目模板历史
namespace App\Business\API\QualityControl;


class InvoiceProjectTemplateHistoryAPIBusiness extends InvoiceProjectTemplateAPIBusiness
{
    public static $model_name = 'QualityControl\InvoiceProjectTemplateHistory';
    public static $table_name = 'invoice_project_template_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
