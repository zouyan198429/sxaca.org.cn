<?php
// 能力验证
namespace App\Business\API\QualityControl;


class CompanyAbilityAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyAbility';
    public static $table_name = 'company_ability';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
