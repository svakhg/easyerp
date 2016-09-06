    <!-- 右侧主体开始-->
    <div class="center-div">
        <div class="center-div-header">当前位置：【 商机管理  >  商机列表 】</div>
        <div class="customrecordiv comct">
            <form id="demand_search" class="easyui-form" method="get" data-options="novalidate:true">
                <table class="systemlogtb">
                    <tbody>
                    <tr>
                        <td class="tbfield">商机状态：</td>
                        <td>
                            <select id="status_1" name="status_1" class="selectbox com-width80 status_1">
                                <option value="">请选择</option>
                                <option selected value="0">全部</option>
                                <option value="1">新增</option>
                                <option value="3">跟进中</option>
                                <option value="101">无效信息</option>
								<option value="7">已建单</option>
								<option value="20">已分单n进4</option>
                                <option value="6">已分单4进2</option>
                            </select>
                            <select id="status_2" name="status_2" class="selectbox com-width80 status_2"></select>
                        </td>
                        <td class="tbfield">商机类型：</td>
                        <td>
                            <select id="ordertype" name="ordertype" class="selectbox com-width80 c_type">
                                <option value="">全部</option>
                                <?php foreach ($ordertype as $key => $val): ?>
                                <option value="<?php echo $val; ?>"><?php echo $ordertype_explan[$key]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="tbfield">商机来源：</td>
                        <td>
                            <select id="source" name="source" class="selectbox com-width120 c_form">
                                <option value="">全部</option>
                                <?php foreach ($source as $key => $val): ?>
                                <option value="<?php echo $val; ?>"><?php echo $source_explan[$key]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="tbfield">提交时间：</td>
                        <td><input id="creat_time_start" name="creat_time_start" class="datetimebox com-width120 creat_time_start"></td>
                        <td class="singlefw">至</td>
                        <td><input id="creat_time_end" name="creat_time_end" class="datetimebox com-width120 creat_time_end"></td>
                    </tr>
                    <tr>
                        <td class="tbfield">新人顾问：</td>
                        <td>
                            <select id="follower_uid" name="follower_uid" class="selectbox com-width80 c_consultant">
                                <option value="">全部</option>
                                <?php foreach ($adviser as $key => $value): ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['username']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <!-- <td class="tbfield">客户手机：</td>
                        <td>
                            <input class="textbox condition_text com-width150" name="mobile" placeholder="">
                        </td>
                        <td class="tbfield">客户姓名：</td>
                        <td>
                            <input class="textbox condition_text com-width150" name="username" placeholder="">
                        </td> -->
                        <td class="tbfield">运营：</td>
                        <td>
                            <select id="operate_uid" name="operate_uid" class="selectbox com-width80 c_consultant">
                                <option value="">全部</option>
                                <?php foreach ($operater as $key => $value): ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['username']; ?></option>
                                <?php endforeach; ?>
								 <option value="99">为空</option>
                            </select>
                        </td>
                        <td class="tbfield">客户类型：</td>
                        <td>
                            <select id="usertype" name="usertype" class="selectbox com-width120 con_type">
                                <option value="">全部</option>
                                <?php foreach($usertype as $val): ?>
                                <option value="<?php echo $val; ?>"><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="tbfield">婚礼日期：</td>
                        <td><input id="wed_time_start" name="wed_time_start" class="datebox com-width120 wed_time_start"></td>
                        <td class="singlefw">至</td>
                        <td><input id="wed_time_end" name="wed_time_end" class="datebox com-width120 wed_time_end"></td>
                    </tr>
					<tr>
					   <td colspan="5">分配顾问时间：<input id="adviser_time_start" name="adviser_time_start" class="datebox com-width120 wed_time_start"> <span class="tc" style="width: 30px;display: inline-block">至</span><input id="adviser_time_end" name="adviser_time_end" class="datebox com-width120 wed_time_end"></td>
                        
                        <td colspan="5">分配运营时间：<input id="operate_time_start" name="operate_time_start" class="datebox com-width120 wed_time_start"> <span class="tc" style="width: 30px;display: inline-block">至</span><input id="operate_time_end" name="operate_time_end" class="datebox com-width120 wed_time_end"></td> 
					</tr>
                    <tr>
                        <td class="tbfield">条件选择：</td>
                        <td>
                            <select id="cond_type" class="selectbox com-width80">
                                <option value="mobile" selected>客户手机</option>
                                <option value="username">客户姓名</option>
                                <option value="tradeno">交易编号</option>
                            </select>
                            <input class="textbox condition_text com-width80" id="cond_type_choose" placeholder="">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10">
                            <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c7" data-options="iconCls:'icon-search'">查询</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton c7 auth-none" data-options="iconCls:'icon-ok'" id="distConsultant" data-auth="business/demand/adviserbutton">分配顾问</a>
                            <?php if($admin['is_hunliguwenzhuguan'] == 1){ ?>
                            <a href="javascript:void(0)" class="easyui-linkbutton c7 auth-none" data-options="iconCls:'icon-ok'" id="distOperateBtn" data-auth="business/demand/operatebutton">分配运营</a>
                            <?php } ?>
                            <a href="/business/demand/add" class="easyui-linkbutton c7" data-options="iconCls:'icon-add'" >添加新商机</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton c7" data-options="iconCls:'icon-redo'" id="exportBtn">导 出</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name="shopper_alias" id="filter">
            </form>

            <table id="businesslist" class="datagrid"></table>
        </div>
    </div>
    <!-- 右侧主体结束-->
</div>

<!--各种弹窗
    通过JS调用.
-->

<!-- 分配顾问的弹层 -->
<div id="distlayer" class="recommend">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td class="tbfield">跟进顾问：</td>
                <td>
                    <select class="selectbox com-width200" id="dist_consultant" name="dist_consultant" data-options="required:true">
                        <option value="">--请选择--</option>
                        <?php foreach ($adviser as $key => $value): ?>
                        <option value="<?php echo $value['id']; ?>"><?php echo $value['username']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- 分配顾问的弹层 -->

<!-- 分配运营的弹层 -->
<div id="distOperateWindow" class="recommend">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td class="tbfield">运营人员：</td>
                <td>
                    <select class="selectbox com-width200" id="dist_operate" name="dist_operate" data-options="required:true">
                        <option value="">--请选择--</option>
                        <?php foreach ($operater as $key => $value): ?>
                        <option value="<?php echo $value['id']; ?>"><?php echo $value['username']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- 分配运营的弹层 -->

<?php $this->load->view('header/footer_view.php');?>

<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/business/list");
</script>
</body>
</html>