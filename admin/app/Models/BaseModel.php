<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
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
    public static $ownProperty = 0;
    public static $modelPath = ''; // 模型路径 名称\..\名称
    public static $IntPriceFields = [];//[有则设置] 表中整型表示价格的字段数组 -- 一维数组，目的：方便统一把数据中的字段转浮点数或转整数
    public static $IntPriceIndex = [];// $IntPriceFields 字段对应的扩大陪数；没有配置默认为2  扩大或缩小倍数： pow(10,2)；格式：['sl' => 2,]；注：2的可不用配置【只配置特殊的】，因为默认就是2
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
    public static $primaryKeyValType = 1;

    // 常量 默认的排序字段数组 ['id' => 'desc']--默认 或 ['sort_num' => 'desc', 'id' => 'desc'];
    // 其它具体的类可以在自己的类中 继承或 重写此常量 -- 只对外提供使用
    const ORDER_BY = ['id' => 'desc'];// ['sort_num' => 'desc', 'id' => 'desc'];

    /**
     * The attributes that aren't mass assignable.
     * 所有属性都是可批量赋值
     * @var array
     */
    protected $guarded = [];


    /**
     * 获得属性
     *
     * @param string $attrName 属性名称[静态或动态]--注意静态时不要加$ ,与动态属性一样 如 attrTest
     * @param int $isStatic 是否静态属性 0：动态属性 1 静态属性
     * @return mixed 属性值
     * @author zouyan(305463219@qq.com)
     */
    public function getTableAttr($attrName = '', $isStatic = 0){
        if ( !property_exists($this, $attrName)) {
            throws("未定义[" . $attrName  . "] 属性");
        }
        // 静态
        if($isStatic == 1) return $this::${$attrName};
        return $this->{$attrName};
    }

    // _________重写核心代码_____开始____________

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, ['increment', 'decrement'])) {
            return $this->$method(...$parameters);
        } else { // 自己重写的
            $result = $this->doCustomMethod($method ,$parameters ,1);
            if ($result !== null) {
                return $result;
            }
        }

        return $this->newQuery()->$method(...$parameters);
    }

    /**
     * Get a relationship.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getRelationValue($key)
    {
        // If the key already exists in the relationships array, it just means the
        // relationship has already been loaded, so we'll just return it out of
        // here because there is no need to query within the relations twice.
        if ($this->relationLoaded($key)) {
            return $this->relations[$key];
        }

        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return results from the query
        // and hydrate the relationship's value on the "relationships" array.
        if (method_exists($this, $key)) {
            return $this->getRelationshipFromMethod($key);
        } else { // 自己重写的__get的方法
            $result = $this->doCustomMethod($key ,[] ,4);
            if ($result !== null) {
                return $result;
            }
        }
    }

    // _________重写核心代码_____结束____________


    // _________自己编写的通过__call或 __get 调用的方法_____开始____________

    // 自己编写的__call或 __get 调用 调渡方法。有新的需要，在此添加方法
    /**
     * 自定义公共类处理方法在__call 或者 ->属性 __get 时调用
     * 一、多对多的多态关联[反向-用于查看某个资源的使用者信息(如新闻)]   getCustom_morphedByMany_** [**：具体的模型名称] ，
     *         必须要用with来用，不然会报错，TODO后面再看原因
     *         使用
     *          1、
                $resource = Resource::find(1);
                $resource->load('getCustom_morphedByMany_SiteNews');
                或
                $resource = Resource::with('getCustom_morphedByMany_SiteNews')->find(1);
                或

                foreach ($resource->getCustom_morphedByMany_SiteNews as $siteNew) {
                echo '<pre>';
                print_r($siteNew->new_title);
                echo '</pre>';
                }
                2
                $siteResources = Resource::with([
                    'getCustom_morphedByMany_SiteNews'=> function ($query) {
                        $query->select('site_news.id','site_news.new_title');
                     }
                ])->find([1,2]);
                或
                $siteResources = Resource::with('getCustom_morphedByMany_SiteNews')->find([1,2]);

                foreach ($siteResources as $resource){
                    foreach ($resource->getCustom_morphedByMany_SiteNews as $siteNew) {
                        echo '<pre>';
                        print_r($siteNew->new_title);
                        echo '</pre>';
                    }
                }
     * @param  string  $method
     *   格式: getCustom_基础类的类型标识_公共类可能会使用关键字
     *
     * @param  array  $parameters
     * @param  int  $sourceNum 来源编号 1 __call;2__callstatic;4__get
     * @return mixed
     */
    public function doCustomMethod($method ,$parameters ,$sourceNum = 7)
    {
        preg_match('/^([^_]+)[_]([^_]+)[_]([^_]+)$/', $method, $matches);
        $preKey = $matches[1] ?? '';
        $operateType = $matches[2] ?? '';
        $keyWord = $matches[3] ?? '';

        if ($preKey !== 'getCustom' || empty($operateType)) {
            return null;
        }

//        if (method_exists($this, $keyWord)) {
//
//        }

        // 自定义的公共处理
        switch (trim($operateType)) {
            case 'morphedByMany'://多对多的多态关联  getCustom_morphedByMany_** [**：具体的模型名称]
                if ( ($sourceNum & 1) == 1  || ($sourceNum & 4) == 4) {
                    if (empty($keyWord)) { return null;}
                    return $this->getCustomMorphedByMany($keyWord);
                }
                break;
        }

        return null;
    }

    //多对多的多态关联
    /**
     * 获取使用该资源的新闻信息[二维对象]
     * @param  string  $modelName 数据模型的名称，如 SiteNews
     * @return mixed
     */
    public function getCustomMorphedByMany($modelName = null)
    {
        $related = "App\\Models\\". static::$modelPath . "\\" . $modelName;
        return $this->morphedByMany(
            $related// 站点新闻模型 'App\Models\SiteNews'
            ,'module'// 关系名称-注意：这个值必须是表中 ***_type 的星号部分，暂时还没有指定***_type 这个字段
            ,'resource_module'// 关系表名称
        // ,'resource_id'// 关系表中的与资源对象主键对应的字段
        // ,'module_id' // 关系表中的与新闻表主键对应的字段
        // ,'id'// 资源对象主键字段名
        // ,'id'// 主表新闻主键字段名
        )->withPivot('id', 'resource_id_history', 'operate_staff_id', 'operate_staff_id_history' )->withTimestamps();
    }

    // _________自己编写的通过__call或 __get 调用的方法_____结束____________


    //------- 多对多的多态关联-----开始------------------

    /**
     * 获取指定***模块所有图片资源[二维对象]
     */
    public function siteResources()
    {
        return $this->morphToMany(
            'App\Models\\'. static::$modelPath . '\Resource'//资源对象
            ,'module' // 关系名称-注意：这个值必须是表中 ***_type 的星号部分，暂时还没有指定***_type 这个字段
            ,'resource_module'// 关系表名称
        // ,'module_id'// 关系表中的与新闻表主键对应的字段
        // ,'resource_id'// 关系表中的与资源对象主键对应的字段
        // ,'id'// 主表新闻主键字段名
        // ,'id'// 资源对象主键字段名
        // ,$inverse 参数 flase[默认]，module_type 可以在 AppServiceProvider 中指定段名; true： 必须用App\Models\Resource
        )->withPivot('id', 'resource_id_history', 'operate_staff_id', 'operate_staff_id_history' )->withTimestamps();// ->withPivot('notice', 'id')
    }

    // 同步修改图片资源关系-
    /**
     * 获取指定***模块所有图片资源[二维对象]
     *       $siteNew = SiteNews::find(1);
     *       $siteNew->siteResources()->sync([1, 2]);
     *          的封装
     * 模块 单条的对象  SiteNews::find(1)->updateResourceByResourceIds([1,2,3]);
     * @param array $resourceIds 需要操作的资源id数组,空数组：代表删除
     */
    public function updateResourceByResourceIds($resourceIds = [])
    {
        $this->siteResources()->sync($resourceIds);
    }

    //------- 多对多的多态关联-----结束------------------
}
