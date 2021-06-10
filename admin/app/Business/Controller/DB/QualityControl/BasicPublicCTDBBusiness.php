<?php
namespace App\Business\Controller\DB\QualityControl;

use App\Business\Controller\DB\BasicCTDBBusiness;
use App\Services\Response\Data\CommonAPIFromDBBusiness;
use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;

class BasicPublicCTDBBusiness extends BasicCTDBBusiness
{
    public static $database_model_dir_name = 'QualityControl';// 对应的数据库模型目录名称
    public static $model_name = '';// 中间层 App\Business\DB 下面的表名称 QualityControl\CountSenderReg
    public static $table_name = '';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
