<?php
// 人员
namespace App\Business\Controller\API\QualityControl;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIStaffHistoryBusiness extends CTAPIStaffBusiness
{
    public static $model_name = 'API\QualityControl\StaffHistoryAPI';
    public static $table_name = 'staff_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
