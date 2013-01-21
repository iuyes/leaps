<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php
$page_title=L('change_password');
include $this->admin_tpl('header');
?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formValidatorRegex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform"});
	$("#password").formValidator({onshow:"<?php echo L('inputpassword')?>",onfocus:"<?php echo L('password_len_error')?>"}).inputValidator({min:6,max:20,onerror:"<?php echo L('password_len_error')?>"});
	$("#newpassword").formValidator({onshow:"<?php echo L('inputpassword')?>",onfocus:"<?php echo L('password_len_error')?>"}).inputValidator({min:6,max:20,onerror:"<?php echo L('password_len_error')?>"});
	$("#newpassword2").formValidator({onshow:"<?php echo L('means_code')?>",onfocus:"<?php echo L('the_two_passwords_are_not_the_same_admin_zh')?>",oncorrect:"<?php echo L('right')?>"}).inputValidator({min:6,max:20,onerror:"<?php echo L('password_len_error')?>"}).compareValidator({desid:"newpassword",operateor:"=",onerror:"<?php echo L('the_two_passwords_are_not_the_same_admin_zh')?>"});
	$("#email")
	.formValidator({
		onshow:"<?php echo L('input').L('email')?>",
		onfocus:"<?php echo L('email').L('format_incorrect')?>",
		oncorrect:"<?php echo L('email').L('format_right')?>"
	})
	.regexValidator({
		regexp:"email",
		datatype:"enum",
		onerror:"<?php echo L('email').L('format_incorrect')?>"
	});
	$("#mobile")
	.formValidator({
		empty:true,onshow:"<?php echo L('input').L('mobile')?>",
		onfocus:"<?php echo L('mobile').L('format_incorrect')?>",
		oncorrect:"<?php echo L('mobile').L('format_right')?>"
	})
	.regexValidator({
		regexp:"mobile",
		datatype:"enum",
		onerror:"<?php echo L('mobile').L('format_incorrect')?>"
	});

})
//-->
</script>
<div class="subnav">
<h2 class="title-1 line-x f14 fb blue lh28"><?php echo L('change_info')?></h2>
</div>
<div class="pad-lr-10">
<form action="?app=admin&controller=Private&action=init" method="post">
<table width="100%" class="table_form">
<tr>
<th width="100"><?php echo L('username')?>：</th>
<td><?php echo $userinfo['username']?></td>
</tr>
<tr>
<th width="100"><?php echo L('current_password')?>：</th>
<td><input type="password" class="input-text" name="password" id="password" value="" /></td>
</tr>
<tr>
<th width="100" align="right"><?php echo L('new_password')?>：</th>
<td><input type="password" class="input-text" name="newpassword"  id="newpassword" value="" /></td>
</tr>
<tr>
<th width="100" align="right"><?php echo L('bootos_x')?>：</th>
<td><input type="password" class="input-text" name="newpassword2" id="newpassword2" value="" /></td>
</tr>
<tr>
<th><?php echo L('email')?>：</th>
<td class="y-bg"><input type="text" class="input-text" name="email" value="<?php echo $userinfo['email'];?>" id="email"/></td>
</tr>
<tr>
<th><?php echo L('realname')?>：</th>
<td class="y-bg"><input type="text" class="input-text" name="realname" value="<?php echo $userinfo['realname'];?>" /></td>
</tr>
<tr>
<th><?php echo L('mobile')?>：</th>
<td class="y-bg"><input type="text" class="input-text" name="mobile" value="<?php echo $userinfo['mobile'];?>" id="mobile" /></td>
</tr>

</table>
<div class="bk15"></div>
    <input type="submit" class="button" name="dosubmit" value="<?php echo L('submit')?>" />

</form>
</div>

</body>
</html>