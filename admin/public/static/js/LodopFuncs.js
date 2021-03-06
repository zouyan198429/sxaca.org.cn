var CreatedOKLodop7766=null;

//====判断是否需要安装CLodop云打印服务器:====
function needCLodop(){
    try{
	var ua=navigator.userAgent;
	if (ua.match(/Windows\sPhone/i) !=null) return true;
	if (ua.match(/iPhone|iPod/i) != null) return true;
	if (ua.match(/Android/i) != null) return true;
	if (ua.match(/Edge\D?\d+/i) != null) return true;
	if (ua.match(/QQBrowser/i) != null) return false;
	var verTrident=ua.match(/Trident\D?\d+/i);
	var verIE=ua.match(/MSIE\D?\d+/i);
	var verOPR=ua.match(/OPR\D?\d+/i);
	var verFF=ua.match(/Firefox\D?\d+/i);
	var x64=ua.match(/x64/i);
	if ((verTrident==null)&&(verIE==null)&&(x64!==null))
		return true; else
	if ( verFF !== null) {
		verFF = verFF[0].match(/\d+/);
		if ( verFF[0] >= 42 ) return true;
	} else
	if ( verOPR !== null) {
		verOPR = verOPR[0].match(/\d+/);
		if ( verOPR[0] >= 32 ) return true;
	} else
	if ((verTrident==null)&&(verIE==null)) {
		var verChrome=ua.match(/Chrome\D?\d+/i);
		if ( verChrome !== null ) {
			verChrome = verChrome[0].match(/\d+/);
			if (verChrome[0]>=42) return true;
		};
	};
        return false;
    } catch(err) {return true;};
};

//====页面引用CLodop云打印必须的JS文件：====
if (needCLodop()) {
    //让其它电脑的浏览器通过本机打印（适用例子）：
    // oscript = document.createElement("script");
    // oscript.src ="/CLodopfuncs.js";
    // var head = document.head || document.getElementsByTagName("head")[0] || document.documentElement;
    // head.insertBefore( oscript,head.firstChild );
    //让本机浏览器打印(更优先)：
    var oscript = document.createElement("script");
    oscript.src ="http://localhost:8000/CLodopfuncs.js?priority=1";
    var head = document.head || document.getElementsByTagName("head")[0] || document.documentElement;
    head.insertBefore( oscript,head.firstChild );
};

//====获取LODOP对象的主过程：====
function getLodop(oOBJECT,oEMBED){
    var strHtmInstall="<br><font color='#FF00FF'>打印控件未安装!点击这里<a href='/print_driver/install_lodop32.exe' target='_self'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
    var strHtmUpdate="<br><font color='#FF00FF'>打印控件需要升级!点击这里<a href='/print_driver/install_lodop32.exe' target='_self'>执行升级</a>,升级后请重新进入。</font>";
    var strHtm64_Install="<br><font color='#FF00FF'>打印控件未安装!点击这里<a href='/print_driver/install_lodop64.exe' target='_self'>执行安装</a>,安装后请刷新页面或重新进入。</font>";
    var strHtm64_Update="<br><font color='#FF00FF'>打印控件需要升级!点击这里<a href='/print_driver/install_lodop64.exe' target='_self'>执行升级</a>,升级后请重新进入。</font>";
    var strHtmFireFox="<br><br><font color='#FF00FF'>（注意：如曾安装过Lodop旧版附件npActiveXPLugin,请在【工具】->【附加组件】->【扩展】中先卸它）</font>";
    var strHtmChrome="<br><br><font color='#FF00FF'>(如果此前正常，仅因浏览器升级或重安装而出问题，需重新执行以上安装）</font>";
    var strCLodopInstall="<br><font color='#FF00FF'>打印服务(localhost本地)未安装启动!点击这里<a href='/print_driver/CLodopPrint_Setup_for_Win32NT.exe' target='_self'>执行安装</a>,安装后请刷新页面。</font>";
    var strCLodopUpdate="<br><font color='#FF00FF'>打印服务需升级!点击这里<a href='/print_driver/CLodopPrint_Setup_for_Win32NT.exe' target='_self'>执行升级</a>,升级后请刷新页面。</font>";
    var LODOP;
    try{
        var isIE = (navigator.userAgent.indexOf('MSIE')>=0) || (navigator.userAgent.indexOf('Trident')>=0);
        if (needCLodop()) {
            try{ LODOP=getCLodop();} catch(err) {};
	    if (!LODOP && document.readyState!=="complete") {alert("C-Lodop没准备好，请稍后再试！"); return;};
            if (!LODOP) {
		 if (isIE) document.write(strCLodopInstall); else
		 //document.documentElement.innerHTML=strCLodopInstall+document.documentElement.innerHTML;
             $("body").prepend(strCLodopInstall);
                 return;
            } else {
	         if (CLODOP.CVERSION<"2.0.4.0") {
			if (isIE) document.write(strCLodopUpdate); else
			document.documentElement.innerHTML=strCLodopUpdate+document.documentElement.innerHTML;
                //$("body").prepend(strCLodopInstall);
		 };
		 if (oEMBED && oEMBED.parentNode) oEMBED.parentNode.removeChild(oEMBED);
		 if (oOBJECT && oOBJECT.parentNode) oOBJECT.parentNode.removeChild(oOBJECT);
	    };
        } else {
            var is64IE  = isIE && (navigator.userAgent.indexOf('x64')>=0);
            //=====如果页面有Lodop就直接使用，没有则新建:==========
            if (oOBJECT!=undefined || oEMBED!=undefined) {
                if (isIE) LODOP=oOBJECT; else  LODOP=oEMBED;
            } else if (CreatedOKLodop7766==null){
                LODOP=document.createElement("object");
                LODOP.setAttribute("width",0);
                LODOP.setAttribute("height",0);
                LODOP.setAttribute("style","position:absolute;left:0px;top:-100px;width:0px;height:0px;");
                if (isIE) LODOP.setAttribute("classid","clsid:2105C259-1E0C-4534-8141-A753534CB4CA");
                else LODOP.setAttribute("type","application/x-print-lodop");
                document.documentElement.appendChild(LODOP);
                CreatedOKLodop7766=LODOP;
             } else LODOP=CreatedOKLodop7766;
            //=====Lodop插件未安装时提示下载地址:==========
            if ((LODOP==null)||(typeof(LODOP.VERSION)=="undefined")) {
                 if (navigator.userAgent.indexOf('Chrome')>=0)
                     document.documentElement.innerHTML=strHtmChrome+document.documentElement.innerHTML;
                 if (navigator.userAgent.indexOf('Firefox')>=0)
                     document.documentElement.innerHTML=strHtmFireFox+document.documentElement.innerHTML;
                 if (is64IE) document.write(strHtm64_Install); else
                 if (isIE)   document.write(strHtmInstall);    else
                     document.documentElement.innerHTML=strHtmInstall+document.documentElement.innerHTML;
                 return LODOP;
            };
        };
        if (LODOP.VERSION<"6.2.0.3") {
            if (needCLodop())
            document.documentElement.innerHTML=strCLodopUpdate+document.documentElement.innerHTML; else
            if (is64IE) document.write(strHtm64_Update); else
            if (isIE) document.write(strHtmUpdate); else
            document.documentElement.innerHTML=strHtmUpdate+document.documentElement.innerHTML;
            return LODOP;
        };
        //===如下空白位置适合调用统一功能(如注册语句、语言选择等):===
        //LODOP.SET_LICENSES("","C8171E2E3C575B2C45D4E538794A758C","C94CEE276DB2187AE6B65D56B3FC2848","");
        //===========================================================
        return LODOP;
    } catch(err) {alert("getLodop出错:"+err);};
};

