<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Business\Controller\API\QualityControl\CTAPIAbilityJoinBusiness;
use App\Business\Controller\API\QualityControl\CTAPIAbilityJoinItemsBusiness;
use App\Business\Controller\API\QualityControl\CTAPISmsTemplateBusiness;
use App\Http\Controllers\WorksController;
use App\Models\QualityControl\AbilityJoin;
use App\Services\Request\CommonRequest;
use App\Services\Tool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function Stringy\create;

class AbilityJoinController extends BasicController
{
    public $controller_id =0;// 功能小模块[控制器]id - controller_id  历史表 、正在进行表 与原表相同

    /**
     * 首页
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function index(Request $request)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            return view('admin.QualityControl.AbilityJoin.index', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 1, 1, 'admin.QualityControl.AbilityJoin.index', true
            , 'doListPage', [], function (&$reDataArr) use ($request){

            });

    }

    /**
     * 同事选择-弹窗
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function select(Request $request)
//    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//            $reDataArr['province_kv'] = CTAPIAbilityJoinBusiness::getCityByPid($request, $this,  0);
//            $reDataArr['province_kv'] = CTAPIAbilityJoinBusiness::getChildListKeyVal($request, $this, 0, 1 + 0, 0);
//            $reDataArr['province_id'] = 0;
//            return view('admin.QualityControl.AbilityJoin.select', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 2048, 1, 'admin.QualityControl.RrrDddd.select', true
//            , 'doListPage', [], function (&$reDataArr) use ($request){
//
//            });
//    }

    /**
     * 添加
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function add(Request $request,$id = 0)
//    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);

//            return view('admin.QualityControl.AbilityJoin.add', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);

//        $pageNum = ($id > 0) ? 64 : 16;
//        return $this->exeDoPublicFun($request, $pageNum, 1,'admin.QualityControl.AbilityJoin.add', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * 取样
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function get_sample(Request $request,$id = 0)
    {
//        $reDataArr = [];// 可以传给视图的全局变量数组
//        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
//            // 正常流程的代码
//
//            $this->InitParams($request);
//            // $reDataArr = $this->reDataArr;
//            $reDataArr = array_merge($reDataArr, $this->reDataArr);
//
//            return view('admin.QualityControl.AbilityJoin.get_sample', $reDataArr);
//
//        }, $this->errMethod, $reDataArr, $this->errorView);
        return $this->exeDoPublicFun($request, 0, 8, 'admin.QualityControl.AbilityJoin.get_sample', true
            , '', [], function (&$reDataArr) use ($request, &$id){

                if(!is_numeric($id) || $id <= 0){
                    throws('参数[id]有误！');
                }
                $operate = "取样";
                // $handleKeyArr = ['joinItems'];
                $extParams = [
                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                    'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['company_info_data' ,'join_items_get'], []),// , 'join_items'
                ];

                $info = CTAPIAbilityJoinBusiness::getInfoData($request, $this, $id, [], '', $extParams);
                // $reDataArr = array_merge($reDataArr, $resultDatas);
                if(empty($info)) {
                    throws('记录不存在！');
                }

                $reDataArr['info'] = $info;
                $reDataArr['operate'] = $operate;
            });
    }

    /**
     * 取样
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function print(Request $request,$id = 0)
    {
        return $this->exeDoPublicFun($request, 0, 8, 'admin.QualityControl.AbilityJoin.print', false
            , '', [], function (&$reDataArr) use ($request, &$id){

                if(!is_numeric($id) || $id <= 0){
                    throws('参数[id]有误！');
                }
                $operate = "打印证书";
                // $handleKeyArr = ['joinItems'];
                $extParams = [
                    // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                    'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['company_info_certificate' ,'join_items_print', 'ability_code_info'], []),// , 'join_items'
                ];

                $info = CTAPIAbilityJoinBusiness::getInfoData($request, $this, $id, [], '', $extParams, 1);
                // $reDataArr = array_merge($reDataArr, $resultDatas);
                if(empty($info)) {
                    throws('记录不存在！');
                }
                $reDataArr['info'] = $info;
                $reDataArr['operate'] = $operate;
            });
    }

    /**
     * 查看
     *
     * @param Request $request
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function info(Request $request,$id = 0)
    {
        $reDataArr = [];// 可以传给视图的全局变量数组
        return Tool::doViewPages($this, $request, function (&$reDataArr) use($request, &$id){
            // 正常流程的代码

            $this->InitParams($request);
            // $reDataArr = $this->reDataArr;
            $reDataArr = array_merge($reDataArr, $this->reDataArr);
            if(!is_numeric($id) || $id <= 0){
                throws('参数[id]有误！');
            }
            $operate = "详情";
            // $handleKeyArr = ['joinItems'];
            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['join_items'], []),
            ];

            $info = CTAPIAbilityJoinBusiness::getInfoData($request, $this, $id, [], '', $extParams);
            // $reDataArr = array_merge($reDataArr, $resultDatas);
            if(empty($info)) {
                throws('记录不存在！');
            }
            $reDataArr['info'] = $info;
            $reDataArr['operate'] = $operate;
            return view('admin.QualityControl.AbilityJoin.info', $reDataArr);

        }, $this->errMethod, $reDataArr, $this->errorView);
//        return $this->exeDoPublicFun($request, 17179869184, 1,'web.QualityControl.admin.info', true
//            , 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//            });
    }

    /**
     * @OA\Get(
     *     path="/api/admin/ability_join/ajax_info",
     *     tags={"大后台-能力验证-能力验证报名"},
     *     summary="能力验证报名--详情",
     *     description="根据单个id,查询详情记录......",
     *     operationId="adminQualityControlAbilityJoinAjax_info",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_info_ability_join"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join"}
     */
    /**
     * ajax获得详情数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_info(Request $request){
//        $this->InitParams($request);
//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        $info = CTAPIAbilityJoinBusiness::getInfoData($request, $this, $id, [], '', []);
//        $resultDatas = ['info' => $info];
//        return ajaxDataArr(1, $resultDatas, '');

//        $id = CommonRequest::getInt($request, 'id');
//        if(!is_numeric($id) || $id <=0) return ajaxDataArr(0, null, '参数[id]有误！');
//        return $this->exeDoPublicFun($request, 128, 2,'', true, 'doInfoPage', ['id' => $id], function (&$reDataArr) use ($request){
//
//        });
//    }

    /**
     * @OA\Post(
     *     path="/api/admin/ability_join/ajax_save",
     *     tags={"大后台-能力验证-能力验证报名"},
     *     summary="能力验证报名--新加/修改",
     *     description="根据单个id,新加/修改记录(id>0:修改；id=0:新加)......",
     *     operationId="adminQualityControlAbilityJoinAjax_save",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_modify"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join"}
     */

