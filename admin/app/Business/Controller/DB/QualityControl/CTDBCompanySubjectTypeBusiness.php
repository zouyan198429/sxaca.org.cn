<?php
// 试题分类[一级分类]
namespace App\Business\Controller\DB\QualityControl;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBCompanySubjectTypeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'QualityControl\CompanySubjectType';
    public static $table_name = 'company_subject_type';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

}
