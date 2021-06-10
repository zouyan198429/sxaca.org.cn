<?php
// 机构自我声明管理
namespace App\Business\API\QualityControl;


class CompanyStatementAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyStatement';
    public static $table_name = 'company_statement';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
