<?php
// 行业[一级分类]
namespace App\Business\API\QualityControl;


class IndustryAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\Industry';
    public static $table_name = 'industry';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
