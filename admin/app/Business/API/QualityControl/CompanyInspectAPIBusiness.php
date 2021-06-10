<?php
// 监督检查
namespace App\Business\API\QualityControl;


class CompanyInspectAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyInspect';
    public static $table_name = 'company_inspect';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
