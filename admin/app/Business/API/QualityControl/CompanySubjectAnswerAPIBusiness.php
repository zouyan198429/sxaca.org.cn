<?php
// 试题答案
namespace App\Business\API\QualityControl;


class CompanySubjectAnswerAPIBusiness extends BasePublicAPIBusiness
{
    public static $model_name = 'QualityControl\CompanySubjectAnswer';
    public static $table_name = 'company_subject_answer';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
}
