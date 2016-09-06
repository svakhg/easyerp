<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($cur_info['func_name']) ? $cur_info['func_name'] : ""; ?>-ERP系统</title>
    <meta content="" name="description" />
    <meta content="" name="keywords" />
    <meta property="qc:admins" content="42702303365131754636" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link href="<?php echo saFileUrl($config['srcPath'].'/css/lib/themes/metro/easyui.css')?>" rel="stylesheet" />
    <link href="<?php echo saFileUrl($config['srcPath'].'/css/lib/themes/icon.css')?>" rel="stylesheet" />
    <link href="<?php echo saFileUrl($config['srcPath'].'/css/lib/global.css')?>" rel="stylesheet" />
    <link href="<?php echo saFileUrl($config['srcPath'].'/css/lib/common.css')?>" rel="stylesheet" />
    <script type="text/javascript">
    var page_auth = <?php echo json_encode($page_permission); ?>;
    </script>
</head>
<body>
<div class="north-div">
    <label>易结网-ERP管理系统</label>
    <ul class="top-bar">
        <li><a href="<?php echo site_url('account/login/logout');?>" class="backsys"><i></i> 退出系统</a></li>
        <li><a href="javascript:void(0)" class="sysmsg"><i></i> 系统消息</a></li>
        <li><a href="javascript:void(0)" class="sysitd"><i></i> 系统介绍</a></li>
        <li><a href="javascript:void(0)" class="company"><i></i> 公司简介</a></li>
    </ul>
    <input type="hidden" id="curent_user" value="当前用户：<?php echo $this->session->userdata("admin");?>">
</div>
<div id="maincontent" class="clearfix delaybd">
<div class="west-div">
    <div class="west-div-header">菜单列表》</div>
    <div class="easyui-accordion" style="border:none;">
        <div title="首页" class="actitle">
            <ul>
                <li>
                    <a href="<?php echo site_url();?>" class="ahome"><i></i> 首页</a>
                </li>
            </ul>
        </div>
        <?php foreach($func_infos as $v):?>
            <div title="<?php echo $v['func_name'];?>" <?php if($v['id'] == $cur_info['pid']): ?>data-options="selected:true"<?php endif; ?> class="actitle">
                <ul>

                    <?php if(isset($v['func_two'])):?>
                        <?php foreach($v['func_two'] as $val):?>
                            <?php if($val['is_show']): ?>
                            <li><a href="<?php echo site_url($val['controller'].'/index');?>" class="<?php echo $val['style'];?>"><i></i> <?php echo $val['func_name'];?></a></li>
                            <?php endif; ?>
                        <?php endforeach;?>
                    <?php endif;?>
                </ul>
            </div>
        <?php endforeach;?>
    </div>
</div>
