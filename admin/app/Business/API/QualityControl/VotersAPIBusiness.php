<?php
// 选民表
namespace App\Business\API\QualityControl;


class VotersAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\Voters';
    public static $table_name = 'voters';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
