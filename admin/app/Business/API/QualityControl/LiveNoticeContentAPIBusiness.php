<?php
// 直播公告内容
namespace App\Business\API\QualityControl;


class LiveNoticeContentAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\LiveNoticeContent';
    public static $table_name = 'live_notice_content';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
