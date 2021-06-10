<?php
// 分数等级历史
namespace App\Business\API\QualityControl;


class CompanyCoreGradeHistoryAPIBusiness extends CompanyCoreGradeAPIBusiness
{
    public static $model_name = 'QualityControl\CompanyCoreGradeHistory';
    public static $table_name = 'company_core_grade_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
