<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><?php echo L('leaps_admin')?></title>
<link href="<?php echo CSS_PATH?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>system.css" rel="stylesheet" type="text/css" />
<style>
#loading{position:absolute;left:600px;display:none}
</style>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery-1.4.2.min.js"></script>
</head>
<body scroll="no">
<div id="loading"><div class="msg lf"><p class="attention"><?php echo L('load..');?><span id="loadsecond"></span>...</p></div></div>
<div class="header">
	<div class="logo lf"><span class="invisible"><?php echo L('leaps_admin')?></span></div>
    <div class="lf">
    <div class="log white cut_line"><?php echo L('hellow')?> <?php echo $userinfo['username']?> <?php if($userinfo['issuper']) echo '['.L('subminiature_tube').']'?><span>|</span><a href="?app=admin&controller=Private&action=init" target="right_iframe">[<?php echo L('account_setting')?>]</a><span>|</span><a href="?app=admin&controller=index&action=logout">[<?php echo L('logout')?>]</a></div>
    </div>
</div>
<div id="content" class="content-on">
	<div class="col-left left_menu">
    	<div>
       <a href="/"><h3 class="f14"><?php echo L('manage_center')?></h3></a>
        <ul>
          	<li class="menuli" onclick="menu_show(this);"><a hidefocus="true" style="outline:none" href="?app=admin&controller=Private&action=init" target="right_iframe"><?php echo L('account_setting')?></a></li>
        </ul>
        <?php if($userinfo['issuper']):?>
        <div class="line-x"></div>
        <ul>
           <li class="menuli" onclick="menu_show(this);"><a hidefocus="true" style="outline:none" href="?app=admin&controller=admin&action=init" target="right_iframe"><?php echo L('admin_manage')?></a></li>
			<li class="menuli" onclick="menu_show(this);"><a hidefocus="true" style="outline:none" href="?app=admin&controller=system&action=init" target="right_iframe"><?php echo L('system_setting')?></a></li>
			<li class="menuli" onclick="menu_show(this);"><a hidefocus="true" style="outline:none" href="?app=admin&controller=cache&action=init" target="right_iframe"><?php echo L('clean_cache')?></a></li>
        </ul>
        <?php endif;?>
        </div>
        <a hidefocus="true" style="outline:none" href="javascript:;" id="openClose" class="open" title="<?php echo L('spread_or_closed')?>"><span class="hidden"><?php echo L('expand')?></span></a>
    </div>

    <div class="col-auto mr8">
    <div class="crumbs"><?php echo L('local')?><span id="local"></span></div>
    	<div class="col-1">
        	<div class="content">
                <iframe name="right_iframe" id="right_iframe" src="?app=admin&controller=index&action=right" frameborder="false" scrolling="auto" style="overflow-x:hidden;border:none" width="100%" height="auto" allowtransparency="true" onload="showloading()"></iframe>
        	</div>
        </div>
    </div>
</div>
<script type="text/javascript">
//clientHeight-0; 空白值 iframe自适应高度
function windowW(){
	if($(window).width()<940){
			$('.header').css('width',940+'px');
			$('#content').css('width',940+'px');
			$('body').attr('scroll','');
			$('body').css('overflow','');
	}
}
windowW();
$(window).resize(function(){
	if($(window).width()<940){
		windowW();
	}else{
		$('.header').css('width','auto');
		$('#content').css('width','auto');
		$('body').attr('scroll','no');
		$('body').css('overflow','hidden');

	}
});
window.onresize = function(){
	var heights = document.documentElement.clientHeight-120;document.getElementById('right_iframe').height = heights;
	var openClose = $("#right_iframe").height()+39;
	$("#openClose").height(openClose);$("#content").height(openClose);
}
window.onresize();
//左侧开关
$("#openClose").toggle(
  function () {
    $(".left_menu").addClass("left_menu_on");
	$(this).addClass("close");
	$("#content").addClass("content-off")
  },
  function () {
    $(".left_menu").removeClass("left_menu_on");
	$(this).removeClass("close");
	$("#content").removeClass("content-off")
  }
);

function menu_show(obj) {
	$('.menuli').removeClass('on fb blue');
	$(obj).addClass('on fb blue');
}

function span_local(name) {
	$('#local').html(name);
}

if(top.location != self.location) {
	top.location=self.location;
}

var read;
function showloading(type) {
	if(type) {
		$('#loadsecond').html('');
		$('#loading').show();
		//second = 1;
		//read = setInterval(readsecond, 1000);
	} else {
		$('#loading').fadeOut("slow");
		//if(read) clearInterval(read);
	}
}

function readsecond() {
	$('#loadsecond').html('('+second+')');
	second++;
}

</script>
</body>
</html>