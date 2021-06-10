<?php
// 监督检查信息管理
namespace App\Business\API\QualityControl;


class CompanySuperviseAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanySupervise';
    public static $table_name = 'company_supervise';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
