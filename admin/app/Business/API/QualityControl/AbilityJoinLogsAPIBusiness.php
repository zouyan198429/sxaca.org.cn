<?php
// 能力验证操作日志
namespace App\Business\API\QualityControl;


class AbilityJoinLogsAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinLogs';
    public static $table_name = 'ability_join_logs';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
