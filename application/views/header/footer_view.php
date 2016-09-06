<div class="south-div delaybd">&copy; 2014易结 <a href="http://www.miitbeian.gov.cn/" rel="nofollow" target="_blank">京ICP备11044017号</a></div>
<div class="smblock">
    <div class="content"></div>
    <div class="bottom-btn">
        <a class="easyui-linkbutton btnok subbtn" data-options="iconCls:'icon-ok'">确定</a>
        <a class="easyui-linkbutton btncancel" data-options="iconCls:'icon-cancel'">关闭</a>
    </div>
</div>
<div id="message_info" class="sysinfodlg">
    <div id="message_info_content">
        <h4>任务提醒：</h4><div class="lbox"><b>客户需求</b><br><label class="demand">待审核：</label><br><label class="recomment">自荐信：</label><br></div><div class="rbox"><b>商家需求</b><br><label class="shopper_demand">待审核：</label><br><label class="shopper_letter">意向书：</label><br></div>
    </div>
    <img class="message_info_img" src="<?php echo $config['srcPath'];?>/images/loading.gif" />
</div>
<div id="loading">加载中......</div>
<div id="waitingdiv" class="waitingdiv"><img src="<?php echo $config['srcPath'];?>/images/loading.gif" /><label>请稍候。。。</label></div>
<div id="msgdiv" class="msgdiv"></div>
<div id="modalbg" class="modalbg"></div>
<script id="seajsnode"  src="<?php echo saFileUrl($config['srcPath'].'/js/seajs/sea.js')?>"></script>
<script src="<?php echo saFileUrl($config['srcPath'].'/js/seajs/sea-config.js')?>"></script>
