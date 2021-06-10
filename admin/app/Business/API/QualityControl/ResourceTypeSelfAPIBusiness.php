<?php
// 资源分类自定义[一级分类]
namespace App\Business\API\QualityControl;


class ResourceTypeSelfAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\ResourceTypeSelf';
    public static $table_name = 'resource_type_self';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
