<?php
defined ( 'IN_ADMIN' ) or exit ( 'No permission resources.' );
$page_title = L('system_setting');
include $this->admin_tpl ( 'header' );
?>
<script type="text/javascript">
<!--
	$(function(){
		SwapTab('setting','on','',5,<?php echo isset($_GET['tab']) ? $_GET['tab'] : '1'?>);
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog.alert(msg);$(obj).focus();}});
		$("#site_name").formValidator({onshow:"<?php echo L('setting_input').L('site_name')?>",onfocus:"<?php echo L('setting_input').L('site_name')?>"}).inputValidator({min:1,onerror:"<?php echo L('setting_input').L('site_name')?>"}).defaultPassed();
		$("#site_url")
			.formValidator({
				onshow:"<?php echo L('site_domain_ex')?>",
				onfocus:"<?php echo L('site_domain_ex')?>",
				tipcss:{width:'300px'},
				empty:false
			})
			.inputValidator({
				onerror:"<?php echo L('site_domain_ex')?>"
			})
			.regexValidator({
				regexp:"http:\/\/(.+)\/$",
				onerror:"<?php echo L('site_domain_ex2')?>"
			});
		$("#js_path")
			.formValidator({
				onshow:"<?php echo L('setting_input').L('setting_js_path')?>",
				onfocus:"<?php echo L('setting_js_path').L('setting_end_with_x')?>"
			})
			.inputValidator({
				onerror:"<?php echo L('setting_js_path').L('setting_input_error')?>"
			})
			.regexValidator({
				regexp:"(.+)\/$",
				onerror:"<?php echo L('setting_js_path').L('setting_end_with_x')?>"
			});
		$("#css_path")
			.formValidator({
				onshow:"<?php echo L('setting_input').L('setting_css_path')?>",
				onfocus:"<?php echo L('setting_css_path').L('setting_end_with_x')?>"
			})
			.inputValidator({
				onerror:"<?php echo L('setting_css_path').L('setting_input_error')?>"
			})
			.regexValidator({
				regexp:"(.+)\/$",
				onerror:"<?php echo L('setting_css_path').L('setting_end_with_x')?>"
			});
		$("#img_path")
			.formValidator({
				onshow:"<?php echo L('setting_input').L('setting_img_path')?>",
				onfocus:"<?php echo L('setting_img_path').L('setting_end_with_x')?>"
			})
			.inputValidator({
				onerror:"<?php echo L('setting_img_path').L('setting_input_error')?>"
			})
			.regexValidator({
				regexp:"(.+)\/$",
				onerror:"<?php echo L('setting_img_path').L('setting_end_with_x')?>"
			});
		$("#errorlog_size")
			.formValidator({
				onshow:"<?php echo L('setting_errorlog_hint')?>",
				onfocus:"<?php echo L('setting_input').L('setting_error_log_size')?>"
			})
			.inputValidator({
				onerror:"<?php echo L('setting_error_log_size').L('setting_input_error')?>"
			})
			.regexValidator({
				regexp:"num",
				datatype:"enum",
				onerror:"<?php echo L('setting_errorlog_type')?>"
			});
		$("#error_message")
			.formValidator({
				onshow:"<?php echo L('error_message_tip');?>",
			})
			.inputValidator({
				onerror:"<?php echo L('error_message_tip')?>"
			});
	})
