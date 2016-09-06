<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" /><meta property="qc:admins" content="42702303365131754636" />
    
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link href="<?php echo saFileUrl($config['srcPath'].'/css/lib/themes/black/easyui.css')?>" rel="stylesheet" />
    <link href="<?php echo saFileUrl($config['srcPath'].'/css/lib/themes/icon.css')?>" rel="stylesheet" />
    <link href="<?php echo saFileUrl($config['srcPath'].'/css/lib/themes/color.css')?>" rel="stylesheet" />
    <link href="<?php echo saFileUrl($config['srcPath'].'/css/lib/global.css')?>" rel="stylesheet" />
    <link href="<?php echo saFileUrl($config['srcPath'].'/css/lib/common.css')?>" rel="stylesheet" />
    <link href="<?php echo saFileUrl($config['srcPath'].'/css/account/login.css')?>" rel="stylesheet" />
</head>
<body class="loginbd">
<!--    <div style="position:fixed;top:0;height:30px;padding:10px;">-->
<!--        <span style="display:inline-block;width:10px;height:10px;background:#9A4E5A;border:1px solid #000;cursor:pointer;" class="bg2"></span>-->
<!--        <span style="display:inline-block;width:10px;height:10px;background:#333365;border:1px solid #000;cursor:pointer;" class="bg1"></span>-->
<!--        <span style="display:inline-block;width:10px;height:10px;background:#BBEAF3;border:1px solid #000;cursor:pointer;" class="bg"></span>-->
<!--    </div>-->
    <div class="lgbox">
        <div><label>易结网-ERP管理系统 v1.0</label></div>
        <form id="subform" method="post" action="/account/login/sign_in">
			<label for="username">手机号：</label><input class="easyui-validatebox textbox com-width200" type="text" name="username" id = "username" placeholder="请输入手机号" data-options="required:true,novalidate:true" maxlength="30"><br />

            <label for="password">密码：</label><input class="easyui-validatebox textbox com-width200" type="password" name="password" data-options="required:true,validType:'safepass',novalidate:true" placeholder="请输入密码" maxlength="16"><br>
			<label for="code">验证码：</label><input class="easyui-validatebox textbox com-width100" type="text" name="code"  placeholder="请输入短信验证码">
            <input id="getcode" class="easyui-validatebox" type="button" value="获取短信验证码"><br>

<!--			<a class="easyui-linkbutton">获取验证码</a>
			<input class="easyui-validatebox textbox com-width100" type="text" name="code" data-options="required:true" placeholder="请输入验证码" maxlength="16">-->
			
			
			<?php if($this->session->userdata('log_status') == 'logout') : ?>
			<font color="red">用户名或密码错误</font>
			<?php elseif($this->session->userdata('log_status') == 'validate') : ?>
			<font color="red">验证码不正确</font>
			<?php endif;?>
		</form>
        <div>
            <a class="easyui-linkbutton subbtn c1">登 录</a>
            <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
        </div>
		
    </div>
    <!-- 加载全站js -->
    <script src="<?php echo saFileUrl($config['srcPath'].'/js/seajs/sea.js')?>"></script>
    <script src="<?php echo saFileUrl($config['srcPath'].'/js/seajs/sea-config.js')?>"></script>
    <script type="text/javascript">
        seajs.use("<?php echo $config['srcPath'];?>/js/account/login");
    </script>
</body>
</html>