//打印订单
function PrintOneURL(printUrl){
    LODOP=getLodop();
    if(typeof(LODOP) == 'undefined') {
        return ;
    }
    LODOP.SET_LICENSES("","C8171E2E3C575B2C45D4E538794A758C","C94CEE276DB2187AE6B65D56B3FC2848","");
    LODOP.PRINT_INIT("打印控件功能演示_Lodop功能_按网址打印");
    LODOP.ADD_PRINT_URL(0,0,"100%","100%", printUrl);
    LODOP.SET_PRINT_STYLEA(0,"HOrient",3);
    LODOP.SET_PRINT_STYLEA(0,"VOrient",3);
//		LODOP.SET_SHOW_MODE("MESSAGE_GETING_URL",""); //该语句隐藏进度条或修改提示信息
//		LODOP.SET_SHOW_MODE("MESSAGE_PARSING_URL","");//该语句隐藏进度条或修改提示信息
    LODOP.SET_PRINT_PAGESIZE(3,580,45,"");
    //LODOP.PREVIEW();
    LODOP.PRINT();
}

//打印证书
function PrintCertificateURL(printUrl,intOrient,intPageWidth,intPageHeight,strPageName){
    intOrient = intOrient || 3;
    LODOP=getLodop();
    if(typeof(LODOP) == 'undefined') {
        return ;
    }
    LODOP.SET_LICENSES("","C8171E2E3C575B2C45D4E538794A758C","C94CEE276DB2187AE6B65D56B3FC2848","");
    LODOP.PRINT_INIT("打印控件功能演示_Lodop功能_按网址打印");
    LODOP.ADD_PRINT_URL(0,0,"100%","100%", printUrl);
    LODOP.SET_PRINT_STYLEA(0,"HOrient",3);
    LODOP.SET_PRINT_STYLEA(0,"VOrient",3);
//		LODOP.SET_SHOW_MODE("MESSAGE_GETING_URL",""); //该语句隐藏进度条或修改提示信息
//		LODOP.SET_SHOW_MODE("MESSAGE_PARSING_URL","");//该语句隐藏进度条或修改提示信息
//     LODOP.SET_PRINT_PAGESIZE(3,580,45,"");
    LODOP.SET_PRINT_PAGESIZE(intOrient,intPageWidth,intPageHeight,strPageName);
    //LODOP.PREVIEW();
    LODOP.PRINT();
}
// https://www.it610.com/article/2094844.htm  打印函数LODOP.SET_PRINT_PAGESIZE
// SET_PRINT_PAGESIZE(intOrient,intPageWidth,intPageHeight,strPageName);
//
// 参数含义：
// intOrient：打印方向及纸张类型
// 值为1---纵向打印，固定纸张；
// 值为2---横向打印，固定纸张；
// 值为3---纵向打印，宽度固定，高度按打印内容的高度自适应；
// 0(或其它)----打印方向由操作者自行选择或按打印机缺省设置。
// intPageWidth：
// 纸张宽，单位为0.1mm 譬如该参数值为45，则表示4.5mm,计量精度是0.1mm。
//
// intPageHeight：
// 固定纸张时该参数是纸张高；高度自适应时该参数是纸张底边的空白高，计量单位与纸张宽一样。
//
// strPageName：
// 纸张名，必须intPageWidth等于零时本参数才有效，有如下选择：
// Letter, LetterSmall, Tabloid, Ledger, Legal,Statement, Executive,
//     A3, A4, A4Small, A5, B4, B5, Folio, Quarto, qr10X14, qr11X17, Note,
//     Env9, Env10, Env11, Env12,Env14, Sheet, DSheet, ESheet
