<?php


namespace App\Services\Invoice\hydzfp;


use App\Services\Invoice\BaseInvoice;
use App\Services\Request\API\Sites\APIHYDZFPRequest;

class InvoiceHydzfp extends BaseInvoice
{
    public static $companyConfig = [
            'pay_company_name' => '江苏百旺金赋信息科技有限公司',// 企业名称
            'tax_num' => '91320106598035469W',// 企业税号
            'device_num' => '499111004317',// 设备编号
            'open_id' => 'I9lhovOqS1bdamapbn17NYMxgLbwEq8HmLhRdrR4d3VHma6JC9C1493185737711',// 应用OPENID
            'app_secret' => '6ocogiVdlv7G2jpYcNxmPDaQXDhoZvubxtrayaq3U4WwWOjRwUV1493185737714',// APP_SECRET
        ];

    /**
     * 获取access token
     *
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function getAccessToken($openid, $app_secret, $forceApi = false){
        $accessTokenConfig = APIHYDZFPRequest::getAccessToken($openid, $app_secret, $forceApi);
        return $accessTokenConfig;
    }

    // ~~~~~税收编码~~~~~~~~~~~~开始~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    /**
     * D0001-查询税收编码(单个商品)
     *  商品名称查询税控服务器商品信息(单个商品)
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function esdSkdataQueryItemInfo($openid, $app_secret, $apiDataMode = 0, $forceApi = false){
        $data = [
            "xmmc" => "*非学历教育服务*培训费",// "房地产开发住宅A",// 商品名称 -必填
            "nsrsbh" => "91320106598035469W",// "510302744676556"// 纳税人识别号 -必填

        ];
        APIHYDZFPRequest::getAPI($openid, $app_secret, 'esd_skdata_queryItemInfo', $data, $apiDataMode, $forceApi);
    }

    /**
     * D0003-查询税收编码(分页获取)
     *  商品名称查询税控服务器商品信息 (分页列表,search_key为like：商品名称和商品编码)
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function esdSkdataQueryItemsList($openid, $app_secret, $apiDataMode = 0, $forceApi = false){
        $companyConfig = static::$companyConfig;
        $data = [
            "nsrsbh" => $companyConfig['tax_num'],// "51019806696139X",// 是	string	20	纳税人识别号
            "start" => 1,// 是	string	10	分页数据起始标志,当前所在页数，第一页为1，第二页为2
            "limit" => 3,// 是	string	10	每页返回条数
            "search_key" => "测试"// 否	string	200	查询条件,通过商品名称和商品编码模糊匹配

        ];
        APIHYDZFPRequest::getAPI($openid, $app_secret, 'esd_skdata_queryItemsList', $data, $apiDataMode, $forceApi);
    }


    // ~~~~~税收编码~~~~~~~~~~~~结束~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    /**
     * A0001-开具蓝字发票
     *  商品名称查询税控服务器商品信息(单个商品)
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleNewBlueInvoice($data, $openid, $app_secret, $apiDataMode = 0, $forceApi = false){
//        $companyConfig = static::$companyConfig;
//        $data = [
//            "data_resources" => "API",// 是	string	4	固定参数 “API”
//            "nsrsbh" => $companyConfig['tax_num'],// "1246546544654654",// 是	string	20	销售方纳税人识别号
//            "skph" => "",// "123213123212",// 否	string	12	税控盘号（使用税控盒子必填，其他设备为空）
//            "order_num" => "1120521299480004",// "order_num_1474873042826",// 是	string	200	业务单据号；必须是唯一的
//            "bmb_bbh" => "33.0", // "29.0",// 是	string	10	税收编码版本号，参数“29.0”，具体值请询问提供商-- ?
//            "zsfs" => "0",// 是	string	2	征税方式 0：普通征税 1: 减按计增 2：差额征税
//            "tspz" => "00",// 否	string	2	特殊票种标识:“00”=正常票种,“01”=农产品销售,“02”=农产品收购
//            "xsf_nsrsbh" => $companyConfig['tax_num'],// "1246546544654654",//是	string	20	销售方纳税人识别号
//            "xsf_mc" => $companyConfig['pay_company_name'],// "\t自贡市有限公司",// 是	string	100	销售方名称
//            "xsf_dzdh" => "自贡市斯柯达将阿里是可大家是大家圣诞节阿拉斯加大开杀戒的拉开手机端 13132254",// 是	string	100	销售方地址、电话
//            "xsf_yhzh" =>  "124654654123154",// 是	string	100	销售方开户行名称与银行账号
//            "gmf_nsrsbh" =>  "",// 否	string	100	购买方纳税人识别号(税务总局规定企业用户为必填项)
//            "gmf_mc" =>  "个人",// 是	string	100	购买方名称
//            "gmf_dzdh" =>  "",// 否	string	100	购买方地址、电话
//            "gmf_yhzh" =>  "",// 否	string	100	购买方开户行名称与银行账号
//            "kpr" => "开票员A",// 是	string	8	开票人
//            "skr" =>  "",// 否	string	8	收款人
//            "fhr" =>  "",// 否	string	8	复核人
//            "yfp_dm" =>  "",// 否	string	12	原发票代码
//            "yfp_hm" =>  "",// 否	string	8	原发票号码
//            // 是	string	#.##	价税合计;单位：元（2位小数） 价税合计=合计金额(不含税)+合计税额
//            // 注意：不能使用商品的单价、数量、税率、税额来进行累加，最后四舍五入，只能是总合计金额+合计税额
//            "jshj" =>  "1.00",
//            "hjje" => "0.97",// "0.88",// 是	string	#.##	合计金额 注意：不含税，单位：元（2位小数）
//            "hjse" =>  "0.03",// "0.12",// 是	string	#.##	合计税额单位：元（2位小数）
//            "kce" =>  "",// 否	string	#.##	扣除额小数点后2位，当ZSFS为2时扣除额为必填项
//            "bz" =>  "备注啊哈哈哈哈",// 否	string	100	备注 (长度100字符)
//            // "kpzdbs" => "",// 否	string	20	已经失效，不再支持
//            "jff_phone" => "",// "手机号",// 否	string	11	手机号，针对税控盒子主动交付，需要填写
//            "jff_email" => "",// "电子邮件",// 否	string	100	电子邮件，针对税控盒子主动交付，需要填写
//            "common_fpkj_xmxx" => [
//                [
//                    "fphxz" => "0",// 是	string	2	发票行性质 0正常行、1折扣行、2被折扣行
//                    "spbm" => "3070201020000000000",// "",// 是	string	19	商品编码(商品编码为税务总局颁发的19位税控编码)
//                    "zxbm" => "",// 否	string	20	自行编码(一般不建议使用自行编码)
//                    "yhzcbs" => "0",// "",//否	string	2	优惠政策标识 0：不使用，1：使用
//                    "lslbs" => "",// 否	string	2	零税率标识 空：非零税率， 1：免税，2：不征收，3普通零税率
//                    // 否	string	50	增值税特殊管理-如果yhzcbs为1时，此项必填，
//                    // 具体信息取《商品和服务税收分类与编码》中的增值税特殊管理列。(值为中文)
//                    "zzstsgl" => "",// aa  bbb
//                    // 是	string	90	项目名称 (必须与商品编码表一致;如果为折扣行，商品名称须与被折扣行的商品名称相同，不能多行折扣。
//                    // 如需按照税控编码开票，则项目名称可以自拟,但请按照税务总局税控编码规则拟定)
//                    "xmmc" => "培训费",// "更具自身业务决定",// aa  bbb
//                    "ggxh" => "",// 否	string	30	规格型号(折扣行请不要传)
//                    "dw" => "",// 否	string	20	计量单位(折扣行请不要传)
//                    "xmsl" => "1",// "",// 否	string	#.######	项目数量 小数点后6位,大于0的数字
//                    "xmdj" => "1.00",// 否	string	#.######	项目单价 小数点后6位 注意：单价是含税单价,大于0的数字
//                    "xmje" => "1.00",// 是	string	#.##	项目金额 注意：金额是含税，单位：元（2位小数）
//                    "sl" => "0.03",// "0.13",// 是	string	#.##	税率 例1%为0.01
//                    "se" => "0.03",// "0.12"// 是	string	#.##	税额 单位：元（2位小数）
//                ]
//            ]
//        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_newBlueInvoice', $data, $apiDataMode, $forceApi);
        return $result;
        /**
        $result = [
            [
                "fhr" => "",// string	复核人
                "xsf_mc" => "江苏百旺金赋信息科技有限公司",// string	销售方名称
                "yfp_hm" => "",// string	原发票号码
                "bz" => "备注啊哈哈哈哈",// string	备注
                "xsf_yhzh" => "124654654123154",// string	销售方银行账号
                "gmf_mc" => "个人",// string	购买方名称
                "hjse" => "0.03",// string	合计税额
                "fp_dm" => "050003521270",// string	发票代码
                "kce" => "",// string	扣除额
                "yfp_dm" => "",// string	原发票代码
                "fp_hm" => "69023540",// string	发票号码
                "common_fpkj_xmxx" => [// object	商品明细
                    [
                        "fphxz" => "0",// string	发票行性质
                        "spbm" => "3070201020000000000",// string	商品编码
                        "zxbm" => "",// string	自行编码
                        "yhzcbs" => "0",// string	优惠政策标识
                        "lslbs" => "",// string	零税率标识
                        "zzstsgl" => "",// string	增值税特殊管理
                        "xmmc" => "*非学历教育服务*培训费",// string	项目名称
                        "ggxh" => "",// string	规格型号
                        "dw" => "",// string	计量单位
                        "xmsl" => "1",// string	项目数量
                        "xmdj" => "0.970873786407767",// string	项目单价(不含税)
                        "xmje" => "0.97",// string	项目金额(不含税)
                        "sl" => "0.03",// string	税率
                        "se" => "0.03"// string	税额
                    ]
                ],
                "gmf_nsrsbh" => "",// string	购买方纳税人识别号
                "itype" => "026",// string	发票类型(026=电票,004=专票,007=普票，025=卷票)
                "jff_phone" => "",// aaa
                "jym" => "02581197460769559807",// string	校验码
                "kplx" => "0",// string	开票类型 0-蓝字发票；1-红字发票
                "pdf_item_key" => "pdf_detail_OZwKHou1609236806278",// string	发票清单PDF文件获取key
                "order_num" => "1120521299480004",// string	业务单据号
                "kpzdbs" => "",// aaa
                "zsfs" => "0",// string	征税方式 0：普通征税 2：差额征税
                "xsf_dzdh" => "自贡市斯柯达将阿里是可大家是大家圣诞节阿拉斯加大开杀戒的拉开手机端13132254",// string	销售方地址、电话
                "jqbh" => "499111004317",// string	税控设备机器编号
                "hjje" => "0.97",// string	合计金额(不含税)
                "ext_code" => "2fahsn2gbk",// string	提取码
                "tspz" => "00",// aaa
                "gmf_dzdh" => "",// string	购买方地址、电话
                "fpqqlsh" => "OZwKHou1609236806278",// string	发票请求流水号
                "skr" => "",// string	收款人
                "jff_email" => "",// aaa
                "gmf_yhzh" => "",// string	购买方银行账号
                "kpr" => "开票员A",// string	开票人
                "xsf_nsrsbh" => "91320106598035469W",// string	销售方纳税人识别号
                // string	发票密文
                "fp_mw" => "03/>29631>055*55<3682-05<800<61+4>>6587*764494-/-0<143317-/>29631>055*55<3318900373638/4-173-*01247919274//-6>0-",
                "jshj" => "1.00",// string	价税合计
                "pdf_key" => "pdf_OZwKHou1609236806278",// string	发票PDF文件获取key
                "kprq" => "20201229170644"// string	开票日期(20161107145525格式：yyyymmddhhmiss)
            ]
        ];
        */

    }

    /**
     * A0002-开具红字发票
     *   开具红字发票
     *   只能开具在本系统中开具的蓝字发票对应的红字发票；
     *   电子发票的蓝字发票冲红，必须为整张发票全额冲红；系统自动将红字发票对应的蓝字发票全额冲红；
     * @param  array $data 请求数据 一维数组
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleNewRedInvoice($data, $openid, $app_secret, $apiDataMode = 0, $forceApi = false){
        $companyConfig = static::$companyConfig;
//        $data = [
//            "data_resources" => "API",// 是	string	4	固定值 API
//            "nsrsbh" => $companyConfig['tax_num'],//"123123123",// 是	string	20	销货单位纳税人识别号
//            "skph" => "",// "123213123212",// 否	string	12	税控盘号（使用税控盒子必填，其他设备为空）
//            "order_num" => "1120521299480002",// "order_num_147486801",// 是	string	200	业务单据号；必须是唯一的
//            "yfp_dm" => "050003521270",// "150003529999",// 是	string	12	发票代码
//            "yfp_hm" => "69023540",// "65942490",// 是	string	8	发票号码
//            "bz" =>  "行行行存储",// 否	string	100	发票备注
//            "kpr" => "开票人1",// 否	string	8	开票人
//            "skr" => "收款人2",// 否	string	8	收款人
//            "fhr" => "复核人3",// 否	string	8	复核人
//            "kpzdbs" => ""// 否	string	20	已经失效，不再支持
//        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_newRedInvoice', $data, $apiDataMode, $forceApi);
        /**
         *
        $result = [
            [
                "fhr" => "复核人3",// string	复核人
                "xsf_mc" => "江苏百旺金赋信息科技有限公司",// string	销售方名称
                "yfp_hm" => "69023540",// string	原发票号码
                "bz" => "对应正数发票代码:050003521270 号码:69023540行行行存储",// string	备注
                "xsf_yhzh" => "中国光大银行南京分行营业部76490188000448711",// string	销售方银行账号
                "gmf_mc" => "个人",// string	购买方名称
                "hjse" => "-0.03",// string	合计税额
                "fp_dm" => "050003521270",// string	发票代码
                "kce" => "",// string	扣除额
                "yfp_dm" => "050003521270",// string	原发票代码
                "fp_hm" => "69023571",// string	发票号码
                "common_fpkj_xmxx" => [// object	商品明细
                    [
                        "sl" => "0.03",// string	税率
                        "dw" => "",// string	计量单位
                        "xmdj" => "0.970873786407767",// string	项目单价
                        "lslbs" => "",// string	零税率标识
                        "xmje" => "-0.97",// string	项目金额
                        "xmmc" => "*非学历教育服务*培训费",// string	项目名称
                        "zxbm" => "",// string	自行编码
                        "se" => "-0.03",// string	税额
                        "yhzcbs" => "0",// string	优惠政策标识
                        "zzstsgl" => "",// string	增值税特殊管理
                        "fphxz" => "0",// string	发票行性质
                        "ggxh" => "",// string	规格型号
                        "xmsl" => "-1.0",// string	项目数量
                        "spbm" => "3070201020000000000"// string	商品编码
                    ]
                ],
                "gmf_nsrsbh" => "",// string	购买方纳税人识别号
                "itype" => "026",// string	发票类型(026=电票,004=专票,007=普票，025=卷票)
                "jff_phone" => "15829686962",// aaaa
                "jym" => "18441629046525686862",// string	校验码
                "kplx" => "1",// string	开票类型 0-蓝字发票；1-红字发票
                "pdf_item_key" => "pdf_detail_C6Mih301609315817273",// string	发票清单PDF文件获取key
                "order_num" => "1120521299480002",// string	业务单据号
                "kpzdbs" => "",// aaaa
                "zsfs" => "0",// string	征税方式 0：普通征税 2：差额征税
                "xsf_dzdh" => "南京市鼓楼区中山北路26号新晨国际大厦15F025-86800401",// string	销售方地址、电话
                "jqbh" => "499111004317",// string	税控设备机器编号
                "hjje" => "-0.97",// string	合计金额
                "ext_code" => "zrl2d4uigm",// aaaa
                "tspz" => "00",// aaaa
                "gmf_dzdh" => "",// string	购买方地址、电话
                "fpqqlsh" => "C6Mih301609315817273",// string	发票请求流水号
                "skr" => "收款人2",// string	收款人
                "jff_email" => "305463219@qq.com",// aaaa
                "gmf_yhzh" => "",// string	购买方银行账号
                "kpr" => "开票人1",// string	开票人
                "xsf_nsrsbh" => "91320106598035469W",// string	销售方纳税人识别号
                // string	发票密文
                "fp_mw" => "03-9>/-+*42-/7>46/4+*><4+>/<5/80911<7756-3>7+->57*54400418-9>/-+*42-/7>46/>290/0*<000*885+3<2111647919<8*5916<>5",
                "jshj" => "-1.0",// 	string	价税合计
                "pdf_key" => "pdf_C6Mih301609315817273",// string	发票PDF文件获取key
                "kprq" => "20201230150333"// string	开票日期(20161107145525格式：yyyymmddhhmiss)
            ]
        ];
         *
         */
        return $result;

    }

    /**
     * A0003-获取发票或使用抬头开票
     *   获取发票或使用抬头开票;用于客户业务系统销售商品后，打印出销售小票，小票中生成获取电子发票二维码，客户扫码获取电子发票的场景；
     *   如果该发票在待开队列中并未开出，则可以只用本接口更改购买方抬头信息，并将发票立即开出；
     *   如果该发票已经开出，则直接返回发票信息；
     *   一般开发者可以首先调用“查单个发票”接口（只需纳税人识别号和业务单据号即可）查询该张发票是否已经开出，
     *    如果已经开出，则无需调用本接口，如果没有开出，则再调用本接口将待开队列的发票开具；
     *  小票二维码处理方式分为两种：
     *   1.开发者具有处理能力，并自行定义规则生成处理；
     *   2.开发者不具备处理能力，开发者可以按照我们定义的生成规则生成二维码，再将二维码交由我们来处理；
     *   二维码生成规则请在商户的开放平台中下载。
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleNewOrGetBlueInvoice($openid, $app_secret, $apiDataMode = 0, $forceApi = false){
        $companyConfig = static::$companyConfig;
        $data = [
            "nsrsbh" => $companyConfig['tax_num'],// "123123123",// 是	string	20	纳税人识别号
            "order_num" => "1120505471010040",// "1120512670520001",// "order_num_147487826",// 是	string	200	业务单据号
            "gmf_nsrsbh" => "610113399802848",// "54012021245154140",// 否	string	20	购买方纳税人识别号
            "gmf_mc" => "西安卓彩广告装饰有限公司",// "个人啊",// 否	string	100	购买方名称(当该张发票未开出时,必填)
            "gmf_dzdh" => "西安市雁塔区融鑫路0号丽湾蓝岛第1幢3单元26层32606号15829686962",// "地址哟 电话哟",// 否	string	100	购买方地址电话
            "gmf_yhzh" => "华夏银行股份有限公司西安分行营业部11450000000871357",// "5415110451515454"// 否	string	100	购买方银行账号
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_newOrGetBlueInvoice', $data, $apiDataMode, $forceApi);
//        $result = [
//            [
//                "JYM" => "06801466665358756235",// string	校验码
//                "GMF_MC" => "西安卓彩广告装饰有限公司",// string	购买方名称
//                "XSF_DZDH" => "上海市某某路0232345678",// string	销售方地址、电话
//                "XSF_NSRSBH" => "91320106598035469W",// string	销售方纳税人识别号
//                "ORDER_NUM" => "1120512670520001",// string	业务单据号
//                "ZSFS" => "0",// string	征税方式 0：普通征税 2：差额征税
//                "JFF_PHONE" => "15829686962",// aaaa
//                "PDF_ITEM_KEY" => "",// string	发票清单PDF文件获取key
//                "JSHJ" => "1",// string	价税合计
//                "common_fpkj_xmxx" => [// object	商品明细
//                    [
//                        "SE" => "0.03",// string	税额
//                        "ORDER_NUM" => "1120512670520001",// aaaa
//                        "FPQQLSH" => "OMKjM791609317875383",// aaaa
//                        "SL" => "0.03",// string	税率
//                        "GGXH" => "",// string	规格型号
//                        "ZZSTSGL" => "",// string	增值税特殊管理
//                        "XMSL" => "1",// string	项目数量
//                        "LSLBS" => "",// string	零税率标识
//                        "DW" => "",// string	计量单位
//                        "FPHXZ" => "0",// string	发票行性质
//                        "SPBM" => "3070201020000000000",// string	商品编码
//                        "KPRQ" => "2020-12-30 15:37:52",// aaaa
//                        "XMDJ" => "0.970873786407767",// string	项目单价
//                        "XMMC" => "*非学历教育服务**非学历教育服务*培训费",// string	项目名称
//                        "XMJE" => "0.97",// string	项目金额
//                        "ID" => "63647583",// aaaa
//                        "YHZCBS" => "0",// string	优惠政策标识
//                        "ZXBM" => ""// string	自行编码
//                    ]
//                ],
//                "BZ" => "备注",// string	备注
//                "KPRQ" => "2020-12-30 15:37:52",// string	开票日期(20161107145525格式：yyyymmddhhmiss)
//                "GMF_NSRSBH" => "610113399802848",// string	购买方纳税人识别号
//                "JQBH" => "499111004317",// string	税控设备机器编号
//                "EXT_CODE" => "iveryllxf3",// aaaa
//                "DATA_RESOURCES" => "API",// aaaa
//                "ID" => "35601992",// aaaa
//                "JFF_EMAIL" => "305463219@qq.com",// aaaa
//                // string	发票密文
//                "FP_MW" => "03023/*53321*57<>+7144*6//5>+258>0/99>463733</896<>6*65*<75+--23*76/-969>830/2+*255*0<>3*>83-10164791941/17*>7+7",
//                "KPLX" => "0",// string	开票类型 0-蓝字发票；1-红字发票
//                "KPR" => "开票员",// string	开票人
//                "SKR" => "收款人",// string	收款人
//                "KCE" => "",// string	扣除额
//                "FHR" => "复核人",// string	复核人
//                "XSF_YHZH" => "环球银行123456",// string	销售方银行账号
//                "FPQQLSH" => "OMKjM791609317875383",// string	发票请求流水号
//                "FP_HM" => "69023573",// string	发票号码
//                "FP_DM" => "050003521270",// string	发票代码
//                "ITYPE" => "026",// string	发票类型(026=电票,004=专票,007=普票，025=卷票)
//                "GMF_DZDH" => "西安市雁塔区融鑫路0号丽湾蓝岛第1幢3单元26层32606号15829686962",// string	购买方地址、电话
//                "KPZDBS" => "",// aaaa
//                "XSF_MC" => "江苏百旺金赋信息科技有限公司",// string	销售方名称
//                "HJJE" => "0.97",// string	合计金额
//                "TSPZ" => "00",// aaaa
//                "PDF_KEY" => "pdf_OMKjM791609317875383",// string	发票PDF文件获取key
//                "YFP_DM" => "",// string	原发票代码
//                "HJSE" => "0.03",// string	合计税额
//                "YFP_HM" => "",// string	原发票号码
//                "GMF_YHZH" => "华夏银行股份有限公司西安分行营业部11450000000871357"// string	购买方银行账号
//            ]
//        ];
        return $result;

    }

    /**
     * A0004-历史发票数据导入平台
     *   使用发票数据生成电子发票PDF
     *   税控设备开票不在本系统完成，用于在本系统中通过已经开出的发票数据生成电子发票PDF文件文件；
     *   用于： 1、税控盘开票后生成电子发票；2、历史发票数据导入平台;
     *   注意：该接口生成的电子票PDF文件是依据开发者传入的参数进行原样生成，只校验数据格式合法性,不会校验业务数据有效性；
     *   发票备注字段(bz)特殊说明：现版本增加换行付处理，换行符为 #n# 小写n字幕，在发票PDF生成时会自动按照换行符进行换行，#n#不占用总字符数；
     *   请求报文中xmdj，备注由含税改为了不含税2020-01-20 11:14:26。
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleNewPDFInvoice($openid, $app_secret, $apiDataMode = 0, $forceApi = false){
        $companyConfig = static::$companyConfig;
        $data = [
            "data_resources" => "API",// 是	string	4	固定值 API
            "nsrsbh" => $companyConfig['tax_num'],// "51019806696139X",// 是	string	20	销售方纳税人识别号
            "order_num" => "order_num_1477889851060",// 是	string	200	业务单据号；必须唯一；
            "fp_dm" => "150003529929",// 是	string	12	发票代码
            "fp_hm" => "65942416",// 是	string	8	发票号码
            "kplx" => "0",// 是	string	2	开票类型 0-蓝字发票；1-红字发票
            // string	4000	发票密文
            "fp_mw" => "038>6/6443++352973>-0633497>73061/*0*8<32138+715/392/+>325<26/0--1+5+3+8*+*+7982<\/4>+>67*555*<01817419/-*<>3<<41",
            "jym" => "1892739817239871",// 否	string	20	校验码
            "kprq" => "20160205",// 是	string	14	开票日期 (20161107145525格式：yyyymmddhhmiss)
            "jqbh" => "2134654646",// 是	string	100	税控设备机器编号
            "zsfs" => "0",// 是	string	2	征税方式 0：普通征税 1: 减按计增 2：差额征税
            "tspz" => "00",// 否	string	2	特殊票种标识:“00”=正常票种,“01”=农产品销售,“02”=农产品收购
            "xsf_nsrsbh" => $companyConfig['tax_num'],// "45656456",// 是	string	20	销售方纳税人识别号
            "xsf_mc" => $companyConfig['pay_company_name'],// "\t自贡市商业银行股份有限公司",// 是	string	100	销售方名称
            "xsf_dzdh" => "自贡市斯柯达将阿里是可大家是大家圣诞节阿拉斯加大开杀戒的拉开手机端 13132254",// 是	string	100	销售方地址、电话
            "xsf_yhzh" => "",// "2234234",// 否	string	100	销售方银行账号
            "gmf_nsrsbh" => "",// "gmf_nsrsbh",// 否	string	20	购买方纳税人识别号
            "gmf_mc" => "个人",// 是	string	100	购买方名称
            "gmf_dzdh" => "",// "gmf_dzdh",// 否	string	100	购买方地址、电话
            "gmf_yhzh" => "",// "gmf_yhzh",// 否	string	100	购买方银行账号
            "kpr" => "开票员A",// 是	string	8	开票人
            "skr" => "收款人B",// 否	string	8	收款人
            "fhr" => "复核人C",// 否	string	8	复核人
            "yfp_dm" => "",// "yfp_dm",// 否	string	12	原发票代码
            "yfp_hm" => "",// "yfp_hm",// 否	string	8	原发票号码
            "jshj" => "1.00",// "113.00",// 是	string	#.##	价税合计
            "hjje" => "0.97",// "100.00",// 是	string	#.##	合计金额
            "hjse" => "0.03",// "13.00",// 是	string	#.##	合计税额
            "kce" => "",// 否	string	#.##	扣除额
            "bz" => "备注啊哈哈哈哈",// 否	string	100	备注
            "ewm" => "",// 否	string	4000	发票票面二维码base64数据
            "common_fpkj_xmxx" => [// object	-	-	商品明细
                [
                    "fphxz" => "0",// 是	string	2	发票行性质 0正常行、1折扣行、2被折扣行
                    "spbm" => "3070201020000000000",// "spbm",// 否	string	19	商品编码(商品编码为税务总局颁发的19位税控编码)
                    "zxbm" => "",// "zxbm",// 否	string	20	自行编码(一般不建议使用自行编码)
                    "yhzcbs" => "0",// "",// 否	string	2	优惠政策标识 0：不使用，1：使用
                    "lslbs" => "",// 否	string	2	零税率标识 空：非零税率， 1：免税，2：不征收，3普通零税率
                    "zzstsgl" => "",// 否	string	50	增值税特殊管理-如果yhzcbs为1时，此项必填，具体信息取《商品和服务税收分类与编码》中的增值税特殊管理列。(值为中文)
                    // 是	string	90	项目名称 (必须与商品编码表一致;如果为折扣行，商品名称须与被折扣行的商品名称相同，不能多行折扣。
                    //  如需按照税控编码开票，则项目名称可以自拟,但请按照税务总局税控编码规则拟定)
                    "xmmc" => "*非学历教育服务*培训费",// aaaaa
                    "ggxh" => "",// "ggxh",// 否	string	30	规格型号(折扣行请不要传)
                    "dw" => "",// "dw",// 否	string	20	计量单位(折扣行请不要传)
                    "xmsl" => "1",// 否	string	#.######	项目数量 小数点后6位,大于0的数字
                    "xmdj" => "1.00",// "10.00",// 否	string	#.######	项目单价 小数点后6位 注意：单价是不含税单价,大于0的数字
                    "xmje" => "1.00",// "10.00",// 是	string	#.##	项目金额 注意：金额是含税，单位：元（2位小数）
                    "sl" => "0.03",// "0.13",// 是	string	#.##	税率 6位小数，例1%为0.01
                    "se" => "0.03",// "1.30"// 是	string	#.##	税额 单位：元（2位小数）
                ]
            ]
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_newPDFInvoice', $data, $apiDataMode, $forceApi);

        /**
         *

        $result = [
            "data_resources" => "API",
            "nsrsbh" => "91320106598035469W",
            "order_num" => "order_num_1477889851060",
            "fp_dm" => "150003529929",
            "fp_hm" => "65942416",
            "kplx" => "0",
            "fp_mw" => "038>6/6443++352973>-0633497>73061/*0*8<32138+715/392/+>325<26/0--1+5+3+8*+*+7982<\\/4>+>67*555*<01817419/-*<>3<<41",
            "jym" => "1892739817239871",
            "kprq" => "20160205",
            "jqbh" => "2134654646",
            "zsfs" => "0",
            "tspz" => "00",
            "xsf_nsrsbh" => "91320106598035469W",
            "xsf_mc" => "江苏百旺金赋信息科技有限公司",
            "xsf_dzdh" => "自贡市斯柯达将阿里是可大家是大家圣诞节阿拉斯加大开杀戒的拉开手机端 13132254",
            "xsf_yhzh" => "",
            "gmf_nsrsbh" => "",
            "gmf_mc" => "个人",
            "gmf_dzdh" => "",
            "gmf_yhzh" => "",
            "kpr" => "开票员A",
            "skr" => "收款人B",
            "fhr" => "复核人C",
            "yfp_dm" => "",
            "yfp_hm" => "",
            "jshj" => "1.00",
            "hjje" => "0.97",
            "hjse" => "0.03",
            "kce" => "",
            "bz" => "备注啊哈哈哈哈",
            "ewm" => "",
            "common_fpkj_xmxx" => [
                    [
                    "fphxz" => "0",
                    "spbm" => "3070201020000000000",
                    "zxbm" => "",
                    "yhzcbs" => "0",
                    "lslbs" => "",
                    "zzstsgl" => "",
                    "xmmc" => "*非学历教育服务*培训费",
                    "ggxh" => "",
                    "dw" => "",
                    "xmsl" => "1",
                    "xmdj" => "1.00",
                    "xmje" => "1.00",
                    "sl" => "0.03",
                    "se" => "0.03"
                ]
            ],
            "userid" => "439658478412BB29E0530100007FC1D6",
            "APP_ACCOUNT" => "I9lhovOqS1bdamapbn17NYMxgLbwEq8HmLhRdrR4d3VHma6JC9C1493185737711",
            "FP_DM" => "150003529929",
            "FP_HM" => "65942416",
            "JYM" => "1892739817239871",
            "KPRQ" => "20160205",
            "FP_MW" => "038>6/6443++352973>-0633497>73061/*0*8<32138+715/392/+>325<26/0--1+5+3+8*+*+7982<\\/4>+>67*555*<01817419/-*<>3<<41",
            "BZ" => "备注啊哈哈哈哈",
            "JQBH" => "2134654646",
            "FPQQLSH" => "PcaSAAF1609322604740",
            "fpqqlsh" => "PcaSAAF1609322604740",
            "EWM" => "",
            "ext_code" => "afkasfdyzo",
            "pdf_key" => "pdf_PcaSAAF1609322604740",
            "pdf_item_key" => "pdf_detail_PcaSAAF1609322604740"
        ];
         *
         */
        return $result;

    }

    /**
     * A0005-开具红字发票(部分冲红)
     *   开具红字发票(部分冲红)
     *   开具蓝字发票规则相同，改接口为红字发票开具接口，支持部分冲红(但一张蓝票只能冲红一次)，支持在平台没有蓝票的情况下 直接开具红票；
     *   红票金额不能大于蓝票金额
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleNewRedApiInvoice($openid, $app_secret, $apiDataMode = 0, $forceApi = false){
        $companyConfig = static::$companyConfig;
        $data = [
            "data_resources" => "API",// 是	string	4	固定参数 “API”
            "nsrsbh" => $companyConfig['tax_num'],// "1246546544654654",// 是	string	20	销售方纳税人识别号
            "skph" => "",// "123213123212",// 否	string	12	税控盘号（使用税控盒子必填，其他设备为空）
            "order_num" => "order_num_1474873042899",// 是	string	200	业务单据号；必须是唯一的
            "bmb_bbh" => "33.0",// 是	string	10	参数“29.0”，具体值请询问提供商
            "zsfs" => "0",// 是	string	2	征税方式 0：普通征税 1: 减按计增 2：差额征税
            "tspz" => "00",// 否	string	2	特殊票种标识:“00”=正常票种,“01”=农产品销售,“02”=农产品收购
            "xsf_nsrsbh" => $companyConfig['tax_num'],// "1246546544654654",// 是	string	20	销售方纳税人识别号
            "xsf_mc" => $companyConfig['pay_company_name'],// "\t自贡市有限公司",// 是	string	100	销售方名称
            "xsf_dzdh" => "西安市雁塔区融鑫路0号丽湾蓝岛第1幢3单元26层32606号15829686962",// "自贡市斯柯达将阿里是可大家是大家圣诞节阿拉斯加大开杀戒的拉开手机端 13132254",// 是	string	100	销售方地址、电话
            "xsf_yhzh" => "华夏银行股份有限公司西安分行营业部11450000000871357",// "",// "124654654123154",// 否	string	100	销售方开户行名称与银行账号
            "gmf_nsrsbh" => "610113399802848",// "",// 否	string	100	购买方纳税人识别号(税务总局规定企业用户为必填项)
            "gmf_mc" => "西安卓彩广告装饰有限公司",//"个人",// 是	string	100	购买方名称
            "gmf_dzdh" => "",// 否	string	100	购买方地址、电话
            "gmf_yhzh" => "",// 否	string	100	购买方开户行名称与银行账号
            "kpr" => "开票员A",// 是	string	8	开票人
            "skr" => "收款人B",// 否	string	8	收款人
            "fhr" => "复核人C",// 否	string	8	复核人
            "yfp_dm" => "050003521270",// 是	string	12	原发票代码
            "yfp_hm" => "69023573",// 是	string	8	原发票号码
            // 是	string	#.##	价税合计;单位：元（2位小数） 价税合计=合计金额(不含税)+合计税额
            //  注意：不能使用商品的单价、数量、税率、税额来进行累加，最后四舍五入，只能是总合计金额+合计税额（小于0的数字）
            "jshj" => "-1.00",// aaaaa
            "hjje" => "-0.97",// 是	string	#.##	合计金额 注意：不含税，单位：元（2位小数） （小于0的数字）
            "hjse" => "-0.03",// 是	string	#.##	合计税额单位：元（2位小数） （小于0的数字）
            "kce" => "",// 否	string	#.##	扣除额小数点后2位，当ZSFS为2时扣除额为必填项
            "bz" => "备注啊哈哈哈哈",// 否	string	100	备注 (长度100字符)
            "common_fpkj_xmxx" => [// object	-	-	商品明细
                [
                    "fphxz" => "0",// 是	string	2	发票行性质 0正常行、1折扣行、2被折扣行
                    "spbm" => "3070201020000000000",// 是	string	19	商品编码(商品编码为税务总局颁发的19位税控编码)
                    "zxbm" => "",// 否	string	20	自行编码(一般不建议使用自行编码)
                    "yhzcbs" => "0",// "",// 否	string	2	优惠政策标识 0：不使用，1：使用
                    "lslbs" => "",// 否	string	2	零税率标识 空：非零税率， 1：免税，2：不征收，3普通零税率
                    // 否	string	50	增值税特殊管理-如果yhzcbs为1时，此项必填，具体信息取《商品和服务税收分类与编码》中的增值税特殊管理列。(值为中文)
                    "zzstsgl" => "",// aaaaa
                    // 是	string	90	项目名称 (必须与商品编码表一致;如果为折扣行，商品名称须与被折扣行的商品名称相同，不能多行折扣。
                    //如需按照税控编码开票，则项目名称可以自拟,但请按照税务总局税控编码规则拟定)
                    "xmmc" => "*非学历教育服务*培训费",// "更具自身业务决定",// aaaaa
                    "ggxh" => "",// ggxh	否	string	30	规格型号(折扣行请不要传)
                    "dw" => "",// 否	string	20	计量单位(折扣行请不要传)
                    "xmsl" => "-1",// 否	string	#.######	项目数量 小数点后6位,小于0的数字
                    "xmdj" => "1.00",// 否	string	#.######	项目单价 小数点后6位 注意：单价是含税单价,大于0的数字
                    "xmje" => "-1.00",// 是	string	#.##	项目金额 注意：金额是含税，单位：元（2位小数）小于0的数字
                    "sl" => "0.03",// 是	string	#.##	税率 6位小数，例1%为0.01
                    "se" => "-0.03"// 是	string	#.##	税额 单位：元（2位小数） 小于0的数字
                ]
            ]
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_newRedApiInvoice', $data, $apiDataMode, $forceApi);

//        $result =  [
//            [
//                "fhr" => "复核人C",// fhr	string	复核人
//                "xsf_mc" => "江苏百旺金赋信息科技有限公司",// string	销售方名称
//                "yfp_hm" => "69023573",// string	原发票号码
//                "bz" => "对应正数发票代码:050003521270 号码:69023573备注啊哈哈哈哈",// string	备注
//                "xsf_yhzh" => "华夏银行股份有限公司西安分行营业部11450000000871357",// string	销售方银行账号
//                "gmf_mc" => "西安卓彩广告装饰有限公司",// string	购买方名称
//                "hjse" => "-0.03",// string	合计税额
//                "fp_dm" => "050003521270",// string	发票代码
//                "kce" => "",// string	扣除额
//                "yfp_dm" => "050003521270",// string	原发票代码
//                "fp_hm" => "69023595",// string	发票号码
//                "common_fpkj_xmxx" => [// object	商品明细
//                    [
//                        "fphxz" => "0",// string	发票行性质
//                        "spbm" => "3070201020000000000",// string	商品编码
//                        "zxbm" => "",// string	自行编码
//                        "yhzcbs" => "0",// string	优惠政策标识
//                        "lslbs" => "",// string	零税率标识
//                        "zzstsgl" => "",// string	增值税特殊管理
//                        "xmmc" => "*非学历教育服务**非学历教育服务*培训费",// string	项目名称
//                        "ggxh" => "",// string	规格型号
//                        "dw" => "",// string	计量单位
//                        "xmsl" => "-1",// string	项目数量
//                        "xmdj" => "0.970873786407767",// string	项目单价(不含税)
//                        "xmje" => "-0.97",// string	项目金额(不含税)
//                        "sl" => "0.03",// string	税率
//                        "se" => "-0.03"// string	税额
//                    ]
//                ],
//                "gmf_nsrsbh" => "610113399802848",// string	购买方纳税人识别号
//                "itype" => "026",// string	发票类型(026=电票,004=专票,007=普票，025=卷票)
//                "jff_phone" => "",// aaaa
//                "jym" => "16472248441090669492",// string	校验码
//                "kplx" => "1",// string	开票类型 0-蓝字发票；1-红字发票
//                "pdf_item_key" => "pdf_detail_0wm7HKH1609325340646",// string	发票清单PDF文件获取key
//                "order_num" => "order_num_1474873042899",// string	业务单据号
//                "kpzdbs" => "",// aaaa
//                "zsfs" => "0",// string	征税方式 0：普通征税 2：差额征税
//                "xsf_dzdh" => "西安市雁塔区融鑫路0号丽湾蓝岛第1幢3单元26层32606号15829686962",// string	销售方地址、电话
//                "jqbh" => "499111004317",// string	税控设备机器编号
//                "hjje" => "-0.97",// string	合计金额(不含税)
//                "ext_code" => "il5wz01m0l",// string	提取码
//                "tspz" => "00",// aaaa
//                "gmf_dzdh" => "",// string	购买方地址、电话
//                "fpqqlsh" => "0wm7HKH1609325340646",// string	发票请求流水号
//                "skr" => "收款人B",// string	收款人
//                "jff_email" => "",// aaaa
//                "gmf_yhzh" => "",// string	购买方银行账号
//                "kpr" => "开票员A",// string	开票人
//                "xsf_nsrsbh" => "91320106598035469W",// string	销售方纳税人识别号
//                // string	发票密文
//                "fp_mw" => "03110>0*31/750275<>13<9<74617996<+>>*/2+2<17/+4>8568263>+6**--853<>*39937>955*2*4782213217<98/116479191/461>0*9-",
//                "jshj" => "-1.00",// string	价税合计
//                "pdf_key" => "pdf_0wm7HKH1609325340646",// string	发票PDF文件获取key
//                "kprq" => "20201230174214"// tring	开票日期(20161107145525格式：yyyymmddhhmiss)
//            ]
//        ];
        return $result;

    }

    // **********发票查询*************开始********************************************************
    /**
     * C0001-平台在线交付----会发电子邮件
     *  获取发票PDF下载地址
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param string $order_num 订单号
     * @param string $nsrsbh 开票商户纳税人识别号
     * @param string $jff_phone 交付方手机号码
     * @param string $jff_email 交付方电子邮件--会发电子邮件
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleNewInvoiceDelay($openid, $app_secret, $order_num = '', $nsrsbh = '', $jff_phone = '', $jff_email = '', $apiDataMode = 0, $forceApi = false)
    {
        // $companyConfig = static::$companyConfig;
        $data = [
            "nsrsbh" => $nsrsbh,// $companyConfig['tax_num'],//"51019806696139X",// 是	string	20	开发票方纳税人识别号
            "order_num" => $order_num,// "1120521299480004",//"order_num_web1479345909208",// 是	string	200	发票业务单据号
            "jff_phone" => $jff_phone,// "15829686962",// "1800000000",// 是	string	11	交付方手机号码
            "jff_email" => $jff_email,// "305463219@qq.com",// "1800000000@qq.com"// 否	string	200	交付方电子邮件--会发电子邮件
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_newInvoiceDelay', $data, $apiDataMode, $forceApi);
        // [
        // String	加密后的数据,可以使用 http://web.hydzfp.com/html5/getinvoice/getinvoice.html?data=$result 参数就是返回的result给客户
        //  "result" => "eyJuc3JzYmgiOiI5MTMyMDEwNjU5ODAzNTQ2OVciLCJvcmRlcl9udW0iOiIxMTIwNTIxMjk5NDgwMDA0IiwidGltZSI6IjE2MDkyNDExOTIwOTQiLCJjayI6ImYyNDM0OWNmMjNiYmMzZDJmYTFhNjc2ODZhOTc4YmI0In0='
        // ]
        return $result;

    }
    // **********发票查询*************结束********************************************************
    // **********发票交付*************开始********************************************************
    /**
     * B0002-查单个发票
     *  查单个发票
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleQueryInvoice($openid, $app_secret, $apiDataMode = 0, $forceApi = false)
    {
        $companyConfig = static::$companyConfig;
        $data = [
            "nsrsbh" => $companyConfig['tax_num'],//"51019806696139X",// 是	string	20	开发票方纳税人识别号
            "order_num" => "1120505471010040",// "1120521299480004",//"order_num_web1479345909208",// 是	string	200	发票业务单据号
//            "fpqqlsh" => "18200133031",// 否	string	20	发票请求流水号
//            "fp_dm" => "18200133031",// 否	string	12	发票代码
//            "fp_hm" => "18200133031",// 否	string	8	发票号码
//            "jym" => "18200133031"// 否	string	20	发票校验码
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_queryInvoice', $data, $apiDataMode, $forceApi);
        /**
         *
        $result = [
            "JYM" => "02581197460769559807",// string	校验码
            "GMF_MC" => "个人",// string	购买方名称
            "XSF_DZDH" => "自贡市斯柯达将阿里是可大家是大家圣诞节阿拉斯加大开杀戒的拉开手机端13132254",// string	销售方电话地址
            "XSF_NSRSBH" => "91320106598035469W",// string	销售方纳税人识别号
            "ORDER_NUM" => "1120521299480004",// string	业务单据号
            "ZSFS" => "0",// string	征收方式 0：普通征税 1: 减按计增 2：差额征税
            "JFF_PHONE" => "15829686962",// aaaaa
            "PDF_ITEM_KEY" => "",// string	清单PDFkey
            "JSHJ" => "1",// string	价税合计
            "common_fpkj_xmxx" => [// object	商品明细
                [
                    "SE" => "0.03",// string	税额
                    "ORDER_NUM" => "1120521299480004",// string	业务单据号
                    "FPQQLSH" => "OZwKHou1609236806278",// string	发票请求流水号
                    "SL" => "0.03",// string	税率
                    "GGXH" => "",// string	规格型号
                    "ZZSTSGL" => "",// string	增值税特殊管理
                    "XMSL" => "1",// string	数量
                    "LSLBS" => "",// string	零税率标识 空：非零税率， 1：免税，2：不征收，3普通零税率
                    "DW" => "",// string	计量单位
                    "FPHXZ" => "0",// string	发票行性质 0正常行、1折扣行、2被折扣行
                    "SPBM" => "3070201020000000000",// string	商品编码
                    "KPRQ" => "2020-12-29 17:06:44",// string	开票日期
                    "XMDJ" => "0.970873786407767",// string	项目单价
                    "XMMC" => "*非学历教育服务*培训费",// string	项目名称
                    "XMJE" => "0.97",// string	项目金额
                    "ID" => "63546159",// aaaaa
                    "YHZCBS" => "0",// string	优惠政策标识 0：不使用，1：使用
                    "ZXBM" => ""// string	自行编码
                ]
            ],
            "BZ" => "备注啊哈哈哈哈",// string	备注
            "KPRQ" => "2020-12-29 17:06:44",// string	开票日期
            "GMF_NSRSBH" => "",// string	购买方纳税人识别号
            "JQBH" => "499111004317",// string	机器编号
            "EXT_CODE" => "2fahsn2gbk",// string	提取码
            "DATA_RESOURCES" => "API",// string	数据来源
            "ID" => "35541211",// aaaaa
            "JFF_EMAIL" => "305463219@qq.com",// aaaaa
            // string	发票税控码
            "FP_MW" => "03/>29631>055*55<3682-05<800<61+4>>6587*764494-/-0<143317-/>29631>055*55<3318900373638/4-173-*01247919274//-6>0-",
            "KPLX" => "0",// string	开票类型0-蓝字发票；1-红字发票
            "KPR" => "开票员A",// string	开票人
            "SKR" => "",// string	收款人
            "KCE" => "",// string	扣除额
            "FHR" => "",// string	复核人
            "XSF_YHZH" => "124654654123154",// string	销售方银行账号
            "FPQQLSH" => "OZwKHou1609236806278",// string	发票请求流水号
            "FP_HM" => "69023540",// string	发票号码
            "FP_DM" => "050003521270",// string	发票代码
            "ITYPE" => "026",// string	发票类型(026=电票,004=专票,007=普票，025=卷票)
            "GMF_DZDH" => "",// string	购买方地址电话
            "KPZDBS" => "",// string	开票终端标示
            "XSF_MC" => "江苏百旺金赋信息科技有限公司",// string	销售方名称
            "HJJE" => "0.97",// string	合计金额
            "TSPZ" => "00",// STRING	“00”正常，“01”农产品销售，“02”农产品收购
            "PDF_KEY" => "pdf_OZwKHou1609236806278",// string	发票pdfkey
            "YFP_DM" => "",// string	原发票代码
            "HJSE" => "0.03",// string	合计税额
            "YFP_HM" => "",// string	原发票号码
            "GMF_YHZH" => ""// string	购买方银行账号
        ];
         *
         */
        return $result;

    }

    /**
     * B0003-查单个发票(简易查询)
     *  查单个发票(简易查询)
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleQueryInvoice4DMDH($openid, $app_secret, $apiDataMode = 0, $forceApi = false)
    {
        $companyConfig = static::$companyConfig;
        $data = [
            "fp_dm" => "050003521270",// 是	string	12	发票代码
            "fp_hm" => "69023540",// 是	string	8	发票号码
            // "jym" => "02581197460769559807"// 否	string	20	校验码
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_queryInvoice4DMDH', $data, $apiDataMode, $forceApi);
        /**
         *
        $result = [
            "JYM" => "02581197460769559807",// STRING	校验码
            "GMF_MC" => "个人",// STRING	购买方名称
            "XSF_DZDH" => "自贡市斯柯达将阿里是可大家是大家圣诞节阿拉斯加大开杀戒的拉开手机端13132254",// STRING	销售方地址、电话
            "XSF_NSRSBH" => "91320106598035469W",// STRING	销售方纳税人识别号
            "ORDER_NUM" => "1120521299480004",// STRING	业务单据号
            "ZSFS" => "0",// STRING	征税方式 0：普通征税 2：差额征税
            "JFF_PHONE" => "15829686962",// aaaa
            "PDF_ITEM_KEY" => "",// STRING	发票清单PDF文件获取KEY
            "JSHJ" => "1",// STRING	价税合计
            "BZ" => "备注啊哈哈哈哈",// STRING	备注
            "KPRQ" => "2020-12-29 17:06:44",// STRING	开票日期(20161107145525格式：YYYYMMDDHHMISS)
            "GMF_NSRSBH" => "",// STRING	购买方纳税人识别号
            "JQBH" => "499111004317",// STRING	税控设备机器编号
            "EXT_CODE" => "2fahsn2gbk",// STRING	提取码
            "DATA_RESOURCES" => "API",// aaaa
            "ID" => "35541211",// aaaa
            "JFF_EMAIL" => "305463219@qq.com",// aaaa
            // STRING	发票密文
            "FP_MW" => "03/>29631>055*55<3682-05<800<61+4>>6587*764494-/-0<143317-/>29631>055*55<3318900373638/4-173-*01247919274//-6>0-",
            "KPLX" => "0",// STRING	开票类型 0-蓝字发票；1-红字发票
            "PDF_URL" => "999",// aaaa
            "KPR" => "开票员A",// STRING	开票人
            "SKR" => "",// STRING	收款人
            "KCE" => "",// STRING	扣除额
            "FHR" => "",// STRING	复核人
            "XSF_YHZH" => "124654654123154",// STRING	销售方银行账号
            "FPQQLSH" => "OZwKHou1609236806278",// STRING	发票请求流水号
            "FP_HM" => "69023540",// STRING	发票号码
            "FP_DM" => "050003521270",// STRING	发票代码
            "ITYPE" => "026",// STRING	发票类型(026=电票,004=专票,007=普票，025=卷票)
            "GMF_DZDH" => "",// STRING	购买方地址、电话
            "KPZDBS" => "",// STRING	开票终端标示
            "XSF_MC" => "江苏百旺金赋信息科技有限公司",// STRING	销售方名称
            "HJJE" => "0.97",// STRING	合计金额
            "TSPZ" => "00",// STRING	“00”正常，“01”农产品销售，“02”农产品收购
            "PDF_KEY" => "pdf_OZwKHou1609236806278",// STRING	发票PDF文件获取KEY
            "YFP_DM" => "",// STRING	原发票代码
            "HJSE" => "0.03",// STRING	合计税额
            "PDF_ITEM_URL" => "999",// aaaa
            "YFP_HM" => "",// STRING	原发票号码
            "GMF_YHZH" => ""// STRING	购买方银行账号
        ];
         *
         */
        return $result;

    }

    /**
     * B0004-税控盒子开票状态查询
     *  税控盒子查询发票开票状态
     * 调用频率请勿过快，间隔请勿小于5s/次
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleGetInvoiceStatus($openid, $app_secret, $apiDataMode = 0, $forceApi = false)
    {
        $companyConfig = static::$companyConfig;
        $data = [
            "nsrsbh" => $companyConfig['tax_num'],//"500102010003540",// 是	string	50	税号
            "order_num" => "1120505471010040",// "1120521299480004"// 是	string	100	业务系统订单编号
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_getInvoiceStatus', $data, $apiDataMode, $forceApi);
        /**
         *
        $result = [
            "CODE" => "1",// STRING	是否成功：-1：正在开票，请等待；1-开票成功；其他的都是失败
            "MSG" => "开票成功",// STRING	失败原因，如果是成功，则提示开票成功
            "NSRSBH" => "91320106598035469W",
            "ORDER_NUM" => "1120521299480004",
            "INVOICE" => [
                    "JYM" => "02581197460769559807",
                "GMF_MC" => "个人",
                "XSF_DZDH" => "自贡市斯柯达将阿里是可大家是大家圣诞节阿拉斯加大开杀戒的拉开手机端13132254",
                "XSF_NSRSBH" => "91320106598035469W",
                "ORDER_NUM" => "1120521299480004",
                "ZSFS" => "0",
                "JFF_PHONE" => "15829686962",
                "PDF_ITEM_KEY" => "",
                "JSHJ" => "1",
                "common_fpkj_xmxx" => [
                    [
                        "SE" => "0.03",
                        "ORDER_NUM" => "1120521299480004",
                        "FPQQLSH" => "OZwKHou1609236806278",
                        "SL" => "0.03",
                        "GGXH" => "",
                        "ZZSTSGL" => "",
                        "XMSL" => "1",
                        "LSLBS" => "",
                        "DW" => "",
                        "FPHXZ" => "0",
                        "SPBM" => "3070201020000000000",
                        "KPRQ" => "2020-12-29 17:06:44",
                        "XMDJ" => "0.970873786407767",
                        "XMMC" => "*非学历教育服务*培训费",
                        "XMJE" => "0.97",
                        "ID" => "63546159",
                        "YHZCBS" => "0",
                        "ZXBM" => ""
                    ]
                ],
                "BZ" => "备注啊哈哈哈哈",
                "KPRQ" => "2020-12-29 17:06:44",
                "GMF_NSRSBH" => "",
                "JQBH" => "499111004317",
                "EXT_CODE" => "2fahsn2gbk",
                "DATA_RESOURCES" => "API",
                "ID" => "35541211",
                "JFF_EMAIL" => "305463219@qq.com",
                "FP_MW" => "03/>29631>055*55<3682-05<800<61+4>>6587*764494-/-0<143317-/>29631>055*55<3318900373638/4-173-*01247919274//-6>0-",
                "KPLX" => "0",
                "KPR" => "开票员A",
                "SKR" => "",
                "KCE" => "",
                "FHR" => "",
                "XSF_YHZH" => "124654654123154",
                "FPQQLSH" => "OZwKHou1609236806278",
                "FP_HM" => "69023540",
                "FP_DM" => "050003521270",
                "ITYPE" => "026",
                "GMF_DZDH" => "",
                "KPZDBS" => "",
                "XSF_MC" => "江苏百旺金赋信息科技有限公司",
                "HJJE" => "0.97",
                "TSPZ" => "00",
                "PDF_KEY" => "pdf_OZwKHou1609236806278",
                "YFP_DM" => "",
                "HJSE" => "0.03",
                "YFP_HM" => "",
                "GMF_YHZH" => ""
            ]
        ];
         *
         */
        return $result;

    }


    /**
     * C0002-获取平台交付二维码
     *  获取交付发票的二维码图片；
     * 该接口返回的二维码为图片base64数据(qrcode)，需开发者自行转换为图片显示即可，转换后的图片可直接使用
     * 该接口返回的交付链接地址(qrcodeurl)是可直接使用的交付地址(与二维码扫码后的结果一致)；
     * 注意： 如在html中使用img标签，请加入base64头信息(data:image/png;base64,)：例如：
     * <img src="data:image/png;base64,/9jxxxxxx" style="width:200px;height:200px;">
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param string $order_num 订单号
     * @param string $nsrsbh 开票商户纳税人识别号
     * @param string $isimg isimg	否	string	10	是否需要二维码图片数据(如需要则值为“qrcode”，不需要则为空 );当值不为“qrcode”时，接口不返回 qrcode 数据
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleGetInvoiceDelayQRCode($openid, $app_secret, $order_num = '', $nsrsbh = '', $isimg = 'qrcode', $apiDataMode = 0, $forceApi = false)
    {
        // $companyConfig = static::$companyConfig;
        $data = [
            "nsrsbh" => $nsrsbh, // $companyConfig['tax_num'],//"51019806696139X",// 是	string	20	开发票方纳税人识别号
            "order_num" => $order_num ,// "1120521299480004",//"order_num_web1479345909208",// 是	string	200	发票业务单据号
            "isimg" => $isimg,// "qrcode"// isimg	否	string	10	是否需要二维码图片数据(如需要则值为“qrcode”，不需要则为空 );当值不为“qrcode”时，接口不返回 qrcode 数据
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_getInvoiceDelayQRCode', $data, $apiDataMode, $forceApi);
//         [
//             // qrcodeurl	String	用于交付发票的链接地址
//            "qrcode" => "FABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB//9k=",
//             // qrcode	String	二维码的base64数据,可直接转换为图片(二维码)使用,与codeurl内容一致 ;PS:内容可能相对比较大;建议开发者使用qrcodeurl自行转为二维码图片
//            "qrcodeurl" => "http://web.hydzfp.com/html5/getinvoice/getinvoice.html?data=eyJuciNyJ9"
//         ]
        return $result;

    }

    /**
     * C0003-获取预览用PDF图片
     *  获取PDF文件在移动端或其他终端预览时使用的pdf文件base64形式的数据,base64数据转换文件后的类型为图片类型(不包含电子签名),
     *  因此不能作为交付给最终用户的电子发票；交付给最终用户的电子发票因使用其他接口（获取原始的PDFbase64数据）获取；
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function epPdfGetPdfImgByte($openid, $app_secret, $apiDataMode = 0, $forceApi = false)
    {
        $companyConfig = static::$companyConfig;
        $data = [
            "fileKey" => "pdf_OZwKHou1609236806278",// "pdf_510302744676556",// 是	string	200	PDF文件的filekey ,由发票开具接口返回的pdf_key(发票)或pdf_item_key(如果有清单)
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ep_pdf_getPdfImgByte', $data, $apiDataMode, $forceApi);
        // ["url" => "PDF文件转换为图片后的base64数据"]
        return $result;

    }

    /**
     * C0004-获取原始PDF文件
     *  获取PDF文件的base64形式的数据,base64数据转换文件后的类型为电子签名后的pdf文件,可以作为交付最终用户的电子发票；
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function epPdfGetPdfByte($openid, $app_secret, $apiDataMode = 0, $forceApi = false)
    {
        $companyConfig = static::$companyConfig;
        $data = [
            "fileKey" => "pdf_OZwKHou1609236806278",// "pdf_510302744676556",// 是	string	200	PDF文件的filekey ,由发票开具接口返回的pdf_key(发票)或pdf_item_key(如果有清单)
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ep_pdf_getPdfByte', $data, $apiDataMode, $forceApi);
        // ["url" => "原始PDF文件的base64数据"]
        return $result;

    }

    /**
     * C0005-获取发票PDF下载地址
     *  获取发票PDF下载地址
     * @param  array $data 查询用的数组
     *    $data = [
     *        "nsrsbh" => $companyConfig['tax_num'],//"开票商户纳税人识别号",// 是	string	20	开票商户纳税人识别号
     *       "order_num" => "1120521299480004",//"业务单据号"// 是	string	200	业务单据号
     *   ];
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiInvoiceHandleGetInvoiceDownloadUrl($data, $openid, $app_secret, $apiDataMode = 0, $forceApi = false)
    {
        $companyConfig = static::$companyConfig;
//        $data = [
//            "nsrsbh" => $companyConfig['tax_num'],//"开票商户纳税人识别号",// 是	string	20	开票商户纳税人识别号
//            "order_num" => "1120521299480004",//"业务单据号"// 是	string	200	业务单据号
//        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_InvoiceHandle_getInvoiceDownloadUrl', $data, $apiDataMode, $forceApi);
        // ["fp_url" => "String	发票下载url，GET方式", 'qd_url' => 'String	清单下载url，没有清单字段为空']
        return $result;

    }

    // **********发票交付*************结束********************************************************
    // **********微信卡包*************开始********************************************************

    /**
     * WX_CARD_001-获取授权链接
     *  获取微信(票据)的用户领取卡包获取授权页的链接，在微信中向用户展示授权页，当用户点击了授权页上的“领取发票”按钮后，即完成了订单号与该用户的授权关系绑定，
     *  后续平台自动将此订单号发起将发票卡券插入用户卡包的请求，微信也将据此授权关系校验是否放行插卡请求。
     *  获取前发票必须开出,并且已经上传到平台中(如果是服务器开票,则无需上传)
     *  每张发票对应的微信卡卷只能被一个微信用户领取，如已经有用户领取,则无法授权;
     *  该授权页链接需要在微信客户端中打开，并且需要微信用户点击领取发票,授权才能完成;
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function piaojuHydzfpCardGetAtuhurl($openid, $app_secret, $apiDataMode = 0, $forceApi = false)
    {
        $companyConfig = static::$companyConfig;
        $data = [
            "nsrsbh" => $companyConfig['tax_num'],//是	string	20	发票的销售方纳税人识别号(必须和access_token对应的税号相同)
            "fp_dm" => "050003521270",// 是	string	12	发票代码
            "fp_hm" => "69023540"// 是	string	8	发票号码
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'piaoju_hydzfp_card_getAtuhurl', $data, $apiDataMode, $forceApi);
        // String	微信卡卷的授权链接
        // ["url" => "https://mp.weixin.qq.com/bizmall/authinvoice?action=list&s_pappid=d3gxZTk2MzY3NGIxMGQ4ZDg0X5Q8DCqGd5fEicMUHLpZBisDInUBOQ0bTgrAH52SaWGT&appid=wx1e963674b10d8d84&num=1&o1=1120521299480004_91320106598035469W&m1=100&t1=1609296645&source=web&type=2&signature=b399884d29d6de11be829a240f673b341cf686a3#wechat_redirect']
        return $result;

    }
    // **********微信卡包*************结束********************************************************
    // **********合作商户*************开始********************************************************

    /**
     * 合作商户-查单个发票(完整信息)
     *  查单个发票
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function ebiCmQueryInvoice($openid, $app_secret, $apiDataMode = 0, $forceApi = false)
    {
        $companyConfig = static::$companyConfig;
        $data = [
            "fp_dm" => "050003521270",// 是	string	12	发票代码
            "fp_hm" => "69023540"// 是	string	8	发票号码
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'ebi_cm_queryInvoice', $data, $apiDataMode, $forceApi);
        // 沪友接口错误:ebi_cm_queryInvoice 对应的服务未找到,或没有开通该服务!
        return $result;

    }
    // **********合作商户*************结束********************************************************
    // **********离线开票(扫码开票)*************开始********************************************************

    /**
     * E0001-获取离线开票authid
     *
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  string  $xsf_nsrsbh 销售方纳税人识别号
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function getAuthidConfig($openid, $app_secret, $xsf_nsrsbh, $apiDataMode = 0, $forceApi = false){
        $accessTokenConfig = APIHYDZFPRequest::getAuthid($openid, $app_secret, $xsf_nsrsbh, $apiDataMode, $forceApi);
        return $accessTokenConfig;
    }

    /**
     * E0002设置二维码有效期
     * 设置离线开具发票的二维码有效期限，调用后永久生效；您也可以直接登录http://web.hydzfp.com设置二维码有效期；
     * 该接口处理过期二维码数据会在过期之后1个小时左右处理完成；包括删除或者开具
     * 若选用的处理方式为二维码失效后开具；则自动开具发票默认抬头为“个人”;
     * 该接口连续调用次数有限制，请合理安排。
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  string  $xsf_nsrsbh 销售方纳税人识别号
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function offlineAuthIdSetQRTerm($openid, $app_secret, $xsf_nsrsbh, $apiDataMode = 0, $forceApi = false){
        $companyConfig = static::$companyConfig;
        $authidConfig = APIHYDZFPRequest::getAuthid($openid, $app_secret, $xsf_nsrsbh, $apiDataMode, $forceApi);
        $data = [
            "nsrsbh" => $xsf_nsrsbh,// "510302744676556",// 是	string	50	纳税人识别号
            "authid" => $authidConfig['authid'],// "29c3d5xxxxf1646db28e5769e4",// 是	string	256	授权authid
            "expires_in" => 15*24*60*60,// "1200",// 是	string	-	二维码有效时间戳(建议最大值15天)，精确到秒；例如1天则该参数为：1*24*60*60
            "expires_do_type" => "0",// 是	string	2	二维码过期之后的处理方式。0：删除；1：开具；默认不处理该数据。
            "sksblx" => "0"// 是	string	2	税控设备类型。0：税控盘；1：税控服务器；2：税控盒子
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'offline_authId_setQRTerm', $data, $apiDataMode, $forceApi);
        // 成功，返回空数组
        return $result;
    }

    /**
     * E0003-上传待开发票数据
     * 将需要待开的发票数据上传；以提供后续的扫码开具; 变更说明：
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  string  $xsf_nsrsbh 销售方纳税人识别号
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function offlineOrigUploadData($openid, $app_secret, $xsf_nsrsbh, $apiDataMode = 0, $forceApi = false){
        $companyConfig = static::$companyConfig;
        $authidConfig = APIHYDZFPRequest::getAuthid($openid, $app_secret, $xsf_nsrsbh, $apiDataMode, $forceApi);
        $data = [
            "authid" => $authidConfig['authid'],// "29c3d5xxxxf1646db28e5769e4",// 是	string	256	授权authid
            "fplx" => "026",// 是	string	3	发票类型026-电子发票005-机动车发票004-专用发票025-卷式发票007-普通发票(2019-03-29)
            // "ipaddr" => "",// 是	string	64	客户端IP地址,由对外的交易接收方直接获取(2019-03-29)
            "order_num" => "1120505471010040",// "1120512670520001",// "order_num_600000000",// 是	string	30	发票业务单据号
            "bmb_bbh" => "13.0",// 是	string		编码版本号；默认13.0；此字段依照税务机关标准
            "zsfs" => "0",// 是	string	1	征税方式 0：普通征税 1: 减按计增 2：差额征税
            "tspz" => "00",// 否	string	2	特殊票种标识:“00”=正常票种,“01”=农产品销售,“02”=农产品收购
            "xsf_nsrsbh" => $companyConfig['tax_num'],// "510302744676556",// 是	string	20	销货方纳税人识别号
            "xsf_mc" => $companyConfig['pay_company_name'],// "xxx商业银行股份有限公司",// 是	string	100	销售方名称
            "xsf_dzdh" => "上海市某某路0232345678",// "11111",// 是	string	100	销售方地址电话
            "xsf_yhzh" => "环球银行123456",// "33333",// 是	string	100	销售方银行及账号
            "gmf_nsrsbh" => "610113399802848",// "510302744676556",// 否	string	20	购买方纳税人识别号
            "gmf_mc" => "西安卓彩广告装饰有限公司",// "xxx商业银行股份有限公司",// 否	string	100	购买方名称
            "gmf_dzdh" => "西安市雁塔区融鑫路0号丽湾蓝岛第1幢3单元26层32606号15829686962",// 否	string	100	购买方地址电话
            "gmf_yhzh" => "华夏银行股份有限公司西安分行营业部11450000000871357",// 否	string	100	购买方银行及帐号
            "kpr" => "开票员",// 是	string	10	开票人
            "skr" => "收款人",// 否	string	10	收款人
            "fhr" => "复核人",// 否	string	10	复核人
            "jshj" => "1.00",// 是	string	#.##	价税合计
            "hjse" => "0.03",// 是	string	#.##	合计税额
            "hjje" => "0.97",// 是	string	#.##	合计金额
            "kce" => "",// 否	string	#.##	扣除额
            "bz" => "备注",// 否	string	160	备注：最长160个字符
            // "kpzdbs" => "",// 否	string	20	开票终端标识
            "notice_url" => APIHYDZFPRequest::getAPIConfigByKey('notifyUrl'),// "开票成功后的回调url",// 否	string	256	开票成功后的回调url 2018-11-26新增
            "jff_phone" => "15829686962", // "13800000000",// 否	string	256	手机号 2018-11-26新增
            "jff_email" => "305463219@qq.com",// "acbc@hydzfp.com",// 否	string	256	电子邮箱 2018-11-26新增
            "items" => [// 是	JSONobject		商品明细
                [
                    "fphxz" => "0",// 是	string	1	0正常行、1折扣行、2被折扣行
                    "spbm" => "3070201020000000000",// "",// 是	string	19	商品编码为税务总局颁发的19位税控编码
                    "zxbm" => "",// 否	string	20	自行编码(一般不建议使用自行编码)
                    "yhzcbs" => "0",// 是	string	1	优惠政策标识，0：不使用，1：使用
                    "lslbs" => "",// 否	string	1	零税率标识，空：非零税率， 1：免税，2：不征收，3普通零税率
                    "zzstsgl" => "",// 否	string	50	增值税特殊管理-如果yhzcbs为1时，此项必填
                    // 是	string	90	项目名称 (必须与商品编码表一致;如果为折扣行，商品名称须与被折扣行的商品名称相同，不能多行折扣。
                    // 如需按照税控编码开票，则项目名称可以自拟,但请按照税务总局税控编码规则拟定)
                    "xmmc" => "*非学历教育服务*培训费",// "商品A",// aaaa
                    "ggxh" => "",// 否	string	30	规格型号(折扣行请不要传)
                    "dw" => "",// 否	string	20	计量单位(折扣行请不要传)
                    "xmsl" => "1",// 否	string	#.######	商品数量，小数点后6位,大于0的数字
                    "xmdj" => "1.00",// "100.000000",// 否	string	#.######	单价是含税，小数点后6位 注意：单价是含税单价,大于0的数字
                    "xmje" => "1.00",// "100.00",// 是	string	#.##	金额是含税，单位：元（2位小数）
                    "sl" => "0.03",// "0.06",// 是	string	#.##	税率 6位小数，例1%为0.01
                    "se" => "0.03",// "5.66"// 是	string	#.##	税额单位：元（2位小数）
                ]
            ]
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'offline_orig_uploadData', $data, $apiDataMode, $forceApi);
        // ewm_url	否	String	256 二维码规则(订单上传成功后由后台生成的二维码规则)2018-11-26新增
        // $result = ['ewm_url' => 'https://www.hydzfp.com/piaoju/auth/authIn.do?invoke=qRScan&order_num=1120512670520001&nsrsbh=91320106598035469W&sksblx=1'];
        return $result;
    }

    /**
     * E0004-客户端获取某订单信息---此接口用不了
     * 根据纳税人识别代码和业务订单号获取某个订单的开具结果
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  string  $xsf_nsrsbh 销售方纳税人识别号
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function offlineOrigGetData($openid, $app_secret, $xsf_nsrsbh, $apiDataMode = 0, $forceApi = false){
        $companyConfig = static::$companyConfig;
        $authidConfig = APIHYDZFPRequest::getAuthid($openid, $app_secret, $xsf_nsrsbh, $apiDataMode, $forceApi);
        $data = [
            "authid" => $authidConfig['authid'],// "29c3d5xxxxf1646db28e5769e4",// 是	string	256	授权authid
            "clientid" => "112233",// "",// 否	string	200	客户端唯一识别号
            "phone" => "",// 否	string	20	手机号码
            "nsrsbh" => $xsf_nsrsbh,// "12345678910",// 是	string	20	纳税人识别号
            "order_num" => "1120505471010040",// "1120512670520001",// "order123456"// 是	string	30	业务单据号 "1120521299480004",//
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'offline_orig_getData', $data, $apiDataMode, $forceApi);
        return $result;
    }

    /**
     * E0005-客户端绑定待开发票数据
     * 客户端调用接口，直接绑定对应待开发票信息
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  string  $xsf_nsrsbh 销售方纳税人识别号
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function offlineOrigBindData($openid, $app_secret, $xsf_nsrsbh, $apiDataMode = 0, $forceApi = false){
        $companyConfig = static::$companyConfig;
        $authidConfig = APIHYDZFPRequest::getAuthid($openid, $app_secret, $xsf_nsrsbh, $apiDataMode, $forceApi);
        $data = [
            "authid" => $authidConfig['authid'],// "29c3d5xxxxf1646db28e5769e4",// 是	string	256	授权authid
            "clientid" => "112233",// 否	string	200	客户端唯一识别号
            "phone" => "",// 否	string	20	手机号码
            "nsrsbh" => $xsf_nsrsbh,// "12345678910",// 是	string	20	纳税人识别号
            "order_num" => "1120505471010040",// "order123456"// 是	string	30	业务单据号 "1120521299480004",//
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'offline_orig_bindData', $data, $apiDataMode, $forceApi);
//        {
//            "fhr": "复核人",
//    "xsf_mc": "江苏百旺金赋信息科技有限公司",
//    "bz": "备注",
//    "gmf_mc": "西安卓彩广告装饰有限公司",
//    "xsf_yhzh": "环球银行123456",
//    "hjse": "0.03",
//    "kce": "",
//    "type": "026",
//    "bmb_bbh": "13.0",
//    "gmf_nsrsbh": "610113399802848",
//    "orig_id": "12020123023113607520000055905034",
//    "createtime": "2020-12-30 23:12:05",
//    "order_num": "1120505471010040",
//    "zsfs": "0",
//    "kpzdbs": "",
//    "xsf_dzdh": "上海市某某路0232345678",
//    "hjje": "0.97",
//    "tspz": "00",
//    "gmf_dzdh": "西安市雁塔区融鑫路0号丽湾蓝岛第1幢3单元26层32606号15829686962",
//    "status": "0",
//    "skr": "收款人",
//    "gmf_yhzh": "华夏银行股份有限公司西安分行营业部11450000000871357",
//    "kpr": "开票员",
//    "xsf_nsrsbh": "91320106598035469W",
//    "items": [
//        {
//            "sl": "0.03",
//            "xh": "1",
//            "dw": "",
//            "xmdj": "1",
//            "lslbs": "",
//            "xmje": "1",
//            "xmmc": "*非学历教育服务*培训费",
//            "zxbm": "",
//            "yhzcbs": "0",
//            "se": "0.03",
//            "zzstsgl": "",
//            "orig_id": "12020123023113607520000055905034",
//            "id": "22020123023120505050000086548900",
//            "fphxz": "0",
//            "ggxh": "",
//            "xmsl": "1",
//            "spbm": "3070201020000000000"
//        }
//    ],
//    "jshj": "1"
//}
        return $result;
    }


    /**
     * E0006-客户端获取已绑定数据列表
     * 客户端获取已绑定的数据列表
     * @param  string $openid 应用OPENID
     * @param  string  $app_secret 应用密匙
     * @param  string  $xsf_nsrsbh 销售方纳税人识别号
     * @param  int  $apiDataMode 业务请求数据的方式 0 使用配置文件配置的 [默认]；1：通用 ；2  base64数据
     * @param  boolean  $forceApi 是否强制从api重新获取 true:强制重新获取， false:缓存优先[默认]
     * @return array 一维数组
     *  ["expire_in" => 7200, 'access_token' => 'FfJPQjgxQGa2y0Snuuc4Q94iQpce3A6x']
     */
    public static function offlineOrigList($openid, $app_secret, $xsf_nsrsbh, $apiDataMode = 0, $forceApi = false){
        $companyConfig = static::$companyConfig;
        $authidConfig = APIHYDZFPRequest::getAuthid($openid, $app_secret, $xsf_nsrsbh, $apiDataMode, $forceApi);
        $data = [
            "authid" => $authidConfig['authid'],// "29c3d5xxxxf1646db28e5769e4",// 是	string	256	授权authid
            "clientid" => "112233",// 否	string	200	客户端唯一识别号
            "phone" => "15829686962",// 否	string	20	手机号码
            "status"=>  "0",// 是	string	1	开票状态（0=未开，1=已开）
        ];
        $result = APIHYDZFPRequest::getAPI($openid, $app_secret, 'offline_orig_list', $data, $apiDataMode, $forceApi);
        // "沪友接口错误:offline_orig_list 对应的服务未找到,或没有开通该服务!"
        return $result;
    }
    // **********离线开票(扫码开票)*************结束********************************************************
}
