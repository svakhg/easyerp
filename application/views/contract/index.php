 <!-- 右侧主体开始-->
    <div class="center-div">
        <div class="center-div-header">当前位置：【 合同处理  >  合同处理列表 】</div>
        <div class="customrecordiv comct">
            <form id="demand_search" class="easyui-form" method="get" data-options="novalidate:true">
                <table class="systemlogtb">
                    <tbody>
                    <tr>
                        <td class="tbfield">合同状态：</td>
                        <td>
                            <select id="con_type" name="contract_status" class="selectbox com-width150">
                                <option selected value="">全部</option>
                                <?php foreach ($contractstatus as $key => $val) : ?>
								<option value="<?php echo $val;?>"><?php echo $contractstatus_explan[$key];?></option>
								<?php endforeach;?>
                            </select>
                        </td>
						 <td class="tbfield">签约方式：</td>
                        <td>
                            <select id="type" name="type" class="selectbox com-width50 c_consultant">
                                <option value="">全部</option>
                                <option value='1'>三方</option>
                                <option value='2'>双方</option>
                            </select>
                        </td>
						 <td class="tbfield">付款状态：</td>
                        <td>
                            <select id="payment_status" name="payment_status" class="selectbox com-width150">
                                <option selected value="">全部</option>
                                <option value="1">未付款</option>
                                <option value="2">已付款</option>
                            </select>
                        </td>
                        <td class="tbfield">签约生效时间：</td>
                        <td><input id="creat_time_start" name="sign_time_start" class="datetimebox com-width150"></td>
                        <td class="singlefw">至</td>
                        <td><input id="creat_time_end" name="sign_time_end" class="datetimebox com-width150"></td>
                    </tr>
                    <tr>
                       


<!--                        <td class="tbfield">婚礼场地：</td>-->
<!--                        <td>-->
<!--                            <input id="address" class="textbox condition_text com-width150" name="wed_place">-->
<!--                        </td>-->
<!--                        <td class="tbfield">商家昵称：</td>-->
<!--                        <td>-->
<!--                            <input id="nickname" class="textbox condition_text com-width150" name="shopper_name">-->
<!--                        </td>-->
					<td class="tbfield">合同渠道：</td>
                        <td>
                            <select id="offline" name="offline" class="selectbox com-width50 c_consultant">
                                <option value="">全部</option>
                                <option value='0'>线上</option>
							    <option value='1'>线下</option>
							</select>
                        </td>
						<td class="tbfield">归档状态：</td>
                        <td>
                            <select id="file_status" name="archive_status" class="selectbox com-width150">
                                <option selected value="">全部</option>
                                <?php foreach ($archivestatus as $key => $val) : ?>
                                <option value="<?php echo $val;?>"><?php echo $archivestatus_explan[$key];?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
						<td class="tbfield">返款状态：</td>
                        <td>
                            <select id="amount_status" name="funds_status" class="selectbox com-width150">
                                <option selected value="">全部</option>
                                <option value="<?php echo $fundstatus["first_back"]?>"><?php echo $fundstatus_explan["first_back"]?></option>
								<option value="<?php echo $fundstatus["already_first_back"]?>"><?php echo $fundstatus_explan["already_first_back"]?></option>
                                <option value="<?php echo $fundstatus["remainder_back"]?>"><?php echo $fundstatus_explan["remainder_back"]?></option>
                                <option value="<?php echo $fundstatus["all_back"]?>"><?php echo $fundstatus_explan["all_back"]?></option>
                            </select>
                        </td>
                        
                        <td class="tbfield">婚礼日期：</td>
                        <td><input id="wed_time_start" name="wed_date_start" class="datebox com-width150 wed_time_start"></td>
                        <td class="singlefw">至</td>
                        <td><input id="wed_time_end" name="wed_date_end" class="datebox com-width150 wed_time_end"></td>
                    </tr>
                    <tr>
						<td class="tbfield">运营：</td>
                        <td>
                            <select id="operate_uid" name="operate_uid" class="selectbox com-width80 c_consultant">
                                <option value="">全部</option>
                                <?php foreach ($operater as $key => $value): ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['username']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
						
						<!-- 
						<td class="tbfield">新人顾问：</td>
                        <td>
                            <select id="follower_uid" name="follower_uid" class="selectbox com-width150">
                                <option value="">全部</option>
								<?php foreach ($adviser as $key => $value): ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['username']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td> -->
                        <td class="tbfield">条件选项：</td>
                        <td colspan="3">
                            <select id="options1" class="selectbox com-width100">
								 <option value="">--请选择--</option>
                                 <option value="mobile" selected>客户手机</option>
                                <option value="username">客户姓名</option>
                                <option value="contract_num">合同编号</option>
                                <option value="tradeno">交易编号</option>
                            </select>
                            <input class="textbox condition_text com-width150" id="options2">
                        </td>
					
                       
                    </tr>
					
                    <tr>
                        <td colspan="10">
                            <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c7" data-options="iconCls:'icon-search'">查询</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton resetbtn c7">重 置</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton c7 auth-none" data-options="iconCls:'icon-redo'" id="exportBtn" data-auth="api/contract/exportcsv">导 出</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" name="shopper_alias" id="filter">
            </form>

            <table id="contractlist" class="datagrid"></table>
        </div>
    </div>
    <!-- 右侧主体结束-->
</div>

<!--各种弹窗
    通过JS调用.
-->

<!-- 添加收支记录的弹层 -->
<div id="distlayer" class="recommend">
    <form class="easyuiform" method="post">
        <table>
            <tr>
                <td class="tbfield">跟进顾问：</td>
                <td>
                    <select class="selectbox com-width150" name="dist_consultant" id="dist_consultant" data-options="required:true">
                        <option value="">--请选择--</option>
                        <option value='定金'>定金</option>
                        <option value='首款'>首款</option>
                        <option value='尾款'>尾款</option>
                    </select>
                </td>
            </tr>
        </table>
    </form>
</div>
<!-- 添加收支记录的弹层 -->

<!--驳回弹层-->
<div id="rejectlayer" class="examinedlg">
    <form class="easyuiform" id="add_memo_form" method="post" data-options="novalidate:true">
        <table class="bsnstb">
            <tr>
                <td colspan="2" class="pb10" style="color: red;font-weight: bold">提示：驳回原因商家可见！</td>
            </tr>
            <tr>
                <td class="vert-top tl">驳回内容：</td>
                <td>
                    <textarea id="rejectText" class="custextarea com-width300" style="height: 70px;" data-options="required:true" name="memo_text" maxlength="500" placeholder="请输入驳回原因"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>



<input type="hidden" id="cur_page" value="1" />
<input type="hidden" id="cur_pagesize" value="20" />

<?php $this->load->view('header/footer_view.php');?>

<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/contract/contractProcess");
</script>
</body>
</html>