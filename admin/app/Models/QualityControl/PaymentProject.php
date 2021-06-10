<?php
// 付款/收款项目

namespace App\Models\QualityControl;

use App\Services\Tool;

class PaymentProject extends BasePublicModel
{
    //****************数据据缓存**相关的***开始********************************************
//    public static $cachePre = 'cacheDB';// 缓存键最前面的关键字  cacheDb:U:{id值}_{email值}  中的 cacheDb
//    public static $separatoChar = '__';// 缓存相关的分隔符-主要是键;注意合法性，会作为redis键的一部分
//    public static $cacheTimeTableKey = 'Ttbl';// 缓存表更新时间时的缓存关键字
//    public static $cacheTimeBlockKey = 'Tblock';// 缓存块更新时间时的缓存关键字
//    public static $cacheTimeInfoKey = 'Tinfo';// 缓存表具体详情更新时间时的缓存关键字
//    public static $cacheInfoKey = 'info';// 缓存表具体详情数据的缓存关键字
//    public static $cachePrimaryValInfoKey = 'TpriVal';// 缓存表其它缓存字段缓存主键值的缓存关键字
//    public static $operateRedis = 2;// 操作 1 转为json 2 序列化 ; 3 不转换 ---最好用2 序列化，不然可能会有问题
//    public static $cacheExpire = 60 * 60 * 24 * 10;// 10 天 缓存的时间长度 ; 值<= 0时，会使用 public.DBDataCache.expire 配置

    // 1 缓存详情 2缓存块[确定没有用到关系的块，可以缓存]
    //  public.DBDataCache.cacheType 配置打开，且各模型也打开才会有对应缓存
    public static $cacheType = (1 | 2);// 0
    // 最大缓存数据行数，如果>此值的数据不缓存。; 值<= 0时，会使用 public.DBDataCache.maxCacheRows 配置
    public static $maxCacheRows = 0;

//    public static $cacheSimple = 'U';// 表名简写,为空，则使用表名

    public static $cacheVersion = 'V1';// 内容随意改[可0{空默认为0}开始自增]- 如果运行过程中，有直接对表记录进行修改，增加或修改字段名，则修改此值，使表记录的相关缓存过期。
    // $cacheExcludeFields 为空：则缓存所有字段值；排除字段可能是大小很大的字段，不适宜进行缓存
    public static $cacheExcludeFields = [];// 表字段中排除字段; 有值：要小心，如果想获取的字段有在排除字段中的，则不能使用缓存


//    public static $cachePrimaryFields = 'id';//格式 '字段a ' 或 一维数组 ['字段b','字段c',....] 为空，则通过 表的主键缓存，再没有就不缓存

    // 可作为单条记录缓存的字段 格式 ['e' => '字段a ', 'm' => ['字段b','字段c',....] 值需要作为缓存键的字段，缓存值为指向 id 字段
    // 多字段的数组为 层级关系，如：从左到右为 第一层[城市站缓存]、第二层[代理站缓存]、第三层[商家站缓存]、第四层[店铺站缓存]...
    public static $cachePrimaryKeyFields = [];

    // 此属性有值；则是多情况（多种平台应该；如按城市分站）缓存，为空：系统/公用类别的缓存
    // 块数据缓存时，需要标记缓存的字段 格式 ['e' => '字段a ', 'm' => ['字段b','字段c',....] 值需要作为缓存键的字段
    // 多字段的数组为 层级关系，如：从左到右为 第一层[城市站缓存]、第二层[代理站缓存]、第三层[商家站缓存]、第四层[店铺站缓存]...
    // 为空，则表级缓存块
    // 有新下标加入或字段变动，所有缓存会自动失效。删除下标：不会影响已有缓存
    public static $cacheBlockFields = [];

    // 单位时间内，访问多少次，开启缓存--目的去掉冷数据 如：1分钟访问2次，则开启缓存
    // 值[] 空时，会使用 public.DBDataCache.openCache 配置
//    public static $openCache = [
//        'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
//        'requestNum' => 3,// 访问次数
//    ];
    // 缓存自动延期设置 单位时间内访问多少次时，自动延长有效期 10分钟 8次 自动延长有效期 可延长3次
    // 值[] 空时，会使用 public.DBDataCache.extendExpire 配置
//    public static $extendExpire = [
//        'expire' => 60 * 3,// 单位时长，单位秒  建议：2-10分钟
//        'requestNum' => 8,// 访问次数
//        'maxExendNum' => 3,// 可延长3次
//    ];

