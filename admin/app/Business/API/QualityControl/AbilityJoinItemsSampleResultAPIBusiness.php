<?php
// 能力验证取样登记样品结果
namespace App\Business\API\QualityControl;


class AbilityJoinItemsSampleResultAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsSampleResult';
    public static $table_name = 'ability_join_items_sample_result';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
