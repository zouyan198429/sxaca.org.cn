<?php
// 短信模板
namespace App\Business\DB\QualityControl;

use App\Services\DB\CommonDB;
use App\Services\SMS\SendSMS;
use App\Services\Tool;

/**
 *
 */
class SmsTemplateDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'QualityControl\SmsTemplate';
    public static $table_name = 'sms_template';// 表名称
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

        // 模板关键字--唯一
        if( isset($saveData['template_key']) && static::judgeFieldExist($company_id, $id ,"template_key", $saveData['template_key'], [],1)){
            throws('模板关键字已存在！');
        }
        if( isset($saveData['template_code']) && isset($saveData['template_type']) && static::judgeFieldExist($company_id, $id ,"template_code", $saveData['template_code'], [['template_type', $saveData['template_type']]],1)){
            throws('模板ID已存在！');
        }
        // 如果有模板内容
        if( isset($saveData['template_content']) ){
            $template_content = $saveData['template_content'] ?? '';
            $paramsArr = Tool::getLabelArr($template_content, '{', '}');
            if(!empty($paramsArr)){
                $module_id = $saveData['module_id'] ?? 0;
                if($module_id <= 0){// 得新获取
                    if($id > 0){// 修改记录
                        $temInfo = static::getInfo($id);
                        if(empty($temInfo)) throws('记录不存在！');
                        $module_id = $temInfo['module_id'];
                    }else{// 新加记录
                        throws('参数module_id有误或不存在！');
                    }
                }
                // 获得模块参数
                $moduleInfo = SmsModuleDBBusiness::getInfo($module_id);
                if(empty($moduleInfo)) throws('模块记录不存在！');
                // 获得模块参数
                $paramsList = SmsModuleParamsDBBusiness::getDBFVFormatList(1, 1, ['module_id' => $module_id], false, [], []);
                if(!empty($paramsList)){
                    $param_codeArr = Tool::getArrFields($paramsList, 'param_code');
                    $paramsArr = array_diff($paramsArr, $param_codeArr);
                }

                if(!empty($paramsArr)){
                    // 获得通用的参数
                    $paramsCommonList = SmsModuleParamsCommonDBBusiness::getDBFVFormatList(1, 1, [], true, [], []);
                    if(!empty($paramsCommonList)){
                        $param_common_codeArr = Tool::getArrFields($paramsCommonList, 'param_code');
                        $paramsArr = array_diff($paramsArr, $param_common_codeArr);
                    }
                }
                if(!empty($paramsArr)) throws('模板内容参数[' . implode(',', $paramsArr) . ']不是有效参数，请修改！');
            }
        }

        $operate_staff_id_history = config('public.operate_staff_id_history', 0);// 0;--写上，不然后面要去取，但现在的系统不用历史表
        // 保存前的处理
        static::replaceByIdAPIPre($saveData, $company_id, $id, $operate_staff_id, $operate_staff_id_history, $modifAddOprate);
        $modelObj = null;
        //*********************************************************
        $isModify = false;
        CommonDB::doTransactionFun(function() use(&$saveData, &$company_id, &$id, &$operate_staff_id, &$modifAddOprate, &$operate_staff_id_history, &$modelObj, &$isModify ){


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
     * 模板发送短信
     *
     * @param int $sms_template_id 选择发短信的模板id
     * @param array $data_list 需要发送短信的记录 一维或二维数组
     * @param array $inputParamsArr 手动输入的参数值  ['参数代码' => '参数值'] -- 可为空数组：没有手动输入参数
     * @param string $sendMobileField 发送手机号字段
     * @param string $countryCode 国家码 86
     * @param int $send_type 发送类型【1系统发送、2手动发送】
     * @param int  $company_id 企业id
     * @param int $operate_staff_id 操作人id
     * @param int $modifAddOprate 修改时是否加操作人，1:加;0:不加[默认]
     * @return  mixed 列表数据
     * @author zouyan(305463219@qq.com)
     */
    public static function sendSms($sms_template_id, $data_list, $inputParamsArr = [], $sendMobileField = 'mobile', $countryCode = 86, $send_type = 1, $company_id = 0, $operate_staff_id = 0, $modifAddOprate = 0){

        // 手动输入的参数值
        // $inputParamsArr = [];// ['参数代码' => '参数值']
        // 发送手机号字段
        // $sendMobileField = 'mobile';
        if(empty($sendMobileField)) return true;

        // 如果是一维数组,则转为二维数组
        $isMulti = Tool::isMultiArr($data_list, true);
        // 去掉不是手机号的记录
        foreach($data_list as $k => $info){
            if(!isset($info[$sendMobileField])) throws('发送手机号字段指定有误，请重新选择！');
            $temMobile = $info[$sendMobileField] ?? '';
            if(empty($temMobile)){
                unset($data_list[$k]);
                continue;
            }
            // 判断手机号
            $valiDateParam = [
                ["var_name" => "mobile" ,"input" => $temMobile, "require"=>"true", "validator"=>"mobile", "message"=>'手机号格式有误！'],
            ];
            $errMsgArr = Tool::dataValid($valiDateParam, 2);
            if(is_array($errMsgArr) && isset($errMsgArr['errMsg']) && !empty($errMsgArr['errMsg'])){
                $recordErrText = implode('<br/>', $errMsgArr['errMsg']);
                unset($data_list[$k]);
                continue;
            }
        }
        if(empty($data_list)) return true;

        // 选择发短信的模板id
        // $sms_template_id = 1;
        // $templateInfo = CTAPISmsTemplateBusiness::getInfoData($request, $controller, $sms_template_id, [], '', [], $notLog);
        $templateInfo = SmsTemplateDBBusiness::getInfo($sms_template_id);
        if(empty($templateInfo)) throws('短信模板【' . $sms_template_id . '】记录不存在！');
        $template_name = $templateInfo['template_name'] ?? '';
        if($templateInfo['open_status'] != 1) throws('短信模板【' . $template_name . '】非启用状态，不可发送短信！');
        $template_content = $templateInfo['template_content'] ?? '';
        $template_type = $templateInfo['template_type'] ?? 0;// 模板类型【1腾讯云SMS、2阿里云短信】
        $template_code = $templateInfo['template_code'] ?? '';// 模板ID【第三方】
        $sign_name = $templateInfo['sign_name'] ?? '';// 签名名称【第三方】
        $template_key = 'sms_params';// $templateInfo['template_key'] ?? '';// 模板关键字【唯一】---这里手动指定，因为用户指定的值可能会有中文的情况
        $smsType = $template_key;
        $configCodeArr = [
            'SignName' => $sign_name,
            'TemplateCode' => $template_code,
        ];

        // 获得发短信的模块id
        $module_id = $templateInfo['module_id'] ?? 0;
        // $moduleInfo = CTAPISmsModuleBusiness::getInfoData($request, $controller, $module_id, [], '', [], $notLog);
        $moduleInfo = SmsModuleDBBusiness::getInfo($module_id);
        if(empty($moduleInfo)) throws('短信模块【' . $module_id . '】记录不存在！');
        $module_name = $moduleInfo['module_name'] ?? '';
        if($moduleInfo['open_status'] != 1) throws('短信模块【' . $module_name . '】非启用状态，不可发送短信！');

        // 获得模板内容参数
        $paramsArr = Tool::getLabelArr($template_content, '{', '}');

        // $countryCode = 86;
        $shuffle = false;// true;
        // 短信配置相关的信息
        $smsConfig = config('easysms');
        $configs = $smsConfig['gateways'] ?? [];
        $smsConfigList = [
            'aliyun' => [
                'access_key_id' => $configs['aliyun']['access_key_id'],
                'access_key_secret' => $configs['aliyun']['access_key_secret'],
                'sign_name' => $configs['aliyun']['sign_name'],//  签名名称
                'regionId' => $configs['aliyun']['regionId'],// 地域和可用区 https://help.aliyun.com/document_detail/40654.html?spm=a2c6h.13066369.0.0.54a120f89HVXHt
                // 尊敬的用户，您的验证码${code}，请在3分钟内使用，工作人员不会索取，请勿泄漏。
                // 参数必须是 [a-zA-Z0-9]
//                'verification_code_params' => [// 验证码相关参数
//                    'SignName' => env('ALIYUN_SMS_VERIFICATION_SIGN_NAME', ''),// 值为空或没有此下标，会自动使用上层的sign_name值。 短信签名名称。请在控制台签名管理页面签名名称一列查看。
//                    'TemplateCode' => env('ALIYUN_SMS_VERIFICATION_TEMPLATE_CODE', ''),// 短信模板ID。请在控制台模板管理页面模板CODE一列查看。
//
//                ],
//                'template_params' => [// 短信模板替换参数
//                    'verification_code_params' => ['code'],// 验证码模板 参数必须是 [a-zA-Z0-9]
//                ]
            ],
            'qcloud'   => [// 短信内容使用 content。
                'sdk_app_id' => $configs['qcloud']['sdk_app_id'], // SDK APP ID '腾讯云短信平台sdk_app_id'
                'app_key'    => $configs['qcloud']['app_key'], // APP KEY '腾讯云短信平台app_key'
                'secret_id' => $configs['qcloud']['secret_id'], // 通过接口访问时的 SecretId 密钥
                'secret_key' => $configs['qcloud']['secret_key'], // 通过接口访问时的 SecretKey 密钥
                'sign_name'  => $configs['qcloud']['sign_name'],// '可以不填写', // 对应的是短信签名中的内容（非id） '腾讯云短信平太签名'  (此处可设置为空，默认签名)
                /***
                 *
                 *
                 *  # 请选择大区 https://console.cloud.tencent.com/api/explorer?Product=sms&Version=2019-07-11&Action=SendSms&SignVersion=
                 *  # ap-beijing 华北地区(北京)
                 *  # ap-chengdu 西南地区(成都)
                 *  # ap-chongqing 西南地区(重庆)
                 *  # ap-guangzhou 华南地区(广州)
                 *  # ap-guangzhou-open 华南地区(广州Open)
                 *  # ap-hongkong 港澳台地区(中国香港)
                 *  # ap-seoul 亚太地区(首尔)
                 *  # ap-shanghai 华东地区(上海)
                 *  #
                 *  # ap-singapore 东南亚地区(新加坡)
                 *  # eu-frankfurt 欧洲地区(法兰克福)
                 *  # na-siliconvalley 美国西部(硅谷)
                 *  # na-toronto 北美地区(多伦多)
                 *  # ap-mumbai 亚太地区(孟买)
                 *  # na-ashburn 美国东部(弗吉尼亚)
                 *  # ap-bangkok 亚太地区(曼谷)
                 *  # eu-moscow 欧洲地区(莫斯科)
                 *  # ap-tokyo 亚太地区(东京)
                 *  # 金融区
                 *  # ap-shanghai-fsi 华东地区(上海金融)
                 *  # ap-shenzhen-fsi 华南地区(深圳金融)
                 *
                 */
                'regionId' => $configs['qcloud']['regionId'],// 地域和可用区
                // ID 468796  --- 作废，因为第一个参数不能传中文。所以不用了
                // 尊敬的用户：您的{1}验证码{2}，请在{3}分钟内使用，工作人员不会索取，请勿泄漏。
                // 1: operateType 操作类型 如：注册--用不了  ； 2： code 如：验证码 2456  ； 3 ：有效时间(单位分钟) validMinute 如 3

                // ID 470052
                // 内容 尊敬的用户：您的{1}验证码{2}，请在{3}分钟内使用，工作人员不会索取，请勿泄漏。
                // 1： code 如：验证码 2456  ； 2 ：有效时间(单位分钟) validMinute 如 3
//                'verification_code_params' => [// 验证码相关参数
//                    'SignName' => env('QCLOUD_SMS_VERIFICATION_SIGN_NAME', ''),// 值为空或没有此下标，会自动使用上层的sign_name值。 签名参数使用的是`签名内容`，而不是`签名ID`。这里的签名"腾讯云"只是示例，真实的签名需要在短信控制台申请
//                    'TemplateCode' => env('QCLOUD_SMS_VERIFICATION_TEMPLATE_CODE', ''),// 这里的模板 ID`7839`只是示例，真实的模板 ID 需要在短信控制台中申请
//                ],
//                'template_params' => [// 短信模板替换参数
//                    'verification_code_params' => ['code', 'validMinute'],// 'operateType', 验证码模板--注意验证码模板变量参数只能是<=6的数字，不能是中文及字母。
//                ]
            ],
        ];
        // pr($smsConfigList);
        // 默认可用的发送网关
//        [
//            // 'yunpian',//云片
//            // 'aliyun',// 阿里云短信
//            'qcloud', //腾讯云
//        ]
        $gateways = [];// ['qcloud'];//  $smsConfig['default']['gateways'] ?? [];
        if($template_type == 1 ){// 1腾讯云SMS
            array_push($gateways, 'qcloud');
            $smsConfigList['qcloud'][$smsType] = $configCodeArr;
            $smsConfigList['qcloud']['template_params'][$smsType] = $paramsArr;
        }
        if($template_type == 2 ){// 2阿里云短信
            array_push($gateways, 'aliyun');
            $smsConfigList['aliyun'][$smsType] = $configCodeArr;
            $smsConfigList['aliyun']['template_params'][$smsType] = $paramsArr;
        }
        if(empty($paramsArr)){// 没有参数，直接发送短信
            $mobileArr = Tool::getArrFields($data_list, $sendMobileField);
            SendSMS::sendSmsCommonBath($send_type, $templateInfo, $smsConfigList, $gateways, $template_content, [], $mobileArr, $countryCode, $smsType, $shuffle);
            return true;
        }

        $needParamsList = [];// 需要替换的参数
        // 获得短信模块参数
        // $smsModuleParamsList = CTAPISmsModuleParamsBusiness::getFVFormatList( $request,  $controller, 1, 1
//            , ['module_id' => $module_id], false, [], ['sqlParams' => ['orderBy' =>['sort_num' => 'desc', 'id' => 'desc']]], $notLog);
        $smsModuleParamsList = SmsModuleParamsDBBusiness::getDBFVFormatList(1, 1, ['module_id' => $module_id], false, [], ['sqlParams' => ['orderBy' =>['sort_num' => 'desc', 'id' => 'desc']]]);
        $smsModuleParamsFormatList = Tool::arrUnderReset($smsModuleParamsList, 'param_code', 1, '_');// 转为参数代码为下标的数组
        //$needParamsList = Tool::getArrFormatFields($smsModuleParamsFormatList, $paramsArr, false);// 获得指定下标的参数
        $temArr = $smsModuleParamsFormatList;
        $needParamsList = Tool::formatArrByKeys($temArr, $paramsArr, false);// 获得指定下标的参数

        $commonParamsArr = array_diff($paramsArr, array_keys($needParamsList));

        // 获得所有的常用参数
        if(!empty($commonParamsArr)){
//            $smsCommonParamsList = CTAPISmsModuleParamsCommonBusiness::getFVFormatList( $request,  $controller, 1, 1
//                , [], true, [], ['sqlParams' => ['orderBy' =>['sort_num' => 'desc', 'id' => 'desc']]], $notLog);
            $smsCommonParamsList = SmsModuleParamsCommonDBBusiness::getDBFVFormatList(1, 1, [], true, [], ['sqlParams' => ['orderBy' =>['sort_num' => 'desc', 'id' => 'desc']]]);

            $smsCommonParamsFormatList = Tool::arrUnderReset($smsCommonParamsList, 'param_code', 1, '_');// 转为参数代码为下标的数组
            // $needCommonParamsList = Tool::getArrFormatFields($smsCommonParamsFormatList, $commonParamsArr, false);// 获得指定下标的参数
            $temCommonArr = $smsCommonParamsFormatList;
            $needCommonParamsList = Tool::formatArrByKeys($temCommonArr, $commonParamsArr, false);// 获得指定下标的参数

            $lessParamsArr = array_diff($commonParamsArr, array_keys($needCommonParamsList));
            if(!empty($lessParamsArr)){
                throws('参数【' . implode('、', $lessParamsArr) . '】未配置！');
            }
            $needParamsList = array_merge($needParamsList, $needCommonParamsList);
        }

        // 对参数进行处理
        $publicDataParams = [];// 所有的参数值，字段的默认给空--可以占顺序
        $nowDateTime = date('Y-m-d H:i:s');
        // $hasFieldParams = false;// 是否有字段记录匹配参数 ； true:有--需要一条记录一条记录替换； false：没有--可以批量发送
        $fieldParamsArr = [];// 字段记录匹配参数数组
        foreach($paramsArr as $keyName){
            $paramConfigInfo = $needParamsList[$keyName] ?? [];
            if(empty($paramConfigInfo)) throws('参数【' . $keyName . '】配置不能为空！');
            $temParamName = $paramConfigInfo['param_name'];
            $temParamType = $paramConfigInfo['param_type'];
            $temDateTimeFormat = $paramConfigInfo['date_time_format'];
            $temFixedVal = $paramConfigInfo['fixed_val'];
            $temParamVal = '';
            switch($temParamType){// 参数类型1日期时间、2固定值、4手动输入-发送时、8字段匹配
                case 1:// 1日期时间
                    if(!empty($temDateTimeFormat)) $temParamVal = judgeDate($nowDateTime, $temDateTimeFormat);
                    break;
                case 2:// 2固定值
                    $temParamVal = $temFixedVal;
                    break;
                case 4:// 4手动输入-发送时
                    $temParamVal = $inputParamsArr[$keyName] ?? '';
                    break;
                case 8:// 8字段匹配
                    $temParamVal = '';
                    // $hasFieldParams = true;
                    array_push($fieldParamsArr, $keyName);
                    break;
                default:
                    break;
            }
            $publicDataParams[$keyName] = $temParamVal;
        }

        if(empty($fieldParamsArr)){// 可以 批量发送  !$hasFieldParams
            $mobileArr = Tool::getArrFields($data_list, $sendMobileField);
            // 替换共有的参数
            if(!empty($publicDataParams)) Tool::strReplaceKV($template_content, $publicDataParams, '{', '}');
            SendSMS::sendSmsCommonBath($send_type, $templateInfo, $smsConfigList, $gateways, $template_content, $publicDataParams, $mobileArr, $countryCode, $smsType, $shuffle);
            return true;
        }

        // 有第条记录单独的参数
        foreach($data_list as $k => $tInfo){
            $temParamsArr = $publicDataParams;
            $sendTemplateContent = $template_content;
            $temFieldValArr = Tool::getArrFormatFields($tInfo, $fieldParamsArr, true);// 获得指定下标的参数
            $temParamsArr = array_merge($temParamsArr, $temFieldValArr);
            // 替换共有的参数
            if(!empty($temParamsArr)) Tool::strReplaceKV($sendTemplateContent, $temParamsArr, '{', '}');

            $temMobileArr = [];
            $temMobile = $tInfo[$sendMobileField] ?? '';
            if(!is_array($temMobile) && !empty($temMobile)) $temMobileArr = explode(',', $temMobile);
            SendSMS::sendSmsCommonBath($send_type, $templateInfo, $smsConfigList, $gateways, $sendTemplateContent, $temParamsArr, $temMobileArr, $countryCode, $smsType, $shuffle);

        }
        return true;
    }
}
