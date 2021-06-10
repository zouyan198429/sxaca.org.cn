

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>开启头部工具栏 - 数据表格</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    @include('admin.layout_public.pagehead')
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('layui-admin-v1.2.1/src/layuiadmin/style/admin.css')}}" media="all">
    {{--  本页单独使用 --}}
    <script src="{{asset('dist/lib/kindeditor/kindeditor.min.js')}}"></script>
</head>
<body>

{{--<div id="crumb"><i class="fa fa-reorder fa-fw" aria-hidden="true"></i> {{ $operate ?? '' }}员工</div>--}}
<div class="mm">
    <form class="am-form am-form-horizontal" method="post"  id="addForm">
        <input type="hidden" name="hidden_option"  value="{{ $hidden_option ?? 0 }}" />
        <input type="hidden" name="id" value="{{ $info['id'] ?? 0 }}"/>
        <table class="table1">
            <tr  @if (isset($hidden_option) && ($hidden_option & 1) == 1 ) style="display: none;"  @endif>
                <th>所属企业<span class="must"></span></th>
                <td>
                    <input type="hidden" class="select_id"  name="company_id"  value="{{ $info['company_id'] ?? '' }}" /><br/>
                    <span class="select_name company_name">{{ $info['user_company_name'] ?? '' }}</span>
                    <i class="close select_close company_id_close"  onclick="clearSelect(this)" style="display: none;">×</i>
                    <button  type="button"  class="btn btn-danger  btn-xs ace-icon fa fa-plus-circle bigger-60"  onclick="otheraction.selectCompany(this)">选择所属企业</button>
                </td>
            </tr>
            <tr>
                <th>试题分类<span class="must"></span></th>
                <td  class="seledTypeNo">
                    @foreach ($type_no_kv as $k => $txt)
                        <label><input type="checkbox"  name="type_no[]"  value="{{ $k }}"  @if(isset($defaultTypeNo) && (($defaultTypeNo & $k) == $k)) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>试题类型<span class="must">*</span></th>
                <td class="sel_subject_type">
                    @foreach ($subjectType as $k=>$txt)
                        <label><input type="radio"  name="subject_type"  value="{{ $k }}"  @if(isset($defaultSubjectType) && $defaultSubjectType == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                    <p>注：如果只想使用自动阅卷模式，请勿使用【人工批阅】的试题类型。当然，只要考试试卷没有【人工批阅】类型的试题，都可以自动批卷。</p>
                </td>
            </tr>
            <tr>
                <th>题目<span class="must">*</span></th>
                <td>
                    <textarea class="kindeditor" name="title" rows="15" id="doc-ta-1" style=" width:770px;height:400px;">{!!  htmlspecialchars($info['title'] ?? '' )   !!}</textarea>
                    <p class="hand_sure_answer">
                        填空题【确切答案】格式：<br/>
                        一、下划线格式：<br/>
                        1、唯一值格式：1+1={!! $egFormat['splitBegin'] ?? '' !!}2{!! $egFormat['splitEnd'] ?? '' !!}；前端显示：1+1=______; 答案只能填2。---确切的唯一值！<br/>
                        2、多选一格式：1+1>={!! $egFormat['splitBegin'] ?? '' !!}0{!! $egFormat['splitMid'] ?? '' !!}1{!! $egFormat['splitMid'] ?? '' !!}2{!! $egFormat['splitEnd'] ?? '' !!}；前端显示：1+1>=______; 答案只能填 0 或 1 或2。---确切的三个值之一！<br/>
                        二、小括号格式：<br/>
                        1、唯一值格式：1+1={!! $egFormat['bracketsBegin'] ?? '' !!}2{!! $egFormat['bracketsEnd'] ?? '' !!}；前端显示：1+1=(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;); 答案只能填2。---确切的唯一值！<br/>
                        2、多选一格式：1+1>={!! $egFormat['bracketsBegin'] ?? '' !!}0{!! $egFormat['splitMid'] ?? '' !!}1{!! $egFormat['splitMid'] ?? '' !!}2{!! $egFormat['bracketsEnd'] ?? '' !!}；前端显示：1+1>=(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;); 答案只能填 0 或 1 或2。---确切的三个值之一！
                        <br/>三、小括号 + 下划线格式：<br/>
                        1、唯一值格式：1+1={!! $egFormat['bracketsUnderLineBegin'] ?? '' !!}2{!! $egFormat['bracketsUnderLineEnd'] ?? '' !!}；前端显示：1+1=(________); 答案只能填2。---确切的唯一值！<br/>
                        2、多选一格式：1+1>={!! $egFormat['bracketsUnderLineBegin'] ?? '' !!}0{!! $egFormat['splitMid'] ?? '' !!}1{!! $egFormat['splitMid'] ?? '' !!}2{!! $egFormat['bracketsUnderLineEnd'] ?? '' !!}；前端显示：1+1>=(________); 答案只能填 0 或 1 或2。---确切的三个值之一！
                        <br/>四、有多个填空格时，可以用以上3种方式可以随意组合使用：<br/>
                        三原色是{!! $egFormat['splitBegin'] ?? '' !!}红色{!! $egFormat['splitEnd'] ?? '' !!}、{!! $egFormat['bracketsBegin'] ?? '' !!}绿色{!! $egFormat['bracketsEnd'] ?? '' !!}、{!! $egFormat['bracketsUnderLineBegin'] ?? '' !!}蓝色{!! $egFormat['bracketsUnderLineEnd'] ?? '' !!}。
                        <br/>
                        前端显示：三原色是______、(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)、(________)。

                    </p>
                    <p class="hand_judge_answer">
                        填空题【人工批阅】格式：
                        答案占位说明->下划线格式 {!! $egFormat['splitBegin'] ?? '' !!}5{!! $egFormat['splitEnd'] ?? '' !!} ；
                        小括号格式{!! $egFormat['bracketsBegin'] ?? '' !!}5{!! $egFormat['bracketsEnd'] ?? '' !!} ；
                        小括号 + 下划线 格式{!! $egFormat['bracketsUnderLineBegin'] ?? '' !!}5{!! $egFormat['bracketsUnderLineEnd'] ?? '' !!} ；
                        --中间的数字5，代表指定书写位置预留的字符位个数。<br/>
                        1、下划线格式：
                        <br/>题目内容：墙面装饰分为{!! $egFormat['splitBegin'] ?? '' !!}5{!! $egFormat['splitEnd'] ?? '' !!}和{!! $egFormat['splitBegin'] ?? '' !!}5{!! $egFormat['splitEnd'] ?? '' !!}，不同的墙面有着不同的装饰效果和功能。
                        <br/>前端显示结果：墙面装饰分为________和________，不同的墙面有着不同的装饰效果和功能。
                        <br/>
                        2、小括号格式：
                        <br/>题目内容：墙面装饰分为{!! $egFormat['bracketsBegin'] ?? '' !!}5{!! $egFormat['bracketsEnd'] ?? '' !!}和{!! $egFormat['bracketsBegin'] ?? '' !!}5{!! $egFormat['bracketsEnd'] ?? '' !!}，不同的墙面有着不同的装饰效果和功能。
                        <br/>前端显示结果：墙面装饰分为(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)和(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)，不同的墙面有着不同的装饰效果和功能。
                        <br/>
                        3、小括号 + 下划线格式：
                        <br/>题目内容：墙面装饰分为{!! $egFormat['bracketsUnderLineBegin'] ?? '' !!}5{!! $egFormat['bracketsUnderLineEnd'] ?? '' !!}和{!! $egFormat['bracketsUnderLineBegin'] ?? '' !!}5{!! $egFormat['bracketsUnderLineEnd'] ?? '' !!}，不同的墙面有着不同的装饰效果和功能。
                        <br/>前端显示结果：墙面装饰分为(________)和(________)，不同的墙面有着不同的装饰效果和功能。 <br/>
                        4、以上3种方式可以随意组合使用：
                        <br/>题目内容：墙面装饰分为{!! $egFormat['splitBegin'] ?? '' !!}5{!! $egFormat['splitEnd'] ?? '' !!}、{!! $egFormat['bracketsBegin'] ?? '' !!}5{!! $egFormat['bracketsEnd'] ?? '' !!}和{!! $egFormat['bracketsUnderLineBegin'] ?? '' !!}5{!! $egFormat['bracketsUnderLineEnd'] ?? '' !!}，不同的墙面有着不同的装饰效果和功能。
                        <br/>前端显示结果：墙面装饰分为________、(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)和(________)，不同的墙面有着不同的装饰效果和功能。
                    </p>
                </td>
            </tr>
            <tr  class="answer_judge">
                <th>判断答案<span class="must">*</span></th>
                <td class="sel_answer">
                    @foreach ($answer as $k=>$txt)
                        <label><input type="radio"  name="answer"  value="{{ $k }}"  @if(isset($defaultAnswer) && $defaultAnswer == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr class="answer_many">
                <th>单选/多选选项<span class="must">*</span></th>
                <td >
                    <table class="table2">
                        <thead>
                        <tr>
                            <th style="width: 720px;">答案</th>
                            <th >正确答案</th>
                            <th >操作</th>
                        </tr>
                        </thead>
                        <tbody id="data_list">
                        {{--
                        <tr>
                            <td>
                                <input type="hidden" name="answer_id[]" value="{{ $v['id'] or 0 }}"/>
                                <span class="colum"></span>、<input type="text" name="answer_content[]" class="inp wlong" value="{{ $v['answer_content'] or '' }}"/>
                            </td>
                            <td align="center">
                                <input type="radio" name="answer_val" value=""  @if(isset($v['is_right']) && $v['is_right'] == 1) checked="checked"  @endif />
                                <input type="checkbox" class="check_answer" name="check_answer_val[]" value="" @if(isset($v['is_right']) && $v['is_right'] == 1) checked="checked"  @endif/>
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.moveUp(this)">
                                    <i class="ace-icon fa fa-arrow-up bigger-60"> 上移</i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.moveDown(this)">
                                    <i class="ace-icon fa fa-arrow-down bigger-60"> 下移</i>
                                </a>

                                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.del(this)">
                                    <i class="ace-icon fa fa-trash-o bigger-60"> 移除</i>
                                </a>
                            </td>
                        </tr>
                        --}}
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right;">
                                <a href="javascript:void(0);" class="btn btn-mini btn-info" onclick="otheraction.add()"  style="margin-right: 132px;">
                                    <i class="ace-icon fa fa-plus bigger-60"> 增加答案</i>
                                </a>
                            </td>
                        </tr>

                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr>
                <th>试题分析<span class="must"></span></th>
                <td>
                    <textarea class="kindeditor" name="analyse_answer" rows="15" id="doc-ta-1" style=" width:770px;height:400px;">{!!  htmlspecialchars($info['analyse_answer'] ?? '' )   !!}</textarea>
                </td>
            </tr>
            <tr>
                <th>开启状态<span class="must">*</span></th>
                <td class="sel_open_status">
                    @foreach ($openStatus as $k=>$txt)
                        <label><input type="radio"  name="open_status"  value="{{ $k }}"  @if(isset($defaultOpenStatus) && $defaultOpenStatus == $k) checked="checked"  @endif />{{ $txt }} </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>排序[降序]<span class="must">*</span></th>
                <td>
                    <input type="text" class="inp wnormal"  name="sort_num" value="{{ $info['sort_num'] ?? '' }}" placeholder="请输入排序"  onkeyup="isnum(this) " onafterpaste="isnum(this)"  />
                </td>
            </tr>

            <tr>
                <th> </th>
                <td><button class="btn btn-l wnormal"  id="submitBtn" >提交</button></td>
            </tr>

        </table>
    </form>
</div>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.all.js')}}"></script>
{{--<script src="{{asset('layui-admin-v1.2.1/src/layuiadmin/layui/layui.js')}}"></script>--}}
@include('public.dynamic_list_foot')

<script type="text/javascript">

    // hidden_option 8192:调用父窗品的方法：[public/js目录下的] 项目目录+数据功能目录+当前文件名称 【有_线，则去掉】
    // 其它地方弹出此窗，保存完成时调用的父窗口方法名称 参数(obj:当前表单值对像, result:保存接口返回的结果，operateNum:自己定义的一个编号【页面有多处用到时用--通知父窗口调用位置】)
    var PARENT_BUSINESS_FUN_NAME = "adminQualityControlrrrddddedit";

    var SAVE_URL = "{{ url('api/admin/company_subject/ajax_save') }}";// ajax保存记录地址
    var LIST_URL = "{{url('admin/company_subject')}}";//保存成功后跳转到的地址

    var SELECT_COMPANY_URL = "{{url('admin/company/select')}}";// 选择所属企业

    var DYNAMIC_BAIDU_TEMPLATE = "baidu_template_data_list";//百度模板id
    var DYNAMIC_TABLE_BODY = "data_list";//数据列表id
    var ANSWER_LIST = @json($info['subject_answer'] ?? []) ;
    var ANSWER_DATA_LIST = {
        'data_list':ANSWER_LIST
    };
    var DEFAULT_DATA_LIST = [{'id':0, 'answer_content':'', 'is_right':0,}];// 默认答案
</script>
<script src="{{ asset('/js/admin/QualityControl/CompanySubject_edit.js') }}?10"  type="text/javascript"></script>
</body>
</html>
