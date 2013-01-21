<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php
$page_title=L('admin_manage');
include $this->admin_tpl('header');
?>
<div class="subnav">
<h2 class="title-1 line-x f14 fb blue lh28"><?php echo L('admin_manage')?></h2>
<div class="content-menu ib-a blue line-x"><a href="?app=admin&controller=admin&action=init" class="on"><em><?php echo L('listadmins')?></em></a><span>|</span> <a href="?app=admin&controller=admin&action=add"><em><?php echo L('add_admin')?></em></a></div>
</div>
<div class="pad-lr-10">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
				<th align="center"><?php echo L('id')?></th>
				<th align="left"><?php echo L('username')?></th>
				<th align="left"><?php echo L('type')?></th>
				<th align="left"><?php echo L('realname')?></th>
				<th align="left"><?php echo L('mobile')?></th>
				<th align="left"><?php echo L('email')?></th>
				<th align="left"><?php echo L('lastlogintime')?></th>
				<th align="left"><?php echo L('landing_ip')?></th>
				<th align="left"><?php echo L('operation')?></th>
			</tr>
        </thead>
    <tbody>
    <?php foreach ($infos as $v):?>
        <tr>
			<td align="center"><?php echo $v['userid']?></td>
			<td align="left"><?php echo $v['username']?></td>
			<td align="left"><?php if ($v['issuper']) {echo L('subminiature_tube');} else {echo L('administrator');}?></td>
			<td align="left"><?php echo $v['realname']?></td>
			<td align="left"><?php echo $v['mobile']?></td>
			<td align="left"><?php echo $v['email']?></td>
			<td align="left"><?php echo date('Y-m-d h:i:s',$v['lastlogintime'])?></td>
			<td align="left"><?php echo $v['lastloginip']?></td>
			<td align="left"><a href="?app=admin&controller=admin&action=edit&userid=<?php echo $v['userid']?>">[<?php echo L('edit')?>]</a>&nbsp;|&nbsp;<a href="?app=admin&controller=admin&action=delete&userid=<?php echo $v['userid']?>" onclick="return confirm('<?php echo L('sure_delete')?>')">[<?php echo L('delete')?>]</a></td>
</tr>
      <?php endforeach;?>
      </tbody>
    </table>
<div id="pages"><?php echo $pages?></div>
</div>
</div>
</body>
</html>