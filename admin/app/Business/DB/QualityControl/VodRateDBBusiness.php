<?php
// 点播课程学员学习进度
namespace App\Business\DB\QualityControl;

/**
 *
 */
class VodRateDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\VodRate';
    public static $table_name = 'vod_rate';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];
}
