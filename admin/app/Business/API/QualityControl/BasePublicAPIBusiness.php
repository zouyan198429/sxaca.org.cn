<?php
//
namespace App\Business\API\QualityControl;

use App\Business\API\BaseAPIBusiness;

class BasePublicAPIBusiness extends BaseAPIBusiness
{
    public static $database_model_dir_name = 'QualityControl';// 对应的数据库模型目录名称
    public static $model_name = '';// api接口的 模型名
    public static $APIRequestName = 'Sites\APIQualityControlRequest';// 具体的api request请求类名称
    public static $table_name = '';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
