<div class="search">
    <div class="wrap"> 
        <div class="slideTxtBox">
            <div class="hd">
                <ul>
                    <li @if (isset($qkey) && $qkey == 1 )  class="on" @endif>机构名称/执照号</li>
                    <li @if (isset($qkey) && $qkey == 2 )  class="on" @endif>类别/产品名称</li>
                    <li @if (isset($qkey) && $qkey == 4 )  class="on" @endif>证书编号</li>
                </ul>
            </div>
            <div class="bd">
                <ul class="searchbox">
                    <div style="display: none;"> <input type="radio" name="company_field" value="company_name" id="killOrder1"  @if (isset($field) && ($field == 'company_name' || $field != 'company_credit_code') ) checked @endif><label for="killOrder1">检验机构名称</label>
                    <input type="radio" name="company_field" value="company_credit_code" id="killOrder2"   @if (isset($field) && $field == 'company_credit_code') checked @endif><label for="killOrder2">统一社会信用代码或组织机构代码</label></div>
                    <input type="text" name="keyword" placeholder="" class="inp"   @if(isset($field) && $qkey == 1  && ($field == 'company_name' || $field == 'company_credit_code'))  value="{{ $keyword ?? '' }}"  @endif ><button class="searchbtn searchbtn_company" >搜索</button>
                </ul>
                <ul class="searchbox">
                    <input type="radio" name="rang_f_type" value="1" id="killOrder3"  @if (isset($rang_f_type) && ($rang_f_type == 1 || $rang_f_type != 2 ) ) checked @endif><label for="killOrder3">标准名称</label>
                    <input type="radio" name="rang_f_type" value="2" id="killOrder4"  @if (isset($rang_f_type) && $rang_f_type == 2 ) checked @endif><label for="killOrder4">标准编号</label>
                    <input type="text" name="keyword" placeholder="" class="inp"   @if(isset($rang_f_type) && $qkey == 2 && (in_array($rang_f_type, [1,2])))  value="{{ $keyword ?? '' }}"  @endif ><button class="searchbtn searchbtn_range" >搜索</button>
                </ul>
                <ul class="searchbox">
                    <input type="text" name="keyword" placeholder="证书号" class="inp"  @if(isset($field) && $qkey == 4  && $field == 'company_certificate_no')  value="{{ $keyword ?? '' }}"  @endif ><button class="searchbtn searchbtn_no" >搜索</button>
                </ul>
                <div class="c"></div>
            </div>
        </div>
        <script type="text/javascript">jQuery(".slideTxtBox").slide();</script>
    </div>
</div>
