<?php
// 收款帐号配置
namespace App\Business\DB\QualityControl;

use App\Services\Tool;

/**
 *
 */
class OrderPayConfigDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\OrderPayConfig';
    public static $table_name = 'order_pay_config';// 表名称
    public static $record_class = __CLASS__;// 当前的类名称 App\Business\***\***\**\***
    // 历史表对比时 忽略历史表中的字段，[一维数组] - 必须会有 [历史表中对应主表的id字段]  格式 ['字段1','字段2' ... ]；
    // 注：历史表不要重置此属性
    public static $ignoreFields = [];


    /**
     * 组织支付方式文字【去掉非上线的支付方式】--适合于收款帐号配置自己直接使用，或 基于收款配置的子配置使用【已封装下面的方法formatConfigPayMethodAppendMethodText中】
     * @param array $data_list 收款帐号数据--一维或二维数组
     * @param array $disablePayMethod 其它指定的禁用支付方式--一维数组
     * @param array $payKVList 支付方式的 KV数组 -- 一维数组, 可为空【默认--一般传空数组】--为空，则方法内会自动获取
     */
    public static function rmDisablePayMethodAppendMethodText(&$data_list, &$disablePayMethod = [], &$payKVList = []){
        // if(empty($data_list)) return ;--不能返回了， 因为 后面还要获得 支付方式的 KV数组 及 禁用支付方式

        // 如果是一维数组,则转为二维数组
        $isMulti = Tool::isMultiArr($data_list, true);
        // 收款开通类型(1现金、2微信支付、4支付宝)
        // $disablePayMethod = [];
        // $payKVList = [];
        list($payKVList, $disablePayMethod, $payMethodList, $formatPayMethodList) = OrderPayMethodDBBusiness::getPayMethodDisable($disablePayMethod, $payKVList);

        foreach($data_list as $k => &$v){
            $pay_method_text_arr = [];
            $pay_method = $v['pay_method'];
            if($pay_method <= 0) {
                $v['pay_method_text'] = implode('、', $pay_method_text_arr);
                continue;
            }

            // 去掉禁用的
            foreach($disablePayMethod as $disPayMethod){
                $pay_method = Tool::bitRemoveVal($pay_method, $disPayMethod);
            }
            $v['pay_method'] = $pay_method;

            // 遍历支付方式的kv值
            foreach($payKVList as $k_pay_method => $v_method_text){
                if( ($pay_method & $k_pay_method) == $k_pay_method) array_push($pay_method_text_arr, $v_method_text);
            }
            $v['pay_method_text'] = implode('、', $pay_method_text_arr);
        }

        if(!$isMulti) $data_list = $data_list[0] ?? [];
    }

    /**
     * 组织支付方式文字【去掉非上线的支付方式】--适用于对收款配置-》在具体使用时的再一次配置数据处理
     * @param array $data_list 课程数据--一维或二维数组; 需要有字段 pay_config_id , pay_method
     * @param array $disablePayMethod 其它指定的禁用支付方式--一维数组
     * @param array $payKVList 支付方式的 KV数组 -- 一维数组, 可为空【默认--一般传空数组】--为空，则方法内会自动获取
     * @param array $returnFields  新加入的字段['字段名1' => '字段名1' ]
     */
    public static function formatConfigPayMethodAppendMethodText(&$data_list, &$disablePayMethod = [], &$payKVList = [], &$returnFields = []){
        // if(empty($data_list)) return ;--不能返回了， 因为 后面还要获得 支付方式的 KV数组 及 禁用支付方式

        // 如果是一维数组,则转为二维数组
        $isMulti = Tool::isMultiArr($data_list, true);
        $pay_config_ids = Tool::getArrFields($data_list, 'pay_config_id');
        // 去掉空及0值
        // 获得收款配置数据
        $configList = OrderPayConfigDBBusiness::getDBFVFormatList(1, 1, ['id' => $pay_config_ids], false);
        OrderPayConfigDBBusiness::rmDisablePayMethodAppendMethodText($configList, $disablePayMethod, $payKVList);

        // 按id格式化数据
        $formatConfigList = Tool::arrUnderReset($configList, 'id', 1, '_');

        foreach($data_list as $k => &$v){
            $pay_method_text_arr = [];
            $pay_method = $v['pay_method'];
//            if($pay_method <= 0) {
//                $v['pay_method_text'] = implode('、', $pay_method_text_arr);
//                continue;
//            }
            $tem_pay_config_id = $v['pay_config_id'];
            $avalConfigInfo = $formatConfigList[$tem_pay_config_id] ?? [];
            $config_open_status = $avalConfigInfo['open_status'] ?? 0;// 开启状态(1开启2关闭)
            $config_pay_method = $avalConfigInfo['pay_method'] ?? 0;// 配置选中的支付方式
            if($config_open_status != 1){// 配置关闭状态，则都不能使用
                $config_pay_method = 0;
                $avalConfigInfo['pay_method'] = $config_pay_method;
            }

            // 去掉禁用的
            foreach($disablePayMethod as $disPayMethod){
                $pay_method = Tool::bitRemoveVal($pay_method, $disPayMethod);
            }
            // 配置中选中的，才是真的可用的
            $pay_method &= $config_pay_method;

            $v['pay_method'] = $pay_method;

            $v['allow_pay_method'] = $config_pay_method;// 可供选择的
            $v['pay_key'] = $avalConfigInfo['pay_key'] ?? '';
            $v['pay_company_name'] = $avalConfigInfo['pay_company_name'] ?? '';


            // 遍历支付方式的kv值
            foreach($payKVList as $k_pay_method => $v_method_text){
                if( ($pay_method & $k_pay_method) == $k_pay_method) array_push($pay_method_text_arr, $v_method_text);
            }
            $v['pay_method_text'] = implode('、', $pay_method_text_arr);
        }
        $returnFields = array_merge($returnFields, Tool::arrEqualKeyVal(['pay_method_text', 'allow_pay_method', 'pay_key', 'pay_company_name'],true));

        if(!$isMulti) $data_list = $data_list[0] ?? [];
    }
}
