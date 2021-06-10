<?php
// 点播课程学员学习进度
namespace App\Business\API\QualityControl;


class VodRateAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\VodRate';
    public static $table_name = 'vod_rate';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
