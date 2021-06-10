<?php
// 选民表
namespace App\Business\DB\QualityControl;

use App\Models\QualityControl\Voters;
use App\Services\DB\CommonDB;
use App\Services\Tool;

/**
 *
 */
class VotersDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\Voters';
    public static $table_name = 'voters';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];


    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param array $saveData 要保存或修改的数组
     * @param int  $company_id 企业id
     * @param int $id id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  int 记录id值，
     * @author zouyan(305463219@qq.com)
     */
    public static function replaceById($saveData, $company_id, &$id, $operate_staff_id = 0, $modifAddOprate = 0){

//        DB::beginTransaction();
//        try {
//            DB::commit();
//        } catch ( \Exception $e) {
//            DB::rollBack();
//            throws($e->getMessage());
//            // throws($e->getMessage());
//        }
        //        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }
//
//        if(isset($saveData['mobile']) && empty($saveData['mobile'])  ){
//            throws('手机不能为空！');
//        }

        // 组号
        $group_no = '';
        $has_group_no = false;// 是否有方法修改 false:没有 ； true:有
        Tool::getInfoUboundVal($saveData, 'group_no', $has_group_no, $group_no, 0);

        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history
            , &$modelObj, &$isModify, &$group_no, &$has_group_no ){


            // 如果有组号
            if($has_group_no){
                // 能力验证单次结果
                $updateFields = [
                    'group_no' => $group_no,
                ];
                $searchConditon = [
                    'group_no' => $group_no,
                ];
                $resultObj = null;
                VoterGroupDBBusiness::updateOrCreate($resultObj, $searchConditon, $updateFields );
                $result_id = $resultObj->id;
                $saveData['group_no_id'] = $result_id;
            }

            // $ownProperty  自有属性值;
            // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
            list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
            if($id > 0){
                $isModify = true;
                // 判断权限
                //            $judgeData = [
                //                'company_id' => $company_id,
                //            ];
                //            $relations = '';
                //            static::judgePower($id, $judgeData , $company_id , [], $relations);
                if($temNeedStaffIdOrHistoryId && $modifAddOprate) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);

            }else {// 新加;要加入的特别字段
                //            $addNewData = [
                //                'company_id' => $company_id,
                //            ];
                //            $saveData = array_merge($saveData, $addNewData);
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($saveData, $operate_staff_id,$operate_staff_id_history, 1);
            }

            // 新加或修改
            if($id <= 0){// 新加
                $resultDatas = static::create($saveData,$modelObj);
                $id = $resultDatas['id'] ?? 0;
            }else{// 修改
                $saveBoolen = static::saveById($saveData, $id,$modelObj);
                // $resultDatas = static::getInfo($id);
                // 修改数据，是否当前版本号 + 1
                // 1：有历史表 ***_history;
                // if(($ownProperty & 1) == 1) static::compareHistory($id, 1);
            }
            if($isModify && ($ownProperty & 1) == 1){// 1：有历史表 ***_history;
                static::compareHistory($id, 1);
            }

        });
        // ************************************************
        // 保存成功后的处理
        static::replaceByIdAPISucess($isModify, $modelObj, $saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        return $id;
    }



    /**
     * 根据id新加或修改单条数据-id 为0 新加，返回新的对象数组[-维],  > 0 ：修改对应的记录，返回true
     *
     * @param int  $group_type 组序号(1：一组；2：二组；4：三组；8：四组；16：五组)
     * @param array $saveData 要导入的数组 -- 二维数组
     * @param int  $company_id 企业id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  array 单条数据 - 记录的id数组--一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function importData($group_type, $saveData, $company_id, $operate_staff_id = 0, $modifAddOprate = 0){
        Tool::phpInitSet();
        $returnIds = [];
        if(empty($saveData)) return $returnIds;
        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表

        // $ownProperty  自有属性值;
        // $temNeedStaffIdOrHistoryId 当只有自己会用到时操作员工id和历史id时，用来判断是否需要获取 true:需要获取； false:不需要获取
        list($ownProperty, $temNeedStaffIdOrHistoryId) = array_values(static::getNeedStaffIdOrHistoryId());
        $groupTypeArr = Voters::$groupTypeArr;
        $group_name = $groupTypeArr[$group_type] ?? '';// array_search($group_type, $groupTypeArr);
        // if($group_name === false) throws('参数【group_type】有误');// $sex_id = 0;
//        $errsArr = [];
        $new_group_no = '';
        foreach($saveData as $k => &$info) {
            $recordErrText = '';
            $real_name = $info['real_name'] ?? '';// 用户名
            if(empty($real_name)){
                unset($saveData[$k]);
                continue;//  $recordErrText .= '姓名不能为空!<br/>';
            }
//            $group_type = $info['group_type'] ?? '';// 性别
//            $group_name = array_search($group_type, $groupTypeArr);
//            if($group_name === false) $recordErrText .= '组序号名有效值[' . implode('、', $groupTypeArr) . ']!<br/>';// $sex_id = 0;
            $info['group_type'] = $group_type;
            $info['group_name'] = $group_name;
            $group_no = $info['group_no'];
            if(!empty($group_no)) $new_group_no = $group_no;
            if(empty($group_no)) $info['group_no'] = $new_group_no;
            $id_number = $info['id_number'];
            $birth_date = '';
            if(!empty($id_number)){
                $strLen = strlen($id_number);
                // '610427195810130017
                if($strLen >= 10 ){
                    $birth_date = substr($id_number,6,8);
               // }elseif(){

                }elseif($strLen >= 6){
                    $birth_date = $id_number;
                }
            }

            if(!empty($birth_date)){
                $alen = strlen($birth_date);
                $dateStr = '';
                if($alen >= 4){
                    $year = substr($birth_date,0,4);// 年
                    $info['birth_year'] = $year;
                    $dateStr .= $year;
                }

                if($alen >= 6) {// 月
                    $month = substr($birth_date,4,2);
                    if(is_numeric($month) && $month <= 12 ) $info['birth_month'] = $month;
                    $dateStr .= '-' . $month;
                }

                if($alen >= 8){
                    $day = substr($birth_date,6,2);// 日
                    if(is_numeric($day) && $day <= 32 ) $info['birth_day'] = $day;
                    $dateStr .= '-' . $day;
                }else{
                    // $dateStr .= '-01';// 日
                }

                if(judgeDate($dateStr) !== false && strlen($dateStr) >= 8 && (!in_array($dateStr,['1994-02-29']))) $info['birth_date'] = $dateStr;

            }
//            if(!empty($recordErrText)){
//                array_push($errsArr,'第' . ($k + 1) . '条记录[' . $real_name . ']:<br/>' . $recordErrText);
//            }

        }
        // throws(json_encode($saveData));
        // 如果有错，则返回错误
//        if(!empty($errsArr)) throws(implode('<br/>', $errsArr));
        // 对数据进行修改或新加

        CommonDB::doTransactionFun(function() use(&$saveData, &$temNeedStaffIdOrHistoryId,&$operate_staff_id, &$operate_staff_id_history, &$company_id, &$returnIds, &$modifAddOprate ){

            foreach($saveData as $k => $info){
                $id = $info['id'] ?? 0;
                if(isset($info['id'])) unset($info['id']);
                // 加入操作人员信息
                if($temNeedStaffIdOrHistoryId) static::addOprate($info, $operate_staff_id,$operate_staff_id_history, 1);

                // 新加或更新
                static::replaceById($info, $company_id, $id, $operate_staff_id, $modifAddOprate);
                array_push($returnIds, $id);

            }

        });
        return $returnIds;
    }

}
