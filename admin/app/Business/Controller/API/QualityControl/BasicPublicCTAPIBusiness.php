<?php
namespace App\Business\Controller\API\QualityControl;

use App\Business\Controller\API\BasicCTAPIBusiness;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as Controller;

class BasicPublicCTAPIBusiness extends BasicCTAPIBusiness
{
    public static $database_model_dir_name = 'QualityControl';// 对应的数据库模型目录名称
    public static $model_name = '';
    public static $table_name = '';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