    //****************数据据缓存**相关的***结束********************************************

     public static $IntPriceFields = ['pay_amount', 'payed_amount', 'refund_amount'];//[有则设置] 表中整型表示价格的字段数组 -- 一维数组，目的：方便统一把数据中的字段转浮点数或转整数
    // public static $IntPriceIndex = [];//  $IntPriceFields 字段对应的扩大陪数；没有配置默认为2  扩大或缩小倍数： pow(10,2)；格式：['sl' => 2,]；注：2的可不用配置【只配置特殊的】，因为默认就是2

    // 自有属性
    // 0：都没有；
    // 1：有历史表 ***_history;
    // 2：有操作员工id 字段 operate_staff_id
    // 4：有操作员工历史id 字段 operate_staff_id_history
    // 8：有操作日期字段 created_at timestamp
    // 16：有更新日期字段 updated_at  timestamp
    // 32: 有历史表 ***_history; 且 此表实时记录主表数据 （实时数据[不会删除]  +  历史修改过程中的数据）--全表记录【所有记录及历史】--可追溯
    // 64: 有同步数据表 ***_doing;--业务进行表【轻量级表】，当业务进行中时，可直接操作进行表【提高数据操作的率】，
    //                  一旦业务完成，则删除进行表中的数据，原表作为原始数据使用
    //                  -- TODO 直接操作业务写到操作操作的底层 CommonDB 【存在就同步更新，不存在：业务已结束或不用进行表了】
    public static $ownProperty = (1 | 2 | 4 | 8 | 16);// (1 | 2 | 4 | 8 | 16);
    // 同步表后缀 => 同步权限 0/1:增、2改  ; 4：删 (1 | 2)：可做业务同步 ；(1 | 2 | 4)： 操作全同步表【含删除】）
    // 如果是空数组【没有配置】，默认为 ['doing' => (1 | 2 | 4)]
    public static $syncTables = [
        // 'doing' => (1 | 2 | 4),
    ];
    // 主键id的值类型：
    //      1自增id[默认]
    //      2计数器，缓存redis，自增，redis没有，则查表中最大值自增
    //      256 计数器，批量的，自动优先批量生成，在使用的过程中自动补充。--不浪费【没有使用的，自动回收重新历用】
    //      按时间生成 bigint类型
    //          按年的分钟数~~~~~~~直观年，但长度短小
    //          4一秒1  0000个   2【位】+6【位】+ 秒2【位】+自增数5【位】 = 15【位】 => 年【2位】+每年中第几分钟【60*24*365=525600 6位】+ 秒【2位】--长度15位
    //          8一分钟100 0000个   2【位】+6【位】+自增数6【位】 = 14【位】 => 年【2位】+每年中第几分钟【60*24*365=525600 6位】-- 长度 14位
    //          按年的天数~~~~~~~~~~~~~~~~直观年及年的第几天
    //          16 一秒1  0000个 年【2位】+ 日期[一年中的第几天 001-365] 3位 +每天中第几秒钟 一天中的第几秒   0-86400【5位】+自增数5【位】 --长度15位
    //          32 一分钟100 0000个 年【2位】+ 日期[一年中的第几天 001-365] 3位 +每天中时分钟 H时i分【4位】 +自增数6【位】 --长度15位
    //          按年月日的 分或秒~~~~~~~~~~~~~直观年月日
    //          64 一秒1  0000个 年【2位】+ 日期[月日] 4位 +每天中第几秒钟 一天中的第几秒   0-86400【5位】+自增数5【位】 --长度16位
    //          128 一分钟100 0000个 年【2位】+ 日期[月日] 4位 ++每天中时分钟 H时i分【4位】 +自增数6【位】 --长度16位
//    public static $primaryKeyValType = 1;

