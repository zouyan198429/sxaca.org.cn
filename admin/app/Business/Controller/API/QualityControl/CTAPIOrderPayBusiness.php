<?php
// 支付订单
namespace App\Business\Controller\API\QualityControl;

use App\Models\QualityControl\OrderPay;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\pay\weixin\easyWechatPay;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CTAPIOrderPayBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\OrderPayAPI';
    public static $table_name = 'order_pay';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***

    // 是否激活(0:未激活；1：已激活)
//    public static $isActiveArr = [
//        '0' => '未激活',
//        '1' => '已激活',
//    ];

    /**
     * 特殊的验证 关键字 -单个 的具体验证----具体的子类----重写此方法来实现具体的验证
     *
     * @param array $mustFields 表对象字段验证时，要必填的字段，指定必填字须，为后面的表字须验证做准备---一维数组
     * @param array $judgeData 需要验证的数据---数组-- 根据实际情况的维数不同。
     * @param string $key 验证规则的关键字 -单个
     * @param array $tableLangConfig 多语言单个数据表配置数组--也就是表多语言的那个配置数组
     * @param array $extParams 其它扩展参数，
     * @return  array 错误：非空数组；正确：空数组
     * @author zouyan(305463219@qq.com)
     */
    public static function singleJudgeDataByKey(&$mustFields = [], &$judgeData = [], $key = '', $tableLangConfig = [], $extParams = []){
        if(!is_array($mustFields)) $mustFields = [];
        $errMsgs = [];// 错误信息的数组--一维数组，可以指定下标
        // if( (is_string($key) && strlen($key) <= 0 ) || (is_array($key))) return $errMsgs;
        switch($key){
            case 'add':// 添加；

                break;
            case 'modify':// 修改
                break;
            case 'replace':// 新加或修改

                $id = $extParams['id'] ?? 0;
                if($id > 0){

                }

                break;
            default:
                break;
        }
        return $errMsgs;
    }

    // ****表关系***需要重写的方法**********开始***********************************
    /**
     * 获得处理关系表数据的配置信息--重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $relationKeys
     * @param array $extendParams  扩展参数---可能会用；需要指定的实时特别的 条件配置
     *          格式： [
     *                    '关系下标' => [
     *                          'fieldValParams' => [ '字段名1' => '字段值--多个时，可以是一维数组或逗号分隔字符', ...],// 也可以时 Tool getParamQuery 方法的参数$fieldValParams的格式
     *                          'sqlParams' => []// 与参数 $sqlDefaultParams 相同格式的条件
     *                          '关系下标' => ... 下下级的
     *                       ]
     *                ]
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getRelationConfigs(Request $request, Controller $controller, $relationKeys = [], $extendParams = []){
        if(empty($relationKeys)) return [];
        list($relationKeys, $relationArr) = static::getRelationParams($relationKeys);// 重新修正关系参数
        $user_info = $controller->user_info;
        $user_id = $controller->user_id;
        $user_type = $controller->user_type;
        // 关系配置
        $relationFormatConfigs = [
            // 下标 'relationConfig' => []// 下一个关系
            // 获得企业名称
            'company_name' => CTAPIStaffBusiness::getTableRelationConfigInfo($request, $controller
                , ['company_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIStaffBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'company_name'),
                    static::getUboundRelationExtendParams($extendParams, 'company_name')),
                static::getRelationSqlParams([], $extendParams, 'company_name'), '', []),
            // 获得收款帐号名称
            'pay_company_name' => CTAPIOrderPayConfigBusiness::getTableRelationConfigInfo($request, $controller
                , ['pay_config_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIOrderPayConfigBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'pay_company_name'),
                    static::getUboundRelationExtendParams($extendParams, 'pay_company_name')),
                static::getRelationSqlParams([], $extendParams, 'pay_company_name'), '', []),
            // 获得支付方式名称
            'pay_name' => CTAPIOrderPayMethodBusiness::getTableRelationConfigInfo($request, $controller
                , ['pay_method' => 'pay_method']
                , 1, 2
                ,'','',
                CTAPIOrderPayMethodBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'pay_name'),
                    static::getUboundRelationExtendParams($extendParams, 'pay_name')),
                static::getRelationSqlParams([], $extendParams, 'pay_name'), '', []),
        ];
        return Tool::formatArrByKeys($relationFormatConfigs, $relationKeys, false);
    }

    /**
     * 获得要返回数据的return_data数据---每个对象，重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $return_num 指定要获得的关系数据类型格式化后的数据 编号 1[占用：原数据] 2 4 8..
     * @return  array 表关系配置信息
     * @author zouyan(305463219@qq.com)
     */
    public static function getRelationConfigReturnData(Request $request, Controller $controller, $return_num = 0){
        $return_data = [];// 为空，则会返回对应键=> 对应的数据， 具体的 结构可以参考 Tool::formatConfigRelationInfo  $return_data参数格式

        if(($return_num & 1) == 1) {// 返回源数据--特别的可以参考这个配置
            $return_data['old_data'] = ['ubound_operate' => 1, 'ubound_name' => '', 'fields_arr' => [], 'ubound_keys' => [], 'ubound_type' =>1];
        }

//        if(($return_num & 2) == 2){// 给上一级返回名称 company_name 下标
//            $one_field = ['key' => 'company_name', 'return_type' => 2, 'ubound_name' => 'company_name', 'split' => '、'];// 获得名称
//            if(!isset($return_data['one_field'])) $return_data['one_field'] = [];
//            array_push($return_data['one_field'], $one_field);
//        }

        return $return_data;
    }
    // ****表关系***需要重写的方法**********结束***********************************

    /**
     * 获得列表数据时，查询条件的参数拼接--有特殊的需要自己重写此方法--每个字类都有此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function joinListParams(Request $request, Controller $controller, &$queryParams, $notLog = 0){
        // 自己的参数查询拼接在这里-- 注意：多个id 的查询默认就已经有了，参数是 ids  多个用逗号分隔

        $company_id = CommonRequest::getInt($request, 'company_id');
        if($company_id > 0 )  array_push($queryParams['where'], ['company_id', '=', $company_id]);

        $order_type = CommonRequest::get($request, 'order_type');
        if(strlen($order_type) > 0 && $order_type != 0)  Tool::appendParamQuery($queryParams, $order_type, 'order_type', [0, '0', ''], ',', false);

        $pay_config_id = CommonRequest::get($request, 'pay_config_id');
        if(strlen($pay_config_id) > 0 && $pay_config_id != 0)  Tool::appendParamQuery($queryParams, $pay_config_id, 'pay_config_id', [0, '0', ''], ',', false);

        $pay_method = CommonRequest::get($request, 'pay_method');
        if(strlen($pay_method) > 0 && $pay_method != 0)  Tool::appendParamQuery($queryParams, $pay_method, 'pay_method', [0, '0', ''], ',', false);

        $order_no = CommonRequest::get($request, 'order_no');
        if(strlen($order_no) > 0 && $order_no != 0)  Tool::appendParamQuery($queryParams, $order_no, 'order_no', [0, '0', ''], ',', false);

        $pay_order_no = CommonRequest::get($request, 'pay_order_no');
        if(strlen($pay_order_no) > 0 && $pay_order_no != 0)  Tool::appendParamQuery($queryParams, $pay_order_no, 'pay_order_no', [0, '0', ''], ',', false);

        $pay_type = CommonRequest::get($request, 'pay_type');
        if(strlen($pay_type) > 0 && $pay_type != 0)  Tool::appendParamQuery($queryParams, $pay_type, 'pay_type', [0, '0', ''], ',', false);

        $operate_type = CommonRequest::get($request, 'operate_type');
        if(strlen($operate_type) > 0 && $operate_type != 0)  Tool::appendParamQuery($queryParams, $operate_type, 'operate_type', [0, '0', ''], ',', false);

        $frozen_status = CommonRequest::get($request, 'frozen_status');
        if(strlen($frozen_status) > 0 && $frozen_status != -1)  Tool::appendParamQuery($queryParams, $frozen_status, 'frozen_status', [ ''], ',', false);

        $pay_status = CommonRequest::get($request, 'pay_status');
        if(strlen($pay_status) > 0 && $pay_status != 0)  Tool::appendParamQuery($queryParams, $pay_status, 'pay_status', [0, '0', ''], ',', false);

        $pay_no = CommonRequest::get($request, 'pay_no');
        if(strlen($pay_no) > 0 && $pay_no != 0)  Tool::appendParamQuery($queryParams, $pay_no, 'pay_no', [0, '0', ''], ',', false);

        $parent_pay_no = CommonRequest::get($request, 'parent_pay_no');
        if(strlen($parent_pay_no) > 0 && $parent_pay_no != 0)  Tool::appendParamQuery($queryParams, $parent_pay_no, 'parent_pay_no', [0, '0', ''], ',', false);

//        $status_online = CommonRequest::get($request, 'status_online');
//        if(strlen($status_online) > 0 && $status_online != 0)  Tool::appendParamQuery($queryParams, $status_online, 'status_online', [0, '0', ''], ',', false);

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
    }

    /**
     * 支付回调--微信
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $message 参数
     * @param array $queryMessage
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  string 正常返回 已经处理好，不用再通知了 or  throws string :错误信息，还要再通知我
     * @author zouyan(305463219@qq.com)
     */
    public static function payWXNotify(Request $request, Controller $controller, $message = [], $queryMessage = [], $notLog = 0)
    {

        Log::info('微信支付日志 payWXNotify-->' . __FUNCTION__, [$message, $queryMessage, $notLog]);

        $company_id = 0;// $controller->company_id;
        // $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }

        // 调用新加或修改接口
        $apiParams = [
            'message' => $message,
            'queryMessage' => $queryMessage,
            // 'company_id' => $company_id,
        ];
        $result = static::exeDBBusinessMethodCT($request, $controller, '', 'payWXNotify', $apiParams, $company_id, $notLog);
        return $result;
    }

    /**
     * 生成付款码后，用户扫码支付循环获取是否支付成功--微信--还有支付宝扫码支付
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $order_no 订单号
     * @param string $pay_order_no 支付单号
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return   1:支付成功  2: 支付失败 3：其它状态 或 throws 有误或 暂时没有支付结果
     * @author zouyan(305463219@qq.com)
     */
    public static function payWXQuery(Request $request, Controller $controller, $order_no = '', $pay_order_no = '', $notLog = 0)
    {

        $company_id = 0;// $controller->company_id;
        // $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }
        // 调用新加或修改接口
        $apiParams = [
            'order_no' => $order_no,
            'pay_order_no' => $pay_order_no,
        ];
        $result = static::exeDBBusinessMethodCT($request, $controller, '', 'payWXJudge', $apiParams, $company_id, $notLog);
        return $result;
    }

    /**
     * 生成付款码后，用户扫码支付对回调内容进行验证
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $notifyParams 回调的参数数组
     *   //  必有 out_trade_no 、 total_amount、 trade_status、notify_id ；  可能有 seller_id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return   true 成功 或 throws 有误
     * @author zouyan(305463219@qq.com)
     */
    public static function payAlipayNotify(Request $request, Controller $controller, $notifyParams = [], $notLog = 0)
    {

        $company_id = 0;// $controller->company_id;
        // $user_id = $controller->user_id;
//        if(isset($saveData['real_name']) && empty($saveData['real_name'])  ){
//            throws('联系人不能为空！');
//        }
        // 调用新加或修改接口
        $apiParams = [
            'notify_params' => $notifyParams,
        ];
        $result = static::exeDBBusinessMethodCT($request, $controller, '', 'alipayNotifyJudge', $apiParams, $company_id, $notLog);
        return $result;
    }

    /**
     * 格式化关系数据 --如果有格式化，肯定会重写---本地数据库主要用这个来格式化数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $main_list 关系主记录要格式化的数据
     * @param array $data_list 需要格式化的从记录数据---二维数组(如果是一维数组，是转成二维数组后的数据)
     * @param array $handleKeyArr 其它扩展参数，// 一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。--名称关键字，尽可能与关系名一样
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     * @return array  新增的字段 一维数组
     * @author zouyan(305463219@qq.com)
     */
    public static function handleRelationDataFormat(Request $request, Controller $controller, &$main_list, &$data_list, $handleKeyArr, &$returnFields = []){
        // if(empty($data_list)) return $returnFields;
        // 重写开始

        // 对外显示时，批量价格字段【整数转为小数】
        if(in_array('priceIntToFloat', $handleKeyArr)){
            Tool::bathPriceCutFloatInt($data_list, OrderPay::$IntPriceFields, OrderPay::$IntPriceIndex, 2, 2);
        }

        // 重写结束
        return $returnFields;
    }


    /**
     * 获得列表数据时，对查询结果进行导出操作--有特殊的需要自己重写此方法
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param array $queryParams 已有的查询条件数组
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  null 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function exportListData(Request $request, Controller $controller, &$data_list, $notLog = 0){

        $headArr = ['pay_order_no'=>'支付单号', 'pay_no'=>'交易单号[第三方]', 'order_no'=>'订单号', 'company_name'=>'所属企业'
            , 'pay_company_name'=>'收款帐号', 'pay_name'=>'支付方式' , 'order_type_text'=>'订单类型', 'remarks'=>'订单备注'
            , 'pay_type_text'=>'支付类型', 'pay_status_text'=>'支付状态', 'pay_price'=>'金额', 'frozen_status_text'=>'退款冻结状态'
            ,  'parent_pay_no'=>'退款原支付单号[第三方]', 'order_time'=>'下单时间', 'sure_time'=>'第三方通知确认时间'];
//        foreach($data_list as $k => $v){
//            if(isset($v['method_name'])) $data_list[$k]['method_name'] =replace_enter_char($v['method_name'],2);
//            if(isset($v['limit_range'])) $data_list[$k]['limit_range'] =replace_enter_char($v['limit_range'],2);
//            if(isset($v['explain_text'])) $data_list[$k]['explain_text'] =replace_enter_char($v['explain_text'],2);
//
//        }
        ImportExport::export('','第三方支付订单' . date('YmdHis'),$data_list,1, $headArr, 0, ['sheet_title' => '第三方支付订单']);
    }

}
