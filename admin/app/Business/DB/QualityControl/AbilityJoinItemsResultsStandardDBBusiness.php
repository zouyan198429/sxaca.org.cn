<?php
// 能力验证检测标准物质
namespace App\Business\DB\QualityControl;

/**
 *
 */
class AbilityJoinItemsResultsStandardDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\AbilityJoinItemsResultsStandard';
    public static $table_name = 'ability_join_items_results_standard';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];
}