    // 常量 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc'];
    // 其它具体的类可以在自己的类中 继承或 重写此常量 -- 只对外提供使用
     const ORDER_BY = ['sort_num' => 'desc', 'id' => 'desc'];// ['id' => 'desc'];

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'payment_project';

//    标示字段值文字  -- 样例
    // 是否指定金额1 用户或商家输入；2用户输入金额；4卖家输入金额；8指定金额【可以输入优惠金额】
    const SPECIFIED_AMOUNT_STATUS_BOTH_INPUT = 1;// 用户输入金额
    const SPECIFIED_AMOUNT_STATUS_USER_INPUT = 2;// 用户输入金额
    const SPECIFIED_AMOUNT_STATUS_SHOP_INPUT = 4;// 卖家输入金额
    const SPECIFIED_AMOUNT_STATUS_FIXED = 8;// 指定金额
    const SPECIFIED_AMOUNT_STATUS_ARR = [
        self::SPECIFIED_AMOUNT_STATUS_BOTH_INPUT => '用户或商家输入',
        self::SPECIFIED_AMOUNT_STATUS_USER_INPUT => '用户输入',
        self::SPECIFIED_AMOUNT_STATUS_SHOP_INPUT => '商家输入',
        self::SPECIFIED_AMOUNT_STATUS_FIXED => '固定金额',
    ];

    // 收费生效时间1长期有效; 2指定时间
    const PAY_VALID_STATUS_FOREVER = 1;// 长期有效
    const PAY_VALID_STATUS_FIXED= 2;// 指定时间
    const PAY_VALID_STATUS_ARR = [
        self::PAY_VALID_STATUS_FOREVER => '长期有效',
        self::PAY_VALID_STATUS_FIXED => '指定时间',
    ];

    // 有效时长1长期有效【可再次付费】；2长期有效【不可再次付费】；4指定有效时长
    const VALID_LIMIT_FOREVER_REPEAT = 1;// 长期有效【可再次付费】
    const VALID_LIMIT_FIXED_NOT_REPEAT = 2;// 长期有效【不可再次付费】
    const VALID_LIMIT_FIXED = 4;// 指定有效时长
    const VALID_LIMIT_ARR = [
        self::VALID_LIMIT_FOREVER_REPEAT => '长期有效【可再次付费】',
        self::VALID_LIMIT_FIXED_NOT_REPEAT => '长期有效【仅付费一次】',
        self::VALID_LIMIT_FIXED => '指定有效时长',
    ];

    // 判断唯一用户付款标准1不用登录付款【不判断】； 2 登录【不判断】；  4登录【唯一用户】 ；8 登录【唯一企业】
    const UNIQUE_USER_STANDARD_COMMON = 1;// 所有用户
    const UNIQUE_USER_STANDARD_LOGIN = 2;// 登录用户
    const UNIQUE_USER_STANDARD_USER = 4;// 所属用户
    const UNIQUE_USER_STANDARD_COMPANY = 8;// 所属企业
    const UNIQUE_USER_STANDARD_ARR = [
        self::UNIQUE_USER_STANDARD_COMMON => '所有用户',
        self::UNIQUE_USER_STANDARD_LOGIN => '登录用户',
        self::UNIQUE_USER_STANDARD_USER => '所属用户',
        self::UNIQUE_USER_STANDARD_COMPANY => '所属企业',
    ];

    // 开通状态1开通；2关闭；4作废【过时关闭】；
    const OPEN_STATUS_OPEN = 1;// 开通
    const OPEN_STATUS_CLOSE = 2;// 关闭
    const OPEN_STATUS_CANCEL = 4;// 作废
    const OPEN_STATUS_ARR = [
        self::OPEN_STATUS_OPEN => '开通',
        self::OPEN_STATUS_CLOSE => '关闭',
        self::OPEN_STATUS_CANCEL => '作废',
    ];

    // 收费状态1待收费；2收费中；4已收费
    const PAY_STATUS_WAIT = 1;// 待收费
    const PAY_STATUS_DOING = 2;// 收费中
    const PAY_STATUS_DOED = 4;// 已收费
    const PAY_STATUS_ARR = [
        self::PAY_STATUS_WAIT => '待收费',
        self::PAY_STATUS_DOING => '收费中',
        self::PAY_STATUS_DOED => '已收费',
    ];

    // 支付方式(1现金、2微信支付、4支付宝)
//    const PAY_METHOD_RMB = 1;// 现金
//    const PAY_METHOD_WECHAT = 2;// 微信支付
//    const PAY_METHOD_ALIPAY = 4;// 支付宝
//    const PAY_METHOD_ARR = [
//        self::PAY_METHOD_RMB => '现金',
//        self::PAY_METHOD_WECHAT => '微信支付',
//        self::PAY_METHOD_ALIPAY => '支付宝',
//    ];

