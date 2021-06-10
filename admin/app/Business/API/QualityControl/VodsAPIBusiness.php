<?php
// 点播课程
namespace App\Business\API\QualityControl;


class VodsAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\Vods';
    public static $table_name = 'vods';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
