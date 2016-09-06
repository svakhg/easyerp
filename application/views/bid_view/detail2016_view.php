<div class="center-div">
    <div class="center-div-header">当前位置：【 商家招投标管理  >  需求详情页 】</div>
    <div class="comct">
        <div id="maincontent">
            <div id="systemlogtab" class="easyui-tabs" style="overflow:visible; width: 99%;">
                <a href="javascript:history.go(-1)" class="easyui-linkbutton mb10 mr10 mt10" data-options="iconCls:'icon-back'">返 回</a>
                
                <table class="label-tdstyle">
                    <tbody>
                        <tr>
                            <td class="tbfield">交易编号：</td>
                            <td><?php echo isset($number) ? $number : "" ;?></td>
                            
                            <td class="tbfield">交易状态：</td>
                            <td><?php echo isset($status_explan) ? $status_explan : "" ;?></td>
                            
                            <td class="tbfield">需求类型：</td>
                            <td><?php echo isset($shopper_alias_explan) ? $shopper_alias_explan : "" ;?></td>
                            
                            <td class="tbfield">提交时间：</td>
                            <td id="submit_time"><?php echo isset($create_time) ? $create_time : "" ;?></td>
                            
                        </tr>
                        <?php if($status_alias == "close"){ ?>
                        <tr>
                            <td class="tbfield">关闭时间：</td>
                            <td><?php echo isset($close_time) ? $close_time : "" ;?></td>
                            
                            <td class="tbfield">关闭原因：</td>
                            <td><?php echo isset($close_reason) ? $close_reason : "" ;?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div title="基本信息" class="lftab">
                    <div class="tabcont">
                        <div class="modulars">
                            <table>
                                <tr>
                                    <td class="vert-top">商家ID：</td>
                                    <td>
                                        <label><?php echo isset($uid) ? $uid : "" ;?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vert-top">商家昵称：</td>
                                    <td>
                                        <label><?php echo isset($nickname) ? $nickname : "" ;?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vert-top">商家类型：</td>
                                    <td>
                                        <label><?php echo isset($shoper_alias) ? $shoper_alias : "" ;?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vert-top">店铺名称：</td>
                                    <td>
                                        <label><?php echo isset($studio_name) ? $studio_name : "" ;?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vert-top">手机号码：</td>
                                    <td>
                                        <label><?php echo isset($user_phone) ? $user_phone : "" ;?></label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div title="婚礼需求" class="lftab">
                    <div class="tabcont">
                        <div class="modulars">
                            <table>
                                <tr>
                                    <td class="vert-top">婚礼日期：</td>
                                    <td>
                                        <label><?php echo isset($wed_date) ? $wed_date : "" ;?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vert-top">婚礼地点：</td>
                                    <td>
                                        <label><?php echo isset($wed_place_explan) ? $wed_place_explan : "" ;?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vert-top">婚礼场地：</td>
                                    <td>
                                        <label><?php echo isset($wed_location) ? $wed_location : "" ;?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vert-top">服务预算：</td>
                                    <td>
                                        <label>
                                            <?php echo isset($min_service_price) ? $min_service_price : "" ;?>
                                            至
                                            <?php echo isset($max_service_price) ? $max_service_price : "" ;?>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vert-top">期望联系方式：</td>
                                    <td>
                                        <label><?php echo !empty($phone) ? "手机：".$phone : "微信：".$wechat ;?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="vert-top">其他要求：</td>
                                    <td>
                                        <label><?php echo isset($description) ? $description : "" ;?></label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div title="投标商家" class="lftab">
                    <form class="easyuiform" id="bsnsforms" method="post" data-options="novalidate:true">
                        <table class="bsnstb" style="height:40px;">
                            <tr>
                                <td class="tbfield">条件：</td>
                                <td>
                                    <select id="condition" name="condition" class="selectbox com-width100">
                                        <option value="">--请选择--</option>
                                        <option value="nickname">商家昵称</option>
                                        <option value="studio_name">店铺名称</option>
                                        <option value="phone">手机号码</option>
                                    </select>
                                    <input class="textbox condition_text com-width200" id="condition_text" name="condition_text" style="margin-right:30px;" maxlength="50" placeholder="商家昵称/店铺名称/手机号码">
                                </td>
                                <td>
                                    <a href="javascript:void(0)" id="search_form" class="easyui-linkbutton searchdlgbtns c8 pl10 pr10" data-options="iconCls:'icon-search'">查 询</a>
                                    <a href="javascript:void(0)" id="reset_form" class="easyui-linkbutton resetbtn c7">重 置</a>
                                </td>
                            </tr>
                        </table>
                        <input type="hidden" name="sbid" id="sbid" value="<?php echo isset($id) ? $id : "" ;?>" />
                    </form>
                    <table id="bsnsinfo" class="datagrid"></table>
                </div>

            </div>
        </div>
    </div>
</div>

</div>

<?php $this->load->view('header/footer_view.php');?>
<script type="text/javascript">
    seajs.use("<?php echo $config['srcPath'];?>/js/bsnsbidmanage/getshopperbiddetail");
</script>
</body>
</html>