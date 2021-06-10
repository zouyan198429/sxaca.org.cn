<?php
// 能力验证取样登记表
namespace App\Business\API\QualityControl;


class AbilityJoinItemsSamplesAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsSamples';
    public static $table_name = 'ability_join_items_samples';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
