<?php
// 资源模块使用表
namespace App\Business\API\QualityControl;


class ResourceModuleAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\ResourceModule';
    public static $table_name = 'resource_module';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
