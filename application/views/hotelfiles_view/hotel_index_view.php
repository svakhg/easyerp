<!doctype html>
<html style="background:#666;">
<head>
    <meta charset="UTF-8">
    <title>酒店档案</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />
    <meta property="qc:admins" content="42702303365131754636" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link href="../../src/css/lib/themes/black/easyui.css" rel="stylesheet" />
    <link href="../../src/css/lib/themes/icon.css" rel="stylesheet" />
    <link href="../../src/css/lib/themes/color.css" rel="stylesheet" />
    <link href="../../src/css/lib/global.css" rel="stylesheet" />
    <link href="../../src/css/lib/common.css" rel="stylesheet" />
</head>
<body class="easyui-layout">
    <div class="north-div" data-options="region:'north',border:false" style="color:#fff;">
        易结网-ERP管理系统<ul class="top-bar">
            <li><a href="../account/login.html" class="backsys"><i></i> 退出系统</a></li>
            <li><a href="javascript:void(0)" class="sysmsg"><i></i> 系统消息</a></li>
            <li><a href="javascript:void(0)" class="sysitd"><i></i> 系统介绍</a></li>
            <li><a href="javascript:void(0)" class="company"><i></i> 公司简介</a></li>
        </ul>
    </div>
    <div class="west-div" data-options="region:'west',split:false,title:'菜单列表'">
        <div class="easyui-accordion delaybd" style="border:none;">
            <a href="home.html" class="panel-header ahome"><i></i> 首页</a>
            <div title="交易管理" class="actitle">
                <ul>
                    <li><a href="../trademanage/includneed.html" class="includneed"><i></i> 录入客户需求</a></li>
                    <li><a href="../trademanage/perfectneed.html" class="perfectneed"><i></i> 待完善需求</a></li>
                    <li><a href="../trademanage/examineneed.html" class="examineneed"><i></i> 待审核需求</a></li>
                    <li><a href="../trademanage/tobeplanner.html" class="tobeplanner"><i></i> 待选策划师</a></li>
                    <li><a href="../trademanage/tochoseplanner.html" class="tochoseplanner"><i></i> 待确认策划师</a></li>
                    <li><a href="../trademanage/tobesigned.html" class="tobesigned"><i></i> 待签约</a></li>
                    <li><a href="../trademanage/signed.html" class="signed"><i></i> 已签约</a></li>
                    <li><a href="../trademanage/completed.html" class="completed"><i></i> 已完成</a></li>
                    <li><a href="../trademanage/lostbill.html" class="lostbill"><i></i> 已丢单</a></li>
                    <li><a href="../trademanage/needsearch.html" class="needsearch"><i></i> 客户需求查询</a></li>
                </ul>
            </div>
            <div title="客户关系管理" class="actitle">
                <ul>
                    <li><a href="../customer/customarker.html" class="customarker"><i></i> 客户标签</a></li>
                    <li><a href="../customer/customgroup.html" class="customgroup"><i></i> 客户分组</a></li>
                    <li><a href="../customer/customrecord.html" class="customrecord"><i></i> 客户档案</a></li>
                </ul>
            </div>
            <div title="基础档案管理" data-options="selected:true" class="actitle">
                <ul>
                    <li><a href="../basisarchives/hotelarchives.html" class="hotelfiles"><i></i> 酒店档案</a></li>
                </ul>
            </div>
            <div title="系统管理" class="actitle">
                <ul>
                    <li><a href="../systemanage/authorityconfig.html" class="basicsetting"><i></i> 权限配置</a></li>
                    <li><a href="../systemanage/basicsetting.html" class="basicsetting"><i></i> 基础设置</a></li>
                    <li><a href="../systemanage/employmagage.html" class="employmagage"><i></i> 员工管理</a></li>
                    <li><a href="../systemanage/trademark.html" class="trademark"><i></i> 交易标记设置</a></li>
                    <li><a href="../systemanage/rolemanage.html" class="rolemanage"><i></i> 角色管理</a></li>
                    <li><a href="../systemanage/systemlog.html" class="systemlog"><i></i> 系统日志</a></li>
                    <li><a href="../systemanage/updatepwd.html" class="updatepwd"><i></i> 修改密码</a></li>
                </ul>
            </div>
            <div title="敬请期待" class="actitle">
                <ul>
                    <li><a href="javascript:void(0)">敬请期待</a></li>
                    <li><a href="javascript:void(0)">敬请期待</a></li>
                    <li><a href="javascript:void(0)">敬请期待</a></li>
                    <li><a href="javascript:void(0)">敬请期待</a></li>
                    <li><a href="javascript:void(0)">敬请期待</a></li>
                    <li><a href="javascript:void(0)">敬请期待</a></li>
                    <li><a href="javascript:void(0)">敬请期待</a></li>
                    <li><a href="javascript:void(0)">敬请期待</a></li>
                    <li><a href="javascript:void(0)">敬请期待</a></li>
                    <li><a href="javascript:void(0)">敬请期待</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="south-div" data-options="region:'south',border:false"><div class="delaybd">&copy; 2014易结 <a href="http://www.miitbeian.gov.cn/" rel="nofollow" target="_blank">京ICP备11044017号</a></div></div>
    <div class="center-div" data-options="region:'center',title:'当前位置：【 系统管理 &nbsp;&gt;&nbsp; 酒店档案 】'">
        <div class="customarkerdiv delaybd">
            <input class="hotelarc-searchbox">&nbsp;&nbsp;
            <a href="javascript:void(0)" class="easyui-linkbutton c1 com-width100">导出</a>
            <a href="javascript:void(0)" class="easyui-linkbutton c5 com-width100">导入</a><br /><br />
            <a href="javascript:void(0)" class="easyui-linkbutton addhotelarc" data-options="iconCls:'icon-add'">添加客户标签</a>
            <a href="javascript:void(0)" class="easyui-linkbutton used" data-options="iconCls:'icon-ok'">启用</a>
            <a href="javascript:void(0)" class="easyui-linkbutton unused" data-options="iconCls:'icon-no'">停用</a><br /><br />
            <table id="hotelarc" class="datagrid" style="width:100%;">
                <thead>
                    <tr>
                        <th data-options="field:'id',width:30"></th>
                        <th data-options="field:'',width:80,checkbox:true"></th>
                        <th data-options="field:'hotelname',width:100,sortable:true">酒店名称</th>
                        <th data-options="field:'hotelbrand',width:80,sortable:true">酒店品牌</th>
                        <th data-options="field:'hotelgrade',width:100">酒店星级</th>
                        <th data-options="field:'country',width:100">国家</th>
                        <th data-options="field:'province',width:100">省份</th>
                        <th data-options="field:'city',width:100">城市</th>
                        <th data-options="field:'dtaddress',width:100">详细地址</th>
                        <th data-options="field:'explain',width:250">说明</th>
                        <th data-options="field:'operate',width:120">操作</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="edit" class="editrecord hotelrecord">
        <form class="easyuiform" method="post">
            <table>
                <tr>
                    <td class="tbfield"><span class="flag">*</span> 酒店名称：</td>
                    <td><input class="textbox" data-options="required:true,validType:'mobile'" maxlength="30" placeholder="请输入酒店名称"></td>
                    <td class="tbfield">酒店品牌：</td>
                    <td><input class="textbox" data-options="required:true,validType:'QQ'" maxlength="30" placeholder="请输入酒店品牌"></td>
                </tr>
                <tr>
                    <td class="tbfield">星级：</td>
                    <td colspan="3">
                        <select class="selectbox com-width150">
                            <option value="1">一星级</option>
                            <option value="2">二星级</option>
                            <option value="3">三星级</option>
                            <option value="4">四星级</option>
                            <option value="5">五星级</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">通讯地址：</td>
                    <td colspan="3" class="linkage">
                        <select class="com-width60 selectbox">
                            <option value="AL">中国</option>
                            <option value="AK">日本</option>
                            <option value="AK">韩国</option>
                        </select>
                        <select class="com-width100 selectbox">
                            <option value="AL">北京</option>
                            <option value="AK">上海</option>
                            <option value="A2">广州</option>
                        </select>
                        <select class="com-width150 selectbox">
                            <option value="AL">昌平区</option>
                            <option value="AK">回龙观</option>
                            <option value="AK">朝阳区</option>
                        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">详细地址：</td>
                    <td colspan="3"><input class="textbox com-width250" data-options="required:true" maxlength="100" placeholder="详细地址"></td>
                </tr>
                <tr>
                    <td class="tbfield vert-top">备注：</td>
                    <td colspan="3"><textarea class="custextarea com-autowidth" data-options="required:true" maxlength="30" placeholder="请输入备注"></textarea></td>
                </tr>
            </table>
        </form>
    </div>
    <div id="loading" style="text-align:center;color:white;padding:100px;font-size:18px;">加载中......</div>
    <div class="smblock">
        <div class="content"></div>
        <div class="bottom-btn">
            <a class="easyui-linkbutton btnok subbtn" data-options="iconCls:'icon-ok'">确定</a>
            <a class="easyui-linkbutton btncancel" data-options="iconCls:'icon-cancel'">取消</a>
        </div>
    </div>
    <div id="waitingdiv" class="waitingdiv"><img src="../../src/images/loading.gif" /><label>请稍后。。。</label></div>
    <div id="msgdiv" class="msgdiv"></div>
    <div id="modalbg" class="modalbg"></div>

    <script src="../../src/js/seajs/sea.js"></script>
    <script src="../../src/js/seajs/sea-config.js"></script>
    <script type="text/javascript">
        seajs.use("../../src/js/basisarchives/hotelarchives");
    </script>
</body>
</html>
