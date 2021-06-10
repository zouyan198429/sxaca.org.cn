<?php
// 点播课程内容
namespace App\Business\API\QualityControl;


class VodsContentAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\VodsContent';
    public static $table_name = 'vods_content';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
