<?php
// 能力验证类型-行业分类[一级分类]
namespace App\Business\API\QualityControl;


class AbilityTypeAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\AbilityType';
    public static $table_name = 'ability_type';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
