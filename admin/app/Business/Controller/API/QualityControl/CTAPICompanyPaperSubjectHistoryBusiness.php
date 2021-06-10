<?php
// 试卷试题
namespace App\Business\Controller\API\QualityControl;

use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;

class CTAPICompanyPaperSubjectHistoryBusiness extends CTAPICompanyPaperSubjectBusiness
{
    public static $model_name = 'API\QualityControl\CompanyPaperSubjectHistoryAPI';
    public static $table_name = 'company_paper_subject_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
