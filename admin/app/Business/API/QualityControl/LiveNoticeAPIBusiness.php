<?php
// 直播公告
namespace App\Business\API\QualityControl;


class LiveNoticeAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\LiveNotice';
    public static $table_name = 'live_notice';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
