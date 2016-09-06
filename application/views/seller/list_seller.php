        <div class="center-div">
            <div class="center-div-header">当前位置：【 自荐信管理 】</div>
            <div class="customrecordiv comct">
                <form id="demand_search" class="easyui-form" method="post" data-options="novalidate:true">
                    <table class="systemlogtb">
                        <tr>
                            <td class="tbfield">需求类型：</td>
                            <td>
							
                           <select id="cli_source" name="cli_source" class="selectbox com-width150 cli_source">
								<option value="">--请选择--</option>
								<option value="wedplanners">一站式</option>
								<option value="wedphotoer">婚礼摄影</option>
								<option value="wedmaster">主持人</option>
								<option value="wedvideo">婚礼摄像</option>
								<option value="sitelayout">场地布置</option>
								<option value="wedvenues">婚礼造型</option>
								
							<!--<?php foreach($list as $it){?>
								<option value="<?php echo $it['id']?>"><?php echo $it["name"]?></option>
									
							<?php };?>  -->
						</select>

                            </td>
                            <td class="tbfield">交易编号:</td>
                            <td>
                                <input class="textbox com-width150 condition_text" name="condition_number" maxlength="50" placeholder="请输入交易编号">
                            </td>
                        </tr>
                        <tr>
                            <td class="tbfield">关键字：</td>
                            <td colspan="3">
                                <input class="textbox condition_text com-width300" name="keywords" style="margin-right:30px;" maxlength="50" placeholder="交易编号/客户姓名//客户昵称/手机号码//QQ/微信">
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="easyui-linkbutton searchbtn c8 pl10 pr10 ml10" data-options="iconCls:'icon-search'">查 询</a>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="alia" id="filter" value="dai" />
                </form>
                <a href="javascript:void(0)" class="easyui-linkbutton examinesure" data-options="iconCls:'icon-ok'">审核通过</a>
                <a href="javascript:void(0)" class="easyui-linkbutton examineno" data-options="iconCls:'icon-no'">审核不通过</a>
				<label style="display:inline-block;vertical-align:middle;height:26px;"></label>
                <div class="filtertb">
					<ul>
						<li class="filtertb-on" id="dai">待审核（<?php echo $list[0];?>）</li>
						<li class="filtertb-off" id="ok">审核通过(<?php echo $list[1];?>)</li>
						<li class="filtertb-off" id="no">审核不通过(<?php echo $list[2];?>)</li>
					</ul>
				</div>
                <table id="cusrecord" class="datagrid"></table>
            </div>
        </div>
    </div>

 
   <div class="editpanel">
        <form class="easyuiform mult" method="post">
            <table class="ltr" style="width:600px">
                <tr>
                    <td colspan="4" class="pl10">
                      <a href="javascript:" class="easyui-linkbutton save c1 mb10 mr10 mt10 saveletter" data-options="iconCls:'icon-save'">保存</a> 
                        <a href="javascript:" class="easyui-linkbutton mb10 mr10 mt10 closepanel" data-options="iconCls:'icon-back'">关闭</a>
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">建议书编号：</td>
                    <td>
                        <input class="textbox com-width150 id" name="id" readonly="readonly">
                    </td>
                    <td class="tbfield">交易编号：</td>
                    <td>
                        <input class="textbox com-width150 order_id" name="order_id" readonly="readonly">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">商家名称：</td>
                    <td>
                        <input class="textbox com-width150 nickname" name="nickname" readonly="readonly">
                    </td>
                    <td class="tbfield">提交时间：</td>
                    <td>
                        <input class="textbox com-width150 time_21" name="time_21" readonly="readonly">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">需求类型：</td>
                    <td>
                        <input class="textbox com-width150 shopper_alias" name="shopper_alias" readonly="readonly">
                    </td>
                    <td class="tbfield">建议书状态：</td>
                    <td>
                        <input class="textbox com-width150 status" name="status" readonly="readonly">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">接单意愿：</td>
                    <td colspan="3">
                        <select id="wish" name="wish" class="selectbox com-width150 wish">
							<option value="非常强烈">非常强烈</option>
							<option value="希望争取">希望争取</option>
							<option value="意愿一般">意愿一般</option>
						</select>
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">易结店铺：</td>
                    <td colspan="3">
                        <input class="textbox com-width150 studio_name" name="studio_name" readonly="readonly">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">我的优势：</td>
                    <td colspan="3">
                        <textarea class="custextarea com-autowidth advantage" name="advantage"></textarea>
                    </td>
                </tr>
				<tr>
                    <td class="tbfield">方案建议：</td>
                    <td colspan="3">
                        <textarea class="custextarea com-autowidth file_advise" name="file_advise"></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="pl20" colspan="4">推荐的服务套餐：</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table class="smtb" style="width:600px">
                            <tr>
                                <th>服务内容</th>
                                <th>服务报价</th>
                                <th>附加说明</th>
                            </tr>
                            <tr>
                                <td class="text-right">
                                    <div>
										<label>套餐一：</label>
										<input class="textbox com-width250 prices_service_type" name="prices_service_type" maxlength="30" placeholder="请输入套餐名" readonly="readonly">
									</div>
                                    <div>
										<label>价格：</label>
										<input class="textbox com-width250 prices_grabprice" name="prices_grabprice" maxlength="30" placeholder="请输入价格" readonly="readonly">
									</div>
                                    <div>
										<label>套餐说明：</label>
										<textarea class="custextarea com-width250 prices_service " name="prices_service" readonly="readonly"></textarea>
									</div>
                                </td>
                                <td>
									<input class="textbox com-width60 prices_price" name="prices_price" maxlength="30" readonly="readonly">
                                </td>
                                <td>
                                    <textarea class="custextarea com-width150 prices_reason" name="prices_reason" maxlength="200" placeholder="请输入附加说明" readonly="readonly"></textarea>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="id" />
            <input type="hidden" name="experience" />
            <input type="hidden" name="opus" />
			<input type="hidden" name="file_name" />
            <input type="hidden" name="file_url" />
			<input type="hidden" name="prices_service_type" />
			<input type="hidden" name="prices_service" />
			<input type="hidden" name="prices_reason" />
			<input type="hidden" name="prices_price" />
			<input type="hidden" name="prices_grabprice" />
        </form>
    </div>
    <div class="editpanel">
        <form class="easyuiform single" method="post">
            <table class="ltr">
                <tr>
                    <td colspan="4" class="pl10">
                        <a href="javascript:" class="easyui-linkbutton save c1 mb10 mr10 mt10 saveletter" data-options="iconCls:'icon-save'">保存</a>
                        <a href="javascript:" class="easyui-linkbutton mb10 mr10 mt10 closepanel" data-options="iconCls:'icon-back'">关闭</a>
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">建议书编号：</td>
                    <td>
                        <input class="textbox com-width150 id" name="id" readonly="readonly">
                    </td>
                    <td class="tbfield">交易编号：</td>
                    <td>
                        <input class="textbox com-width150 order_id" name="order_id" readonly="readonly">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">商家名称：</td>
                    <td>
                        <input class="textbox com-width150 nickname" name="nickname" readonly="readonly">
                    </td>
                    <td class="tbfield">提交时间：</td>
                    <td>
                        <input class="textbox com-width150 create_time" name="create_time" readonly="readonly">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">需求类型：</td>
                    <td>
                        <input class="textbox com-width150 shopper_alias" name="shopper_alias" readonly="readonly">
                    </td>
                    <td class="tbfield">建议书状态：</td>
                    <td>
                        <input class="textbox com-width150 status" name="status" readonly="readonly">
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">接单意愿：</td>
                    <td colspan="3">
                        <select id="wish" name="wish" class="selectbox com-width150 wish">
							<option value="非常强烈">非常强烈</option>
							<option value="希望争取">希望争取</option>
							<option value="意愿一般">意愿一般</option>
						</select>
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">个人介绍：</td>
                    <td colspan="3">
                        <textarea id="aboutme" class="custextarea com-autowidth aboutme" name="aboutme" readonly="readonly"></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">易结店铺：</td>
                    <td colspan="3">
                        <input class="textbox com-autowidth studio_name" name="studio_name" readonly="readonly">
                    </td>
                </tr>
                <tr>
                    <td class="pl10" colspan="4">承办婚礼经验</td>
                </tr>
                <tr>
                    <td class="tbfield">婚礼链接：</td>
                    <td colspan="3"><input class="textbox com-autowidth opus" maxlength="50" placeholder="请输入婚礼链接" name="opus"></td>
                </tr>
                <tr>
                    <td class="tbfield">描述说明：</td>
                    <td colspan="3">
                        <textarea class="custextarea com-autowidth experience" maxlength="30" placeholder="描述说明" name="experience"></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">我的优势：</td>
                    <td colspan="3">
                        <textarea class="custextarea com-autowidth advantage" maxlength="30" placeholder="我的优势" name="advantage"></textarea>
                    </td>
                </tr> 
                <tr>
                    <td class="tbfield">方案建议：</td>
                    <td colspan="3">
                        <textarea class="custextarea com-autowidth file_advise" maxlength="30" placeholder="婚礼方案建议" name="file_advise"></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="tbfield">附件URL：</td>
                    <td colspan="3"><input class="textbox com-autowidth file_url" maxlength="50" placeholder="请输入婚礼链接" name="file_url"></td>
                </tr>
                <tr>
                    <td class="tbfield">方案建议附件：</td>
                    <td colspan="3">
                        <a class="annex file_url" href="javascript:void(0)">下载附件</a>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="id" />
            <input type="hidden" name="file_name" />
        </form>
    </div>
<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
       seajs.use("<?php echo $config['srcPath'];?>/js/trademanage/lettermanage");
</script>
</body>
</html>