    /**
     * ajax保存数据
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_save(Request $request)
//    {
////        $this->InitParams($request);
//
//        $id = CommonRequest::getInt($request, 'id');
//        $pageNum = ($id > 0) ? 256 : 32;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true
//            , '', [], function (&$reDataArr) use ($request){
//                $id = CommonRequest::getInt($request, 'id');
//                // CommonRequest::judgeEmptyParams($request, 'id', $id);
//                $type_name = CommonRequest::get($request, 'type_name');
//                $sort_num = CommonRequest::getInt($request, 'sort_num');
//
//                $saveData = [
//                    'type_name' => $type_name,
//                    'sort_num' => $sort_num,
//                ];
//
////        if($id <= 0) {// 新加;要加入的特别字段
////            $addNewData = [
////                // 'account_password' => $account_password,
////            ];
////            $saveData = array_merge($saveData, $addNewData);
////        }
//                $extParams = [
//                    'judgeDataKey' => 'replace',// 数据验证的下标
//                ];
//                $resultDatas = CTAPIAbilityJoinBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
//                return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * ajax保存数据--取样
     *
     * @param int $id
     * @return Response
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_save_sample(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::getInt($request, 'id');// 报名id
        // CommonRequest::judgeEmptyParams($request, 'id', $id);
        //$type_name = CommonRequest::get($request, 'type_name');
        //$sort_num = CommonRequest::getInt($request, 'sort_num');

        // 获得报名信息
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['join_items_sample_save'], []),// , 'join_items'
        ];

        $info = CTAPIAbilityJoinBusiness::getInfoData($request, $this, $id, [], '', $extParams);
        if(empty($info)) throws('记录不存在！');
        $join_retry_no = $info['retry_no'] ?? '';
        $first_submit_num = $info['first_submit_num'] ?? 0;
        $repair_submit_num = $info['repair_submit_num'] ?? 0;
        if( ($join_retry_no == 0 && $first_submit_num > 0) ||  ($join_retry_no == 1 && $repair_submit_num > 0)) throws('已传数据，不可进行取样操作！');
        // 获得报名项
        $join_items = $info['join_items_sample_save'] ?? [];
        if(empty($join_items)) throws('没有报名的项目！');

        $table_join_item_ids = Tool::getArrFields($join_items, 'id');

        // 获得取样的项
        $join_item_ids = CommonRequest::get($request, 'join_item_ids');
        // 如果是字符，则转为数组
        if(is_string($join_item_ids) || is_numeric($join_item_ids)){
            if(strlen(trim($join_item_ids)) > 0){
                $join_item_ids = explode(',' ,$join_item_ids);
            }
        }
        if(!is_array($join_item_ids)) $join_item_ids = [];
        if(!Tool::isEqualArr($table_join_item_ids, $join_item_ids, 1) ){
            throws('操作的项目与报名项目不匹配！');
        }

        $currentNow = Carbon::now()->toDateTimeString();

        $sample_num_data = [];
        foreach($join_items as $item_info){
            $join_id = $item_info['id'];
            $ability_name = $item_info['ability_name'];// 项目名称
            //  1已报名  2已取样  4已上传数据
            //   8已判定【如果有有问题、不满意 --还可以再取样--进入已取样状态】
            //   16已完成--不可再修改【打印证书后或大后台点《公布结果》】)
            $status = $item_info['status'];
            $retry_no = $item_info['retry_no'];// 是否补测 0正常测 1补测1 2 补测2 .....
            $result_status = $item_info['result_status'];// 验证结果1待判定 2满意、4有问题、8不满意   16满意【补测满意】
            $is_sample = $item_info['is_sample'];// 是否取样1待取样--未取 2已取样--已取

            // 完成状态不可取样
            // 验证结果 2满意  16满意【补测满意】 不可取样
            if(in_array($status, [16, 32, 64]) || in_array($result_status, [2, 16]) ) continue;
            $item_sample_nums = CommonRequest::get($request, 'items_samples_' . $join_id . '_' . ($retry_no + 1));
            // 如果是字符，则转为数组
            if(is_string($item_sample_nums) || is_numeric($item_sample_nums)){
                if(strlen(trim($item_sample_nums)) > 0){
                    $item_sample_nums = explode(',' ,$item_sample_nums);
                }
            }
            if(!is_array($item_sample_nums)) $item_sample_nums = [];
            if(empty($item_sample_nums)) throws('请输入【' . $ability_name . '】取样编号');
             $i = 1;
            foreach($item_sample_nums as $sample_number){
                if(empty($sample_number)) continue;
                if(trim($sample_number) == '') continue;

                if(!isset($sample_num_data[$join_id])) $sample_num_data[$join_id] = [];
                array_push($sample_num_data[$join_id], [
                    'ability_join_item_id' => $join_id,
                    'retry_no' => $retry_no,
                    'serial_number' => $i,
                    'sample_one' => $sample_number,
                    // 'result_id' => 0, // 保存时再去查询
                    'sample_time' => $currentNow
                ]);
                 $i++;
            }

        }
        if(empty($sample_num_data)) throws('没有领样信息');
        // throws(json_encode($sample_num_data));

        $saveData = [];
        if($join_retry_no == 0){
            $saveData = array_merge($saveData, [
                'status' => 4,
                'is_sample' => 2,
                'sample_time' => $currentNow,
            ]);
        }else{
            $saveData = array_merge($saveData, [
                'is_sample' => 8,
                'sample_time_repair' => $currentNow,
            ]);
        }
        $saveData['sample_num_data'] = $sample_num_data;

//        if($id <= 0) {// 新加;要加入的特别字段
//            $addNewData = [
//                // 'account_password' => $account_password,
//            ];
//            $saveData = array_merge($saveData, $addNewData);
//        }
        $extParams = [
            'judgeDataKey' => 'replace',// 数据验证的下标
            'methodName' => 'sample_save'
        ];
        $resultDatas = CTAPIAbilityJoinBusiness::replaceById($request, $this, $saveData, $id, $extParams, true);
        return ajaxDataArr(1, $resultDatas, '');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/ability_join/ajax_alist",
     *     tags={"大后台-能力验证-能力验证报名"},
     *     summary="能力验证报名--列表",
     *     description="能力验证报名--列表......",
     *     operationId="adminQualityControlAbilityJoinAjax_alist",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_id_optional"),
     *     @OA\Response(response=200,ref="#/components/responses/Response_QualityControl_list_ability_join"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join"}
     */
    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_alist(Request $request){
//        $this->InitParams($request);
//        $relations = [];//  ['siteResources']
//        // $handleKeyArr = ['company'];
//
//        $extParams = [
//           // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
//            'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['company_info'], []),
//        ];
//        return  CTAPIAbilityJoinBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
        return $this->exeDoPublicFun($request, 4, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $relations = [];//  ['siteResources']
            // $handleKeyArr = ['company'];

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['company_info'], []),
            ];
            return  CTAPIAbilityJoinBusiness::getList($request, $this, 2 + 4, [], $relations, $extParams);
        });
    }

    /**
     * 选择短信模板页面
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function sms_send(Request $request)
    {
        return $this->exeDoPublicFun($request, 34359738368, 8, 'admin.QualityControl.SmsTemplate.sms_send', true
            , '', [], function (&$reDataArr) use ($request){
                $sms_operate_type = 1;// 操作类型 1 发送短信  ; 2测试发送短信
                $reDataArr['sms_operate_type'] = $sms_operate_type;
                // 设置参数
                $mergeParams = [// template_id 与 module_id 二选一
                    // 'sms_template_id' => 1,// 短信模板id;--可为0 ；
                    'sms_module_id' => 7,// 短信模块id
                ];
                CTAPISmsTemplateBusiness::mergeRequest($request, $this, $mergeParams);

                $smsMobileFieldKV = ['mobile' => '联系人手机'];// 可以发送短信的手机号字段
                $smsMobileField = 'mobile';// 默认的发送短信的手机号字段
                $reDataArr['smsMobileFieldKV'] = $smsMobileFieldKV;
                $reDataArr['defaultSmsMobileField'] = $smsMobileField;
                CTAPISmsTemplateBusiness::smsSend($request,  $this, $reDataArr);
            });
    }

    /**
     * ajax发送手机短信
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_sms_send(Request $request){
        return $this->exeDoPublicFun($request, 68719476736, 4,'', true, '', [], function (&$reDataArr) use ($request){

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['company_info'], []),
            ];
            return CTAPIAbilityJoinBusiness::getList($request, $this, 1 + 0, [], [], $extParams);
        });
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_search_print(Request $request){
        return $this->exeDoPublicFun($request, 0, 4,'', true, '', [], function (&$reDataArr) use ($request){
            $mergeParams = [
                'status' => '16,64',// 类型1平台2企业4个人
            ];
            CTAPIAbilityJoinBusiness::mergeRequest($request, $this, $mergeParams);

            $relations = [];//  ['siteResources']
            // $handleKeyArr = ['company'];

            $extParams = [
                // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
                // 'relationFormatConfigs'=> CTAPIAbilityJoinBusiness::getRelationConfigs($request, $this, ['company_info'], []),
            ];
            $result = CTAPIAbilityJoinBusiness::getList($request, $this, 1, [], $relations, $extParams);
            $data_list = $result['result']['data_list'] ?? [];
            $need_operate_ids = [];// 需要进行打印操作的id数组
            foreach($data_list as $info){
                $tem_id = $info['id'];
                $tem_is_print = $info['is_print'];
                if($tem_is_print == 1) array_push($need_operate_ids, $tem_id);
            }
            if(!empty($need_operate_ids)) CTAPIAbilityJoinBusiness::printAjax($request, $this, implode(',', $need_operate_ids));

            $re_ids = array_column($data_list, 'id');
            return ajaxDataArr(1, ['ids' => $re_ids], '');
        });
    }

    /**
     * ajax获得列表数据
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_ids(Request $request){
//        $this->InitParams($request);
//        $result = CTAPIAbilityJoinBusiness::getList($request, $this, 1 + 0);
//        $data_list = $result['result']['data_list'] ?? [];
//        $ids = implode(',', array_column($data_list, 'id'));
//        return ajaxDataArr(1, $ids, '');
//        return $this->exeDoPublicFun($request, 4294967296, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $result = CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0);
//            $data_list = $result['result']['data_list'] ?? [];
//            $ids = implode(',', array_column($data_list, 'id'));
//            return ajaxDataArr(1, $ids, '');
//        });
//    }


    /**
     * 导出
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function export(Request $request){
//        $this->InitParams($request);
//        CTAPIAbilityJoinBusiness::getList($request, $this, 1 + 0);
//        return $this->exeDoPublicFun($request, 4096, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::getList($request, $this, 1 + 0);
//        });
//    }


    /**
     * 导入模版
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import_template(Request $request){
//        $this->InitParams($request);
//        CTAPIAbilityJoinBusiness::importTemplate($request, $this);
//        return $this->exeDoPublicFun($request, 16384, 8,'', true, '', [], function (&$reDataArr) use ($request){
//            CTAPIRrrDdddBusiness::importTemplate($request, $this);
//        });
//    }


    /**
     * @OA\Post(
     *     path="/api/admin/ability_join/ajax_del",
     *     tags={"大后台-能力验证-能力验证报名"},
     *     summary="能力验证报名--删除",
     *     description="根据单个id,删除记录......",
     *     operationId="adminQualityControlAbilityJoinAjax_del",
     *     deprecated=false,
     *     @OA\Parameter(ref="#/components/parameters/Accept"),
     *     @OA\Parameter(ref="#/components/parameters/Schema_QualityControl_ability_join_id_required"),
     *     @OA\Response(response=200,ref="#/components/responses/common_Response_del"),
     *     @OA\Response(response=400,ref="#/components/responses/common_Response_err_400"),
     *     @OA\Response(response=404,ref="#/components/responses/common_Response_err_404"),
     * )
     *     请求主体对象
     *     requestBody={"$ref": "#/components/requestBodies/RequestBody_QualityControl_info_ability_join"}
     */
    /**
     * 子帐号管理-删除
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_del(Request $request)
//    {
//        $this->InitParams($request);
//        return CTAPIAbilityJoinBusiness::delAjax($request, $this);
//
//        $tem_id = CommonRequest::get($request, 'id');
//        Tool::formatOneArrVals($tem_id, [null, ''], ',', 1 | 2 | 4 | 8);
//        $pageNum = (is_array($tem_id) && count($tem_id) > 1 ) ? 1024 : 512;
//        return $this->exeDoPublicFun($request, $pageNum, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            return CTAPIRrrDdddBusiness::delAjax($request, $this);
//        });
//    }

    /**
     * ajax操作(标记打印操作)
     *
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_print(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::get($request, 'id');// 报名id
        $modifyNum = CTAPIAbilityJoinBusiness::printAjax($request, $this, $id);
        return ajaxDataArr(1, ['modify_num' => $modifyNum], '');
    }

    /**
     * ajax操作(标记证书领取操作)
     *
     * @param int $id
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function ajax_grant(Request $request)
    {
        $this->InitParams($request);
        $id = CommonRequest::get($request, 'id');// 报名id
        $modifyNum = CTAPIAbilityJoinBusiness::grantAjax($request, $this, $id);
        return ajaxDataArr(1, ['modify_num' => $modifyNum], '');
    }

    /**
     * ajax根据部门id,小组id获得所属部门小组下的员工数组[kv一维数组]
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function ajax_get_child(Request $request){
//        $this->InitParams($request);
//        $parent_id = CommonRequest::getInt($request, 'parent_id');
//        // 获得一级城市信息一维数组[$k=>$v]
//        $childKV = CTAPIAbilityJoinBusiness::getCityByPid($request, $this, $parent_id);
//        // $childKV = CTAPIAbilityJoinBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//        return  ajaxDataArr(1, $childKV, '');;
//        return $this->exeDoPublicFun($request, 8589934592, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $parent_id = CommonRequest::getInt($request, 'parent_id');
//            // 获得一级城市信息一维数组[$k=>$v]
//            $childKV = CTAPIRrrDdddBusiness::getCityByPid($request, $this, $parent_id);
//            // $childKV = CTAPIRrrDdddBusiness::getChildListKeyVal($request, $this, $parent_id, 1 + 0);
//
//            return  ajaxDataArr(1, $childKV, '');
//        });
//    }


    // 导入员工信息
//    public function ajax_import(Request $request){
//        $this->InitParams($request);
//        $fileName = 'staffs.xlsx';
//        $resultDatas = CTAPIAbilityJoinBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
///
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            $fileName = 'staffs.xlsx';
//            $resultDatas = CTAPIRrrDdddBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    /**
     * 单文件上传-导入excel
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
//    public function import(Request $request)
//    {
//        $this->InitParams($request);
//        // 上传并保存文件
//        $result = Resource::fileSingleUpload($request, $this, 1);
//        if($result['apistatus'] == 0) return $result;
//        // 文件上传成功
//        $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//        $resultDatas = CTAPIAbilityJoinBusiness::importByFile($request, $this, $fileName);
//        return ajaxDataArr(1, $resultDatas, '');
//        return $this->exeDoPublicFun($request, 32768, 4,'', true, '', [], function (&$reDataArr) use ($request){
//            // 上传并保存文件
//            $result = Resource::fileSingleUpload($request, $this, 1);
//            if($result['apistatus'] == 0) return $result;
//            // 文件上传成功
//            $fileName = Tool::getPath('public') . '/' . $result['result']['filePath'];
//            $resultDatas = CTAPIAbilityJoinBusiness::importByFile($request, $this, $fileName);
//            return ajaxDataArr(1, $resultDatas, '');
//        });
//    }

    // **************公用方法**********************开始*******************************

    /**
     * 公用列表页 --- 可以重写此方法--需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doListPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 1->1 首页；2->2 列表页； 12->2048 弹窗选择页面；
        // $user_info = $this->user_info;
        // $id = $extendParams['params']['id'];



        // 拥有者类型1平台2企业4个人
        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态

        // 状态
        $reDataArr['status'] =  AbilityJoin::$statusArr;
        $reDataArr['defaultStatus'] = -1;// 列表页默认状态

        // 证书是否打印
        $reDataArr['isPrint'] =  AbilityJoin::$isPrintArr;
        $reDataArr['defaultIsPrint'] = -1;// 列表页默认状态

        // 证书是否领取
        $reDataArr['isGrant'] =  AbilityJoin::$isGrantArr;
        $reDataArr['defaultIsGrant'] = -1;// 列表页默认状态

        // 是否补测 0正常测 1补测1 2 补测2 .....
        $retry_no = CommonRequest::get($request, 'retry_no');
        $reDataArr['retryNo'] =  AbilityJoin::$retryNoArr;
        $reDataArr['defaultRetryNo'] = (strlen($retry_no) > 0) ? $retry_no : -1;// 列表页默认

        // 是否取样1待取样--未取 2已取样--已取
        $reDataArr['isSample'] =  AbilityJoin::$isSampleArr;
        $reDataArr['defaultIsSample'] = -1;// 列表页默认状态

        $reDataArr['hidden_option'] = $hiddenOption;
    }

    /**
     * 公用详情页 --- 可以重写此方法-需要时重写
     *  主要把要传递到视图或接口的数据 ---放到 $reDataArr 数组中
     * @param Request $request
     * @param array $reDataArr // 需要返回的参数
     * @param array $extendParams // 扩展参数
     *   $extendParams = [
     *      'pageNum' => 1,// 页面序号  同 属性 $fun_id【查看它指定的】 (其它根据具体的业务单独指定)
     *      'returnType' => 1,// 返回类型 1 视图[默认] 2 ajax请求的json数据[同视图数据，只是不显示在视图，是ajax返回]
     *                          4 ajax 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果 8 视图 直接返回 $exeFun 或 $extendParams['doFun'] 方法的执行结果
     *      'view' => 'index', // 显示的视图名 默认index
     *      'hasJudgePower' => true,// 是否需要判断登录权限 true:判断[默认]  false:不判断
     *      'doFun' => 'doListPage',// 具体的业务方法，动态或 静态方法 默认'' 可有返回值 参数  $request,  &$reDataArr, $extendParams ；
     *                               doListPage： 列表页； doInfoPage：详情页
     *      'params' => [],// 需要传入 doFun 的数据 数组[一维或多维]
     *  ];
     * @return mixed 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public function doInfoPage(Request $request, &$reDataArr, $extendParams = []){
        // 需要隐藏的选项 1、2、4、8....[自己给查询的或添加页的下拉或其它输入框等编号]；靠前面的链接传过来 &hidden_option=0;
        $hiddenOption = CommonRequest::getInt($request, 'hidden_option');
        // $pageNum = $extendParams['pageNum'] ?? 1;// 5->16 添加页； 7->64 编辑页；8->128 ajax详情； 35-> 17179869184 详情页
//        // $user_info = $this->user_info;
//        $id = $extendParams['params']['id'] ?? 0;
//
////        // 拥有者类型1平台2企业4个人
////        $reDataArr['adminType'] =  AbilityJoin::$adminTypeArr;
////        $reDataArr['defaultAdminType'] = -1;// 列表页默认状态
//        $info = [
//            'id'=>$id,
//            //   'department_id' => 0,
//        ];
//        $operate = "添加";
//
//        if ($id > 0) { // 获得详情数据
//            $operate = "修改";
//            $info = CTAPIRrrDdddBusiness::getInfoData($request, $this, $id, [], '', []);
//        }
//        // $reDataArr = array_merge($reDataArr, $resultDatas);
//        $reDataArr['info'] = $info;
//        $reDataArr['operate'] = $operate;

        $reDataArr['hidden_option'] = $hiddenOption;
    }
    // **************公用方法********************结束*********************************

}