//-->
</script>
<style type="text/css">
.radio-label {
	border-top: 1px solid #e4e2e2;
	border-left: 1px solid #e4e2e2
}
.radio-label td {
	border-right: 1px solid #e4e2e2;
	border-bottom: 1px solid #e4e2e2;
	background: #f6f9fd
}
</style>
<form action="?app=admin&controller=setting&action=save" method="post"
	id="myform">
  <div class="pad-10">
    <div class="col-tab">
      <ul class="tabBut cu-li">
        <li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',5,1);"><?php echo L('setting_basic_cfg')?></li>
        <li id="tab_setting_2" onclick="SwapTab('setting','on','',5,2);"><?php echo L('site_config')?></li>
        <li id="tab_setting_3" onclick="SwapTab('setting','on','',5,3);"><?php echo L('setting_safe_cfg')?></li>
        <li id="tab_setting_5" onclick="SwapTab('setting','on','',5,4);"><?php echo L('sms_config')?></li>
        <li id="tab_setting_6" onclick="SwapTab('setting','on','',5,5);"><?php echo L('contactus_config')?></li>
      </ul>
      <!--基本设置-->
      <div id="div_setting_1" class="contentList pad-10">
        <table width="100%" class="table_form">
          <tr>
            <th width="140"><?php echo L('setting_admin_email')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="system[system_email]" id="admin_email" size="30" value="<?php echo $system['system_email']?>" /></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('setting_gzip')?></th>
            <td class="y-bg"><input name="framework[gzip]" value="true" type="radio" <?php echo ($config['gzip']) ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp; <input name="framework[gzip]" value="false" type="radio" <?php echo (!$config['gzip']) ? ' checked' : ''?>> <?php echo L('setting_no')?></td>
          </tr>

          <tr>
            <th width="140"><?php echo L('setting_js_path')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="system[js_path]" id="js_path" size="50" value="<?php echo $system['js_path']?>" /></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('setting_css_path')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="system[css_path]" id="css_path" size="50" value="<?php echo $system['css_path']?>" /></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('setting_img_path')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="system[img_path]" id="img_path" size="50" value="<?php echo $system['img_path']?>" /></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('show_page_trace')?></th>
            <td class="y-bg"><input name="framework[show_trace]" value="true" type="radio" <?php echo ($config['show_trace']) ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp; <input name="framework[show_trace]" value="false" type="radio" <?php echo (!$config['show_trace']) ? ' checked' : ''?>><?php echo L('setting_no')?></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('show_run_time')?></th>
            <td class="y-bg"><input name="framework[show_time]" value="true" type="radio" <?php echo ($config['show_time']) ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp; <input name="framework[show_time]" value="false" type="radio" <?php echo (!$config['show_time']) ? ' checked' : ''?>> <?php echo L('setting_no')?></td>
          </tr>
        </table>
      </div>
      <!--网站设置-->
      <div id="div_setting_2" class="contentList pad-10 hidden">
        <table width="100%" class="table_form">
          <tr>
            <th width="140"><?php echo L('site_name')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="setting[site_name]" id="site_name" size="30" value="<?php echo $setting['site_name']?>" /></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('site_domain')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="system[app_path]" id="site_url" size="30" value="<?php echo defined('SITE_URL') ? SITE_URL : $app_path ?>" /></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('site_title')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="setting[site_title]" id="site_title" size="50" value="<?php echo $setting['site_title']?>" /></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('keyword_name')?></th>
            <td class="y-bg"><textarea name='setting[keywords]' cols='60' rows='2'><?php echo $setting['keywords']?></textarea></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('description')?></th>
            <td class="y-bg"><textarea name='setting[description]' cols='60' rows='2'><?php echo $setting['description']?></textarea></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('site_copyrigh')?></th>
            <td class="y-bg"><textarea name='setting[copyrights]' cols='60' rows='2'><?php echo $setting['copyrights']?></textarea></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('site_icp')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="setting[icp]" size="50" value="<?php echo $setting['icp']?>" /> <?php echo L('icp_notice')?></td>
          </tr>
        </table>
      </div>
      <!--安全设置-->
      <div id="div_setting_3" class="contentList pad-10 hidden">
        <table width="100%" class="table_form">
          <tr>
            <th width="140"><?php echo L('setting_admin_log')?></th>
            <td class="y-bg">
            	<input name="system[admin_log]" value="true" type="radio" <?php echo ($system['admin_log']) ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="system[admin_log]" value="false" type="radio"<?php echo (!$system['admin_log']) ? ' checked' : ''?>> <?php echo L('setting_no')?>
            </td>
          </tr>
          <tr>
            <th width="140"><?php echo L('error_level')?></th>
            <td class="y-bg">
            	<input name="framework[debug]" value="0"type="radio" <?php echo (!$config['debug']) ? ' checked' : ''?>> <?php echo L('site_att_watermark_close')?>&nbsp;&nbsp;&nbsp;&nbsp;
              	<input name="framework[debug]" value="1" type="radio"<?php echo ($config['debug'] == 1) ? ' checked' : ''?>> E_ERROR&nbsp;&nbsp;&nbsp;&nbsp;
              	<input name="framework[debug]"value="2" type="radio"<?php echo ($config['debug'] == 2) ? ' checked' : ''?>> E_ALL
            </td>
          </tr>
          <tr>
            <th width="140"><?php echo L('setting_error_log')?></th>
            <td class="y-bg">
            	<input name="log[enable]" value="true" type="radio" <?php echo ($log['enable']) ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
            	<input name="log[enable]" value="false" type="radio" <?php echo (!$log['enable']) ? ' checked' : ''?>> <?php echo L('setting_no')?></td>
          </tr>
          <tr>
            <th><?php echo L('setting_error_log_size')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="log[file_size]" id="errorlog_size" size="5" value="<?php echo $log['file_size']?>" /> KB</td>
          </tr>
          <tr>
            <th width="140"><?php echo L('show_error_msg')?></th>
            <td class="y-bg">
            	<input name="framework[show_error_msg]" value="true" type="radio" <?php echo ($config['show_error_msg']) ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
           		<input name="framework[show_error_msg]" value="false" type="radio" <?php echo (!$config['show_error_msg']) ? ' checked' : ''?>> <?php echo L('setting_no')?></td>
          </tr>
          <tr>
            <th><?php echo L('error_message')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="framework[error_message]" size="40" id="error_message"value="<?php echo $config['error_message']?>" /></td>
          </tr>
          <tr>
            <th><?php echo L('error_page')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="framework[error_page]" size="40" value="<?php echo $config['error_page']?>" /></td>
          </tr>
          <tr>
            <th><?php echo L('setting_maxloginfailedtimes')?></th>
            <td class="y-bg"><input type="text" class="input-text" name="setting[maxloginfailedtimes]" id="maxloginfailedtimes" size="10" value="<?php echo $setting['maxloginfailedtimes']?>" /></td>
          </tr>
          <tr>
            <th width="140"><?php echo L('show_firephp')?></th>
            <td class="y-bg">
            	<input name="framework[firephp]" value="true" type="radio" <?php echo ($config['firephp']) ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
            	<input name="framework[firephp]" value="false" type="radio" <?php echo (!$config['firephp']) ? ' checked' : ''?>> <?php echo L('setting_no')?></td>
          </tr>
        </table>
      </div>
      <!--短信设置-->
      <div id="div_setting_4" class="contentList pad-10 hidden">
      		<table width="100%" class="table_form">
            	<tr>
            		<th width="140"><?php echo L('setting_sms_driver')?></th>
            		<td class="y-bg">
                    	<input name="sms[driver]" value="Emay" type="radio" id="driver" <?php echo ($sms['driver'] == 'Emay') ? ' checked' : ''?>> <?php echo L('emay')?>&nbsp;&nbsp;&nbsp;&nbsp;
              			<input name="sms[driver]" value="Winic" type="radio" id="driver" <?php echo ($sms['driver'] == 'Winic') ? ' checked' : ''?>> <?php echo L('winic')?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
          		</tr>
				<tr>
					<th width="140"><?php echo L('username')?></th>
					<td class="y-bg">
                    	<input type="text" class="input-text"	name="sms[username]" id="username" size="30" value="<?php echo $sms['username']?>" />
                        <input type="button" class="button" onClick="javascript:get_balance();" value="<?php echo L('get_balance')?>">  <span id='get_balance'></span>
                    </td>
				</tr>
				<tr>
					<th width="140"><?php echo L('password')?></th>
					<td class="y-bg"><input type="text" class="input-text" name="sms[password]" id="password" size="30" value="<?php echo $sms['password']?>" /> </td>
				</tr>
				<tr>
					<th width="140"><?php echo L('session_key')?></th>
					<td class="y-bg"><input type="text" class="input-text" name="sms[session_key]" id="session_key" size="30" value="<?php echo $sms['session_key']?>" /> </td>
				</tr>
				<tr>
					<th width="140"><?php echo L('sign')?></th>
					<td class="y-bg"><input type="text" class="input-text"name="sms[sign]" id="sign" size="30" value="<?php echo $sms['sign']?>" /></td>
				</tr>
				<tr>
					<th width="140"><?php echo L('sms_test')?></th>
					<td class="y-bg">
                    	<input type="text" class="input-text" name="sms_to" id="sms_to" size="30" value="" />
                        <input type="button" class="button" onClick="javascript:test_sms();" value="<?php echo L('sms_test_send')?>"> <span id='test_sms'></span>
                    </td>
				</tr>
			</table>
      </div>
      <!--联系设置-->
      <div id="div_setting_5" class="contentList pad-10 hidden">
        <table width="100%" class="table_form">
        <tr>
       <th width="140"><?php echo L('open_front_desk_customer_service')?></th>
       <td class="y-bg">
       <input class="radio_style" name="setting[live_ifonserver]" value="true" <?php echo $setting['live_ifonserver'] ? 'checked="checked"' : ''?> type="radio"> <?php echo L('site_att_watermark_open')?>&nbsp;&nbsp;&nbsp;&nbsp;
	   <input class="radio_style" name="setting[live_ifonserver]" value="false" <?php echo !$setting['live_ifonserver'] ? 'checked="checked"' : ''?> type="radio"> <?php echo L('site_att_watermark_close')?></td>
  	</tr>
  	<tr>
       <th width="140"><?php echo L('the_default_on_customer_service_list')?></th>
       <td class="y-bg">
       <input class="radio_style" name="setting[live_boxopen]" value="true" <?php echo $setting['live_boxopen'] ? 'checked="checked"' : ''?> type="radio"> <?php echo L('site_att_watermark_open')?>&nbsp;&nbsp;&nbsp;&nbsp;
	   <input class="radio_style" name="setting[live_boxopen]" value="false" <?php echo !$setting['live_boxopen'] ? 'checked="checked"' : ''?> type="radio"> <?php echo L('site_att_watermark_close')?></td>
  	</tr>
  	 <tr>
       <th width="140"><?php echo L('popup_invite_dialog_box')?></th>
       <td class="y-bg">
       <input class="radio_style" name="setting[live_boxtip]" value="true" <?php echo $setting['live_boxtip'] ? 'checked="checked"' : ''?> type="radio"> <?php echo L('site_att_watermark_open')?>&nbsp;&nbsp;&nbsp;&nbsp;
	   <input class="radio_style" name="setting[live_boxtip]" value="false" <?php echo !$setting['live_boxtip'] ? 'checked="checked"' : ''?> type="radio"> <?php echo L('site_att_watermark_close')?></td>
  	</tr>
  	<tr>
       <th width="140"><?php echo L('customer_service_floating_box_location')?></th>
       <td class="y-bg">
       <input class="radio_style" name="setting[live_serverlistp]" value="left" <?php echo $setting['live_serverlistp']=='left' ? 'checked="checked"' : ''?> type="radio"> <?php echo L('live_left')?>&nbsp;&nbsp;&nbsp;&nbsp;
	   <input class="radio_style" name="setting[live_serverlistp]" value="right" <?php echo $setting['live_serverlistp']=='right' ? 'checked="checked"' : ''?> type="radio"> <?php echo L('live_right')?></td>
  	</tr>
  	<tr>
       <th width="140"><?php echo L('Company_name')?></th>
       <td class="y-bg"><input name="setting[companyname]" type="text" size="30" value="<?php echo $setting['companyname']?>"></td>
  	</tr>
 	<tr>
       <th width="140"><?php echo L('Contact')?></th>
       <td class="y-bg"><input name="setting[contact_name]" type="text" size="20" value="<?php echo $setting['contact_name']?>" ></td>
  	</tr>
  	<tr>
       <th><?php echo L('Mobile')?></th>
       <td class="y-bg"><input name="setting[mobile]" type="text" size="20" value="<?php echo $setting['mobile']?>"></td>
 	</tr>
 	<tr>
       <th><?php echo L('Phone')?></th>
       <td class="y-bg"> <input name="setting[telephone]" type="text" size="30" value="<?php echo $setting['telephone']?>" ></td>
	 </tr>
	 <tr>
            <th width="140">QQ</th>
            <td class="y-bg"><input name="setting[qq]" type="text" size="40" value="<?php echo $setting['qq']?>"> <?php echo L('qq_tip')?><a href="http://zc.qq.com/" target="_blank"><?php echo L('click_register')?></a></td>
          </tr>
 	<tr>
       <th><?php echo L('Address')?></th>
       <td class="y-bg"> <input name="setting[address]" type="text" size="50" value="<?php echo $setting['address']?>"></td>
 	</tr>
 	<tr>
       <th>E-Mail</th>
       <td class="y-bg"> <input name="setting[email]" type="text" size="40" value="<?php echo $setting['email']?>" > <a href="http://email.163.com/" target="_blank"><?php echo L('click_register')?></a></td>
  	</tr>
        </table>
      </div>

      <div class="bk15"></div>
      <input name="dosubmit" type="submit" value="<?php echo L('submit')?>"
				class="button">
    </div>
  </div>
</form>
<script type="text/javascript">
function SwapTab(name,cls_show,cls_hide,cnt,cur){
    for(i=1;i<=cnt;i++){
		if(i==cur){
			 $('#div_'+name+'_'+i).show();
			 $('#tab_'+name+'_'+i).attr('class',cls_show);
		}else{
			 $('#div_'+name+'_'+i).hide();
			 $('#tab_'+name+'_'+i).attr('class',cls_hide);
		}
	}
}
</script>
</body>
</html>