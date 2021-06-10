<?php
// 行业[一级分类]
namespace App\Business\DB\QualityControl;

use App\Models\QualityControl\Staff;
use App\Services\Tool;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class IndustryDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\Industry';
    public static $table_name = 'industry';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];

    /**
     * 分组获得各行业的企业数量
     * @return array 二维数组 [[ 'industry_id' => '行业id','industry_name'=> '行业名称', 'company_count' => '企业数量0']]
     * @author zouyan(305463219@qq.com)
     */
    //
    public static function getCompanyNumGroup(){
        // 获得行业的kv
        $queryParams = Tool::getParamQuery([], ['sqlParams' => ['orderBy' =>['sort_num' => 'asc', 'id' => 'desc']]]);
        $industrys_kv = static::getKeyVals(['key' => 'id', 'val' => 'industry_name'], [], $queryParams);
        if(empty($industrys_kv)) return [];

        $where = [['admin_type' ,2], ['is_perfect', 2], ['open_status', 2], ['account_status', 1]];
        $companyObj = Staff::where($where);
        // 是数组
//        if(!empty($status) && is_array($status)){
//            $orderObj = $companyObj->whereIn('status',$status);
//        }
        //
        $dataList = $companyObj->select(DB::raw('count(*) as company_count, company_industry_id as industry_id'))
            ->groupBy('company_industry_id')
            ->orderBy('company_count', 'desc')
            ->get()->toArray();
        $industrys_num_kv =  Tool::formatArrKeyVal($dataList, 'industry_id', 'company_count');
//        foreach($dataList as $k => $v){
//            if(!isset($industrys_kv[$v['industry_id']])){
//                unset($dataList[$k]);
//                continue;
//            }
//            $dataList[$k]['industry_name'] = $industrys_kv[$v['industry_id']];
//        }
        $returnArr = [];
        foreach($industrys_kv as $k => $v){
            array_push($returnArr, [
                'industry_id' => $k,
                'industry_name'=> $v,
                'company_count' => $industrys_num_kv[$k] ?? 0
            ]);
        }
        // return array_values($dataList);
        return $returnArr;
    }

    /**
     * 分组获得各行业的企业类型数量
     * @return array
     * @author zouyan(305463219@qq.com)
     */
    //
    public static function getCompanyGradeNumGroup(){
        // 获得行业的kv
        $queryParams = Tool::getParamQuery([], ['sqlParams' => ['orderBy' =>['sort_num' => 'asc', 'id' => 'desc']]]);
        $industrys_kv = static::getKeyVals(['key' => 'id', 'val' => 'industry_name'], [], $queryParams);
        $grade_KV = Staff::$companyGradeArr;
        if(empty($industrys_kv) || empty($grade_KV)) return [
            'industry_kv' => $industrys_kv,
            'grade_kv' => $grade_KV,
            'data_list' => [],
        ];

        $where = [['admin_type' ,2], ['is_perfect', 2], ['open_status', 2], ['account_status', 1]];
        $companyObj = Staff::where($where);
        // 是数组
//        if(!empty($status) && is_array($status)){
//            $orderObj = $companyObj->whereIn('status',$status);
//        }
        //
        $dataList = $companyObj->select(DB::raw('count(*) as company_count,  company_industry_id as industry_id, company_grade'))
            ->groupBy('company_industry_id')
            ->groupBy('company_grade')
            // ->orderBy('company_count', 'desc')
            ->get()->toArray();
        foreach($dataList as $k => $v){
            $dataList[$k]['industry_name'] = $industrys_kv[$v['industry_id']] ?? '';
            $dataList[$k]['grade_name'] = $grade_KV[$v['company_grade']] ?? '';
        }
        $dataList = Tool::arrUnderReset($dataList, 'industry_id,company_grade', 1, '_');
        $returnArr = [
            'industry_kv' => $industrys_kv,
            'grade_kv' => $grade_KV,
            'data_list' => $dataList,
        ];
        return $returnArr;
    }
}
