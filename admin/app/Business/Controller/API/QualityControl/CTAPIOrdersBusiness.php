<?php
// 收款订单
namespace App\Business\Controller\API\QualityControl;

use App\Models\QualityControl\Orders;
use App\Services\DBRelation\RelationDB;
use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\SMS\LimitSMS;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CTAPIOrdersBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\QualityControl\OrdersAPI';
    public static $table_name = 'orders';// 表名称
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
            // 获得发票开票模板名称
            'invoice_template_name' => CTAPIInvoiceTemplateBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_template_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIInvoiceTemplateBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_template_name'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_template_name')),
                static::getRelationSqlParams([], $extendParams, 'invoice_template_name'), '', []),// 'where' => [['admin_type', 2]]
            // 获得发票企业抬头名称
            'invoice_buyer_name' => CTAPIInvoiceBuyerBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoice_buyer_id' => 'id']
                , 1, 2
                ,'','',
                CTAPIInvoiceTemplateBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_buyer_name'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_buyer_name')),
                static::getRelationSqlParams([], $extendParams, 'invoice_buyer_name'), '', []),// 'where' => [['admin_type', 2]]
            // 蓝票信息--当前
            'invoice_blue' => CTAPIInvoicesBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoic_blue_num' => 'order_num']
                , 1, 1
                ,'','',
                CTAPIInvoicesBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_blue'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_blue')),
                static::getRelationSqlParams([], $extendParams, 'invoice_blue'), '', ['extendConfig' => ['listHandleKeyArr' => ['priceIntToFloat']]]),
            // 红票信息--当前
            'invoice_red' => CTAPIInvoicesBusiness::getTableRelationConfigInfo($request, $controller
                , ['invoic_red_num' => 'order_num']
                , 1, 1
                ,'','',
                CTAPIInvoicesBusiness::getRelationConfigs($request, $controller,
                    static::getUboundRelation($relationArr, 'invoice_red'),
                    static::getUboundRelationExtendParams($extendParams, 'invoice_red')),
                static::getRelationSqlParams([], $extendParams, 'invoice_red'), '', ['extendConfig' => ['listHandleKeyArr' => ['priceIntToFloat']]]),
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

        $has_refund = CommonRequest::get($request, 'has_refund');
        if(strlen($has_refund) > 0 && $has_refund != -1)  Tool::appendParamQuery($queryParams, $has_refund, 'has_refund', [ ''], ',', false);

        $invoice_buyer_id = CommonRequest::get($request, 'invoice_buyer_id');
        if(strlen($invoice_buyer_id) > 0 && !in_array($invoice_buyer_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_buyer_id, 'invoice_buyer_id', [0, '0', ''], ',', false);

        $invoice_buyer_id_history = CommonRequest::get($request, 'invoice_buyer_id_history');
        if(strlen($invoice_buyer_id_history) > 0 && !in_array($invoice_buyer_id_history, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_buyer_id_history, 'invoice_buyer_id_history', [0, '0', ''], ',', false);

        $invoice_template_id = CommonRequest::get($request, 'invoice_template_id');
        if(strlen($invoice_template_id) > 0 && !in_array($invoice_template_id, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_template_id, 'invoice_template_id', [0, '0', ''], ',', false);

        $invoice_template_id_history = CommonRequest::get($request, 'invoice_template_id_history');
        if(strlen($invoice_template_id_history) > 0 && !in_array($invoice_template_id_history, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_template_id_history, 'invoice_template_id_history', [0, '0', ''], ',', false);

        $invoice_result = CommonRequest::get($request, 'invoice_result');
        if(strlen($invoice_result) > 0 && $invoice_result != 0)  Tool::appendCondition($queryParams, 'invoice_result',  $invoice_result . '=' . $invoice_result, '&');

        $invoice_status = CommonRequest::get($request, 'invoice_status');
        if(strlen($invoice_status) > 0 && !in_array($invoice_status, [0, '-1']))  Tool::appendParamQuery($queryParams, $invoice_status, 'invoice_status', [0, '0', ''], ',', false);

        $upload_status = CommonRequest::get($request, 'upload_status');
        if(strlen($upload_status) > 0 && !in_array($upload_status, [0, '-1']))  Tool::appendParamQuery($queryParams, $upload_status, 'upload_status', [0, '0', ''], ',', false);

//        $status_online = CommonRequest::get($request, 'status_online');
//        if(strlen($status_online) > 0 && $status_online != 0)  Tool::appendParamQuery($queryParams, $status_online, 'status_online', [0, '0', ''], ',', false);

//        $ids = CommonRequest::get($request, 'ids');
//        if(strlen($ids) > 0 && $ids != 0)  Tool::appendParamQuery($queryParams, $ids, 'id', [0, '0', ''], ',', false);

        // 方法最下面
        // 注意重写方法中，如果不是特殊的like，同样需要调起此默认like方法--特殊的写自己特殊的方法
        static::joinListParamsLike($request, $controller, $queryParams, $notLog);
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
            Tool::bathPriceCutFloatInt($data_list, Orders::$IntPriceFields, Orders::$IntPriceIndex, 2, 2);
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

        $headArr = ['order_no'=>'订单号', 'company_name'=>'所属企业', 'order_type_text'=>'订单类型', 'remarks'=>'订单备注'
            , 'pay_company_name'=>'收款帐号', 'pay_name'=>'支付方式' , 'total_amount'=>'商品数量', 'total_price'=>'商品总价'
            , 'total_price_goods'=>'应付金额', 'total_price_discount'=>'优惠金额', 'payment_amount'=>'支付金额', 'change_amount'=>'找零金额'
            ,  'order_time'=>'下单时间', 'pay_time'=>'付款时间', 'has_refund_text'=>'退费状态', 'refund_time'=>'退费时间'
            , 'refund_price'=>'已退金额',  'refund_price_frozen'=>'退费冻结',  'check_price'=>'实收金额',  'order_status_text'=>'订单状态'
            ,  'sure_time'=>'确认时间',  'check_time'=>'完成时间',  'cancel_time'=>'作废时间'];
//        foreach($data_list as $k => $v){
//            if(isset($v['method_name'])) $data_list[$k]['method_name'] =replace_enter_char($v['method_name'],2);
//            if(isset($v['limit_range'])) $data_list[$k]['limit_range'] =replace_enter_char($v['limit_range'],2);
//            if(isset($v['explain_text'])) $data_list[$k]['explain_text'] =replace_enter_char($v['explain_text'],2);
//
//        }
        ImportExport::export('','订单' . date('YmdHis'),$data_list,1, $headArr, 0, ['sheet_title' => '订单']);
    }



    /**
     * 确认 批量 或 单条数据
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $organize_id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param string $id 记录id，多个用逗号分隔 或是 一维数组
     * @param int $operate_type 操作类型 1确认，、2手动订单完成【对业务完成后才确认缴费的订单，进行手动完成】
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 修改的数量   //   array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function operateStatusAjax(Request $request, Controller $controller, $organize_id = 0, $id = 0, $operate_type = 1, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;
        // 调用新加或修改接口
        $apiParams = [
            'company_id' => $company_id,
            'organize_id' => $organize_id,
            'id' => $id,
            'operate_type' => $operate_type,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        $modifyNum = static::exeDBBusinessMethodCT($request, $controller, '',  'operateStatusById', $apiParams, $company_id, $notLog);

        return $modifyNum;
        // return static::delAjaxBase($request, $controller, '', $notLog);

    }

    /**
     * 开电子发票--蓝票
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $organize_id 操作的所属企业id
     * @param string $id 记录id，多个用逗号分隔
     * @param int $operate_staff_id 操作人id
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 修改的数量   //   array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function operateInvoiceBlueAjax(Request $request, Controller $controller, $organize_id = 0, $id = 0, $invoice_buyer_id = 0, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;

        $buyerInfo = CTAPIInvoiceBuyerBusiness::getFVFormatList( $request,  $controller, 4, 1
            , ['id' => $invoice_buyer_id], false, [], []);
        if(empty($buyerInfo)) throws('请选择发票抬头');
        if($organize_id != $buyerInfo['company_id'])  throws('您没有操作此记录的权限！');
        if(!in_array($buyerInfo['open_status'], [1]))  throws('发票抬头未启用，请开启或选择其它抬头！');

        // 调用新加或修改接口
        $apiParams = [
            'company_id' => $company_id,
            'organize_id' => $organize_id,
            'id' => $id,
            'invoice_buyer_id' => $invoice_buyer_id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        $result = static::exeDBBusinessMethodCT($request, $controller, '',  'operateInvoiceBlueById', $apiParams, $company_id, $notLog);

        return $result;

    }

    /**
     * 开电子发票--红票
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $organize_id 操作的所属企业id
     * @param string $id 记录id，多个用逗号分隔
     * @param int $invoice_template_id 开票模版
     * @param int $notLog 是否需要登陆 0需要1不需要
     * @return  int 修改的数量   //   array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function operateInvoiceRedAjax(Request $request, Controller $controller, $organize_id = 0, $id = 0, $invoice_template_id = 0, $notLog = 0)
    {
        $company_id = $controller->company_id;
        $user_id = $controller->user_id;

        $invoiceTemplateInfo = CTAPIInvoiceTemplateBusiness::getFVFormatList( $request,  $controller, 4, 1
            , ['id' => $invoice_template_id], false, [], []);
        if(empty($invoiceTemplateInfo)) throws('请选择开票模版');
        // if($organize_id != $invoiceTemplateInfo['company_id'])  throws('您没有操作此记录的权限！');
        if(!in_array($invoiceTemplateInfo['open_status'], [1]))  throws('开票模版未启用，请开启或选择其它开票模版！');

        // 调用新加或修改接口
        $apiParams = [
            'company_id' => $company_id,
            'organize_id' => $organize_id,
            'id' => $id,
            'invoice_template_id' => $invoice_template_id,
            'operate_staff_id' => $user_id,
            'modifAddOprate' => 0,
        ];
        $result = static::exeDBBusinessMethodCT($request, $controller, '',  'operateInvoiceRedById', $apiParams, $company_id, $notLog);

        return $result;

    }

    /**
     * 根据订单id，获得订单信息并判断是否可以进行分班操作
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param string $id 记录id，多个用逗号分隔
     * @param int $operateType 操作类型 1：电子发票[蓝票] 【默认】；2：电子发票[红票]
     * @return array 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function getOrdersAndJudge(Request $request, Controller $controller, &$id, $operateType = 1){
        // 获得订单列表信息
        $relateionKey = [
            'company_name' => '',
            'pay_company_name' => '',
            'pay_name' => '',
            'invoice_template_name' => '',
            'invoice_buyer_name' => '',
        ];
        if($operateType == 2) $relateionKey = array_merge($relateionKey, ['invoice_blue' => '']);
        $extParams = [
            // 'handleKeyArr' => $handleKeyArr,//一维数组，数数据需要处理的标记，每一个或类处理，根据情况 自定义标记，然后再处理函数中处理数据。
            'relationFormatConfigs'=> static::getRelationConfigs($request, $controller,$relateionKey , []),
            'listHandleKeyArr' => ['priceIntToFloat'],
        ];
        $dataList = static::getFVFormatList( $request,  $controller, 1, 1
            , ['id' => $id], false, [], $extParams);

        foreach($dataList as $info){

            $tem_order_status = $info['order_status'];// 状态1待支付2待确认4已确认8订单完成【服务完成】16取消[系统取消]32取消[用户取消]
            $tem_invoice_result = $info['invoice_result'];// 开票结果1待开票1已开蓝票2已冲红
            $tem_invoice_status = $info['invoice_status'];// 开票状态1待开票2开票中4已开票【冲红后重新走流程】
            $tem_upload_status = $info['upload_status'];// 开票数据状态1待上传2已上传4已开票8已作废[不用]16已冲红[不用]
            $tem_order_no = $info['order_no'];

            switch($operateType) {
                case 1:// 电子发票[蓝票]判断 【默认】
                    if(! (($tem_order_status & (2 | 4 | 8)) > 0 && $tem_invoice_status == 1 )){
                        throws('订单【' . $tem_order_no . '】非可开电子发票状态，不可以进行此操作');
                    }
                    break;
                case 2:// 电子发票[红票]
                    if(! $tem_invoice_status == 4 ){
                        throws('订单【' . $tem_order_no . '】非已开发票状态，不可以进行此操作');
                    }
                    break;
                default:
                    break;
            }
        }
        if($operateType == 2){// 电子发票[红票]
            $invoicBlueNums = Tool::getArrFields($dataList, 'invoic_blue_num');
            if(count($invoicBlueNums) > 1){
                throws('不同的电子发票【蓝票】，不可以一起进行全额冲红！请分别全额冲红！');
            }
        }
        return $dataList;
    }

    /**
     * 根据订单id,获得电子发票信息
     *
     * @param Request $request 请求信息
     * @param Controller $controller 控制对象
     * @param int $id 操作的所属企业id 可以为0：没有所属企业--企业后台，操作用户时用来限制，只能操作自己企业的用户
     * @param int $company_id 所属企业id , 没有
     * @param int $operateType 操作类型 1：电子发票[蓝票] 【默认】；2：电子发票[红票]
     * @return  array 数组 [ '订单数据列表'， '企业id', '企业名称']
     * @author zouyan(305463219@qq.com)
     */
    public static function getInvoiceByIds(Request $request, Controller $controller, $id, $company_id = 0, $operateType = 1){
        $dataList = static::getOrdersAndJudge($request, $controller, $id, $operateType);
        $operateText = "开票";
        if($operateType == 2){
            $operateText = "全额冲红";
        }
        // 判断订单所属企业【同一企业，就可以开电子发票】
        $companyIds = Tool::getArrFields($dataList, 'company_id');
        if(count($companyIds) > 1){
            throws('不同的企业，不可以一起进行' . $operateText . '！请分别' . $operateText . '！');
        }
        $companyNames = Tool::getArrFields($dataList, 'company_name');
        if(is_numeric($company_id) && $company_id > 0 && !in_array($company_id, $companyIds)){
            throws('您没有操作此记录的权限！');
        }
        $payConfigIds = Tool::getArrFields($dataList, 'pay_config_id');
        if(count($payConfigIds) > 1){
            throws('不同的收款帐号，不可以一起进行' . $operateText . '！请分别' . $operateText . '！');
        }
        $invoiceTemplateIds = Tool::getArrFields($dataList, 'invoice_template_id');
        if(count($invoiceTemplateIds) > 1){
            throws('不同的【发票开票模板】，不可以一起进行' . $operateText . '！请分别' . $operateText . '！');
        }
        return [$dataList, $companyIds[0] ?? 0, $companyNames[0] ?? ''];
    }
}
