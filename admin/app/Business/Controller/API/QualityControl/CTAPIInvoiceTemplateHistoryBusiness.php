<?php
// 发票开票模板历史
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

class CTAPIInvoiceTemplateHistoryBusiness extends CTAPIInvoiceTemplateBusiness
{
    public static $model_name = 'API\QualityControl\InvoiceTemplateHistoryAPI';
    public static $table_name = 'invoice_template_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
