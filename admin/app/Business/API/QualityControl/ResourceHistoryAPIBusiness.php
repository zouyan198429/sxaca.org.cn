<?php
// 资源历史
namespace App\Business\API\QualityControl;


class ResourceHistoryAPIBusiness extends ResourceAPIBusiness
{
    public static $model_name = 'QualityControl\ResourceHistory';
    public static $table_name = 'resource_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
