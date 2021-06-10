<?php

namespace App\Models\QualityControl;

class Staff extends BasePublicModel
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

    public static $cacheVersion = 'v4';// 内容随意改[可0{空默认为0}开始自增]- 如果运行过程中，有直接对表记录进行修改，增加或修改字段名，则修改此值，使表记录的相关缓存过期。
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

    // public static $IntPriceFields = [];//[有则设置] 表中整型表示价格的字段数组 -- 一维数组，目的：方便统一把数据中的字段转浮点数或转整数
    // public static $IntPriceIndex = [];// $IntPriceFields 字段对应的扩大陪数；没有配置默认为2  扩大或缩小倍数： pow(10,2)；格式：['sl' => 2,]；注：2的可不用配置【只配置特殊的】，因为默认就是2

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
    // const ORDER_BY = ['sort_num' => 'desc', 'id' => 'desc'];// ['id' => 'desc'];

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'staff';

    /**
     * 在数组中隐藏的属性
     *
     * @var array
     */
    protected $hidden = ['admin_password'];

    /**
     * 设置帐号的密码md5加密
     *
     * @param  string  $value
     * @return string
     */
    public function setAdminPasswordAttribute($value)
    {
        $this->attributes['admin_password'] = md5($value);
    }

    // 数据类型
    public static $recordTypeArr = [
        '1' => '所有数据',
        '2' => '30天内过期',
        '4' => '已过期30天内',
    ];

    // 拥有者类型1平台2企业4个人
    public static $adminTypeArr = [
        '1' => '平台',
        '2' => '企业',
        '4' => '个人',
        '8' => '数据查看人员',
        '16' => '第三方服务商',
    ];

    // 是否完善资料1待完善2已完善
    public static $isPerfectArr = [
        '1' => '待完善',
        '2' => '已完善',
    ];

    // 是否超级帐户2否1是
    public static $issuperArr = [
        '2' => '普通帐户',
        '1' => '超级帐户',
    ];

    // 审核状态1待审核2审核通过4审核不通过
    public static $openStatusArr = [
        '1' => '待审核',
        '2' => '审核通过',
        '4' => '审核不通过',
    ];

    // 状态 1正常 2冻结
    public static $accountStatusArr = [
        '1' => '正常',
        '2' => '冻结',
    ];

    // 性别0未知1男2女
    public static $sexArr = [
        '0' => '未知',
        '1' => '男',
        '2' => '女',
    ];

    // 企业--是否独立法人1独立法人 2非独立法人
    public static $companyIsLegalPersionArr = [
        '1' => '独立法人',
        '2' => '非独立法人',
    ];

    // 企业--企业类型1检测机构、2生产企业
    public static $companyTypeArr = [
        '1' => '检测机构',
        '2' => '生产企业',
    ];

    // 企业--企业性质1企业法人 、2企业非法人、3事业法人、4事业非法人、5社团法人、6社团非法人、7机关法人、8机关非法人、9其它机构、10民办非企业单位、11个体 、12工会法人
    public static $companyPropArr = [
        '1' => '企业法人',
        '2' => '企业非法人',
        '3' => '事业法人',
        '4' => '事业非法人',
//        '5' => '社团法人',
//        '6' => '社团非法人',
//        '7' => '机关法人',
//        '8' => '机关非法人',
        '9' => '其它机构',
//        '10' => '民办非企业单位',
//        '11' => '个体',
//        '12' => '工会法人',
    ];

    // 企业--单位人数1、1-20、2、20-100、3、100-500、4、500以上
    public static $companyPeoplesNumArr = [
        '1' => '1-20',
        '2' => '20-100',
        '3' => '100-500',
        '4' => '500以上',
    ];

    // 企业--会员等级1非会员  2会员  4理事  8常务理事   16理事长
    public static $companyGradeArr = [
        '1' => '非会员',
        '2' => '会员',
        '4' => '理事',
        '8' => '常务理事',
        '16' => '理事长',
    ];

    // 角色1法人  2最高管理者  4技术负责人  8授权签字人  16 质量负责人  32员工
    public static $roleNumArr = [
        '1' => '法人',
        '2' => '最高管理者',
        '4' => '技术负责人',
        '8' => '授权签字人',
        '16' => '质量负责人',
        '32' => '员工',
    ];

    // 角色审核状态 1待审核 2 审核通过  4 审核未通过
    public static $roleStatusArr = [
        '1' => '待审核',
        '2' => '审核通过',
        '4' => '审核未通过',
    ];

    // 授权人审核状态 1待审核 2 审核通过  4 审核未通过
    public static $signStatusArr = [
        '1' => '待审核',
        '2' => '审核通过',
        '4' => '审核未通过',
    ];

    // 是否食品1食品  2非食品
    public static $signIsFoodArr = [
        '1' => '食品',
        '2' => '非食品',
    ];

    // 企业--会员等级是否有续期1没有2有
    public static $companyGradeContinueArr = [
        '1' => '无续期',
        '2' => '有续期',
    ];

    // 有记录官网新导入的 0 普通的【我们系统就有的】； 1 官网抓取的，【我们系统没有的】需要人工判断的；2 官网抓取的【我们系统没有的】已首次登陆并完善资料;4我们系统有的，但是也抓取过文件的
    public static $isImportArr = [
        '0' => '源生用户',
        '1' => '省局用户',
        '2' => '省局用户【已初登录】',
        '4' => '混合用户',
    ];

    // 表里没有的字段
    protected $appends = ['is_perfect_text', 'admin_type_text', 'issuper_text', 'open_status_text', 'account_status_text', 'sex_text', 'company_is_legal_persion_text'
        , 'company_type_text', 'company_prop_text', 'company_peoples_num_text', 'company_grade_text', 'role_num_text', 'role_status_text', 'sign_status_text'
        , 'sign_is_food_text', 'company_grade_continue_text', 'is_import_text'];

    /**
     * 获取用户的是否完善资料文字
     *
     * @return string
     */
    public function getIsPerfectTextAttribute()
    {
        return static::$isPerfectArr[$this->is_perfect] ?? '';
    }

    /**
     * 获取用户的类型文字
     *
     * @return string
     */
    public function getAdminTypeTextAttribute()
    {
        return static::$adminTypeArr[$this->admin_type] ?? '';
    }

    /**
     * 获取用户是否超级帐户文字
     *
     * @return string
     */
    public function getIssuperTextAttribute()
    {
        return static::$issuperArr[$this->issuper] ?? '';
    }

    /**
     * 获取用户审核状态文字
     *
     * @return string
     */
    public function getOpenStatusTextAttribute()
    {
        return static::$openStatusArr[$this->open_status] ?? '';
    }

    /**
     * 获取用户状态文字
     *
     * @return string
     */
    public function getAccountStatusTextAttribute()
    {
        return static::$accountStatusArr[$this->account_status] ?? '';
    }

    /**
     * 获取用户性别文字
     *
     * @return string
     */
    public function getSexTextAttribute()
    {
        return static::$sexArr[$this->sex] ?? '';
    }

    /**
     * 获取是否独立法人文字
     *
     * @return string
     */
    public function getCompanyIsLegalPersionTextAttribute()
    {
        return static::$companyIsLegalPersionArr[$this->company_is_legal_persion] ?? '';
    }

    /**
     * 获取企业类型文字
     *
     * @return string
     */
    public function getCompanyTypeTextAttribute()
    {
        return static::$companyTypeArr[$this->company_type] ?? '';
    }

    /**
     * 获取企业性质文字
     *
     * @return string
     */
    public function getCompanyPropTextAttribute()
    {
        return static::$companyPropArr[$this->company_prop] ?? '';
    }

    /**
     * 获取单位人数文字
     *
     * @return string
     */
    public function getCompanyPeoplesNumTextAttribute()
    {
        return static::$companyPeoplesNumArr[$this->company_peoples_num] ?? '';
    }

    /**
     * 获取会员等级文字
     *
     * @return string
     */
    public function getCompanyGradeTextAttribute()
    {
        return static::$companyGradeArr[$this->company_grade] ?? '';
    }

    /**
     * 获取用户的是否完善资料文字
     *
     * @return string
     */
    public function getCompanyGradeContinueTextAttribute()
    {
        return static::$companyGradeContinueArr[$this->company_grade_continue] ?? '';
    }

    /**
     * 获取用户的类型文字
     *
     * @return string
     */
    public function getIsImportTextAttribute()
    {
        return static::$isImportArr[$this->is_import] ?? '';
    }

    /**
     * 获取角色文字
     *
     * @return string
     */
    public function getRoleNumTextAttribute()
    {
        $return_arr = [];
        $role_num = $this->role_num;
        if($role_num <= 0 ) return '';
        foreach(static::$roleNumArr as $k => $v){
           if(($k & $role_num) == $k)  array_push($return_arr, $v);
        }
        return implode('、', $return_arr);

        // return static::$roleNumArr[$this->role_num] ?? '';
    }

    /**
     * 获取角色审核状态文字
     *
     * @return string
     */
    public function getRoleStatusTextAttribute()
    {
        return static::$roleStatusArr[$this->role_status] ?? '';
    }

    /**
     * 获取授权人审核状态文字
     *
     * @return string
     */
    public function getSignStatusTextAttribute()
    {
        return static::$signStatusArr[$this->sign_status] ?? '';
    }

    /**
     * 获取授权人审核状态文字
     *
     * @return string
     */
    public function getSignIsFoodTextAttribute()
    {
        return static::$signIsFoodArr[$this->sign_is_food] ?? '';
    }

    /**
     * 获取资源分类-二维
     */
    public function resourceTypeSelfs()
    {
        return $this->hasMany('App\Models\QualityControl\ResourceTypeSelf', 'ower_id', 'id');
    }

    /**
     * 获取资源分类历史-二维
     */
    public function resourceTypeSelfHistorys()
    {
        return $this->hasMany('App\Models\QualityControl\ResourceTypeSelfHistory', 'ower_id', 'id');
    }

    /**
     * 获取资源-二维
     */
    public function resources()
    {
        return $this->hasMany('App\Models\QualityControl\Resource', 'ower_id', 'id');
    }

    /**
     * 获取资源历史-二维
     */
    public function resourceHistorys()
    {
        return $this->hasMany('App\Models\QualityControl\ResourceHistory', 'ower_id', 'id');
    }

    /**
     * 获取验证码-二维
     */
    public function smsCodes()
    {
        return $this->hasMany('App\Models\QualityControl\SmsCode', 'staff_id', 'id');
    }

    /**
     * 获取历史-二维
     */
    public function staffHistorys()
    {
        return $this->hasMany('App\Models\QualityControl\StaffHistory', 'staff_id', 'id');
    }

    /**
     * 获取注册记录-二维
     */
    public function regLogs()
    {
        return $this->hasMany('App\Models\QualityControl\RegLog', 'staff_id', 'id');
    }

    /**
     * 获取企业资质证书-二维
     */
    public function companyCertificates()
    {
        return $this->hasMany('App\Models\QualityControl\CompanyCertificate', 'company_id', 'id');
    }

    /**
     * 获取企业应用管理-二维
     */
    public function apply()
    {
        return $this->hasMany('App\Models\QualityControl\Apply', 'staff_id', 'id');
    }

    /**
     * 获取能力验证操作日志-二维
     */
    public function alilityJoinLogs()
    {
        return $this->hasMany('App\Models\QualityControl\AbilityJoinLogs', 'staff_id', 'id');
    }

    /**
     * 获取能力验证报名项-二维
     */
    public function abilityJoinItems()
    {
        return $this->hasMany('App\Models\QualityControl\AbilityJoinItems', 'staff_id', 'id');
    }

    /**
     * 获取能力验证报名-二维
     */
    public function abilityJoins()
    {
        return $this->hasMany('App\Models\QualityControl\AbilityJoin', 'staff_id', 'id');
    }


    /**
     * 获取面授操作日志-二维
     */
    public function courseLog()
    {
        return $this->hasMany('App\Models\QualityControl\CourseLog', 'staff_id', 'id');
    }

    /**
     * 获取企业的所有用户-二维
     */
    public function users()
    {
        return $this->hasMany('App\Models\QualityControl\Staff', 'company_id', 'id');
    }

    /**
     * 获取企业的机构信息管理--能力附表-二维
     */
    public function companySchedule()
    {
        return $this->hasMany('App\Models\QualityControl\CompanySchedule', 'company_id', 'id');
    }

    /**
     * 获取企业的机构自我声明管理-二维
     */
    public function companyStatement()
    {
        return $this->hasMany('App\Models\QualityControl\CompanyStatement', 'company_id', 'id');
    }

    /**
     * 获取企业的能力验证-二维
     */
    public function companyAbility()
    {
        return $this->hasMany('App\Models\QualityControl\CompanyAbility', 'company_id', 'id');
    }

    /**
     * 获取企业的监督检查-二维
     */
    public function companyInspect()
    {
        return $this->hasMany('App\Models\QualityControl\CompanyInspect', 'company_id', 'id');
    }

    /**
     * 获取企业的企业其它【新闻】-二维
     */
    public function companyNews()
    {
        return $this->hasMany('App\Models\QualityControl\CompanyNews', 'company_id', 'id');
    }

    /**
     * 获取企业的机构处罚管理-二维
     */
    public function companyPunish()
    {
        return $this->hasMany('App\Models\QualityControl\CompanyPunish', 'company_id', 'id');
    }

    /**
     * 获取企业的实验室地址-二维
     */
    public function laboratoryAddr()
    {
        return $this->hasMany('App\Models\QualityControl\LaboratoryAddr', 'company_id', 'id');
    }

    /**
     * 获取企业的监督检查信息管理-二维
     */
    public function companySupervise()
    {
        return $this->hasMany('App\Models\QualityControl\CompanySupervise', 'company_id', 'id');
    }

    /**
     * 获取收款订单-二维
     */
    public function orders()
    {
        return $this->hasMany('App\Models\QualityControl\Orders', 'company_id', 'id');
    }

    /**
     * 获取支付订单-二维
     */
    public function orderPay()
    {
        return $this->hasMany('App\Models\QualityControl\OrderPay', 'company_id', 'id');
    }

    /**
     * 获取收款订单财务流水-二维
     */
    public function orderFlow()
    {
        return $this->hasMany('App\Models\QualityControl\OrderFlow', 'company_id', 'id');
    }

    /**
     * 获取发票配置购买方-二维
     */
    public function invoiceBuyer()
    {
        return $this->hasMany('App\Models\QualityControl\InvoiceBuyer', 'company_id', 'id');
    }

    /**
     * 获取发票主表-二维
     */
    public function invoices()
    {
        return $this->hasMany('App\Models\QualityControl\Invoices', 'company_id', 'id');
    }

    /**
     * 获取城市-一维
     */
    public function city()
    {
        return $this->belongsTo('App\Models\QualityControl\Citys', 'city_id', 'id');
    }

    /**
     * 获取行业-一维
     */
    public function industry()
    {
        return $this->belongsTo('App\Models\QualityControl\Industry', 'company_industry_id', 'id');
    }

    /**
     * 获取所属企业[普通用户]-一维
     */
    public function company()
    {
        return $this->belongsTo('App\Models\QualityControl\Staff', 'company_id', 'id');
    }

    /**
     * 获取关联到的扩展信息---一维
     */
    public function extend()
    {
        return $this->hasOne('App\Models\QualityControl\StaffExtend', 'staff_id', 'id');
    }

    /**
     * 获取关联到的企业开票配置信息---一维
     */
    public function companyBillingConfig()
    {
        return $this->hasOne('App\Models\QualityControl\CompanyBillingConfig', 'staff_id', 'id');
    }
}
