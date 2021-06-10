<?php
// 试卷试题
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanyPaperSubjectBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanyPaperSubject';
    public static $table_name = 'company_paper_subject';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
