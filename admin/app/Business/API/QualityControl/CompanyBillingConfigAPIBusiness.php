<?php
// 企业开票配置信息
namespace App\Business\API\QualityControl;


class CompanyBillingConfigAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyBillingConfig';
    public static $table_name = 'company_billing_config';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
