<?php
// 资源分类自定义历史[一级分类]
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBResourceTypeSelfHistoryBusiness extends CTDBResourceTypeSelfBusiness
{
    public static $model_name = 'QualityControl\ResourceTypeSelfHistory';
    public static $table_name = 'resource_type_self_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
