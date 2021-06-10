<?php
// 试题历史
namespace App\Business\API\QualityControl;


class CompanySubjectHistoryAPIBusiness extends CompanySubjectAPIBusiness
{
    public static $model_name = 'QualityControl\CompanySubjectHistory';
    public static $table_name = 'company_subject_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
