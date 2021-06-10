<?php
// 试题答案历史
namespace App\Business\API\QualityControl;


class CompanySubjectAnswerHistoryAPIBusiness extends CompanySubjectAnswerAPIBusiness
{
    public static $model_name = 'QualityControl\CompanySubjectAnswerHistory';
    public static $table_name = 'company_subject_answer_history';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
