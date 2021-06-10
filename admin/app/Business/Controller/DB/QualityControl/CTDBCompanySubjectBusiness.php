<?php
// 试题
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanySubjectBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanySubject';
    public static $table_name = 'company_subject';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
