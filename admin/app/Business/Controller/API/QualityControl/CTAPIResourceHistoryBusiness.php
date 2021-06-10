<?php
// 资源历史
namespace App\Business\Controller\API\QualityControl;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIResourceHistoryBusiness extends CTAPIResourceBusiness
{
    public static $model_name = 'API\QualityControl\ResourceHistoryAPI';
    public static $table_name = 'resource_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