    // 记录处理方式1自动处理【直接完成状态】；2人工处理
    const HANDLE_METHOD_AUTO = 1;// 自动处理
    const HANDLE_METHOD_MAN = 2;// 人工处理
    const HANDLE_METHOD_ARR = [
        self::HANDLE_METHOD_AUTO => '自动处理',
        self::HANDLE_METHOD_MAN => '人工处理',
    ];

    // 表里没有的字段
    protected $appends = ['specified_amount_status_text', 'pay_valid_status_text', 'valid_limit_text', 'unique_user_standard_text'
        , 'open_status_text', 'pay_status_text', 'handle_method_text'];// , 'pay_method_text'

    /**
     * 获取是否指定金额文字
     *
     * @return string
     */
    public function getSpecifiedAmountStatusTextAttribute()
    {
        return Tool::getBitVals(static::SPECIFIED_AMOUNT_STATUS_ARR, $this->specified_amount_status, '、');
        // return static::SPECIFIED_AMOUNT_STATUS_ARR[$this->specified_amount_status] ?? '';
    }

    /**
     * 获取收费生效时间文字
     *
     * @return string
     */
    public function getPayValidStatusTextAttribute()
    {
        return Tool::getBitVals(static::PAY_VALID_STATUS_ARR, $this->pay_valid_status, '、');
        // return static::PAY_VALID_STATUS_ARR[$this->pay_valid_status] ?? '';
    }

    /**
     * 获取有效时长文字
     *
     * @return string
     */
    public function getValidLimitTextAttribute()
    {
        return Tool::getBitVals(static::VALID_LIMIT_ARR, $this->valid_limit, '、');
        // return static::VALID_LIMIT_ARR[$this->valid_limit] ?? '';
    }

    /**
     * 获取判断唯一用户付款标准文字
     *
     * @return string
     */
    public function getUniqueUserStandardTextAttribute()
    {
        return Tool::getBitVals(static::UNIQUE_USER_STANDARD_ARR, $this->unique_user_standard, '、');
        // return static::UNIQUE_USER_STANDARD_ARR[$this->unique_user_standard] ?? '';
    }

    /**
     * 获取开通状态文字
     *
     * @return string
     */
    public function getOpenStatusTextAttribute()
    {
        return Tool::getBitVals(static::OPEN_STATUS_ARR, $this->open_status, '、');
        // return static::OPEN_STATUS_ARR[$this->open_status] ?? '';
    }

    /**
     * 获取收费状态文字
     *
     * @return string
     */
    public function getPayStatusTextAttribute()
    {
        return Tool::getBitVals(static::PAY_STATUS_ARR, $this->pay_status, '、');
        // return static::PAY_STATUS_ARR[$this->pay_status] ?? '';
    }

    /**
     * 获取支付方式文字
     *
     * @return string
     */
//    public function getPayMethodTextAttribute()
//    {
//        return Tool::getBitVals(static::PAY_METHOD_ARR, $this->pay_method, '、');
//        // return static::PAY_METHOD_ARR[$this->pay_method] ?? '';
//    }

    /**
     * 获取记录处理方式文字
     *
     * @return string
     */
    public function getHandleMethodTextAttribute()
    {
        return Tool::getBitVals(static::HANDLE_METHOD_ARR, $this->handle_method, '、');
        // return static::HANDLE_METHOD_ARR[$this->handle_method] ?? '';
    }

    /**
     * 获取付款/收款项目字段-二维
     */
    public function paymentProjectFields()
    {
        return $this->hasMany('App\Models\QualityControl\PaymentProjectFields', 'payment_project_id', 'id');
    }

    /**
     * 获取付款/收款记录-二维
     */
    public function paymentRecord()
    {
        return $this->hasMany('App\Models\QualityControl\PaymentRecord', 'payment_project_id', 'id');
    }

    /**
     * 获取付款/收款记录流水-二维
     */
    public function paymentRecordFlow()
    {
        return $this->hasMany('App\Models\QualityControl\PaymentRecordFlow', 'payment_project_id', 'id');
    }

    /**
     * 获取付款/收款记录字段-二维
     */
    public function paymentRecordFields()
    {
        return $this->hasMany('App\Models\QualityControl\PaymentRecordFields', 'payment_project_id', 'id');
    }

    /**
     * 获取付款/收款记录操作日志-二维
     */
    public function paymentRecordLog()
    {
        return $this->hasMany('App\Models\QualityControl\PaymentRecordLog', 'payment_project_id', 'id');
    }
}
