<?php
// 企业到期配置
namespace App\Business\API\QualityControl;


class CompanyExpireAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyExpire';
    public static $table_name = 'company_expire';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
