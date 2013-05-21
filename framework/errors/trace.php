<?php if(isset($trace) && is_array($trace)){?>
<div id="leaps_page_trace" style="position: fixed;bottom:0;right:0;font-size:14px;width:100%;z-index: 999999;color: #000;text-align:left;font-family:'微软雅黑';">
<div id="leaps_page_trace_tab" style="display: none;background:white;margin:0;height: 250px;">
<div id="leaps_page_trace_tab_tit" style="height:30px;padding: 6px 12px 0;border-bottom:1px solid #ececec;border-top:1px solid #ececec;font-size:16px">
	<?php foreach($trace as $key => $value){ ?>
    <span style="color:#000;padding-right:12px;height:30px;line-height: 30px;display:inline-block;margin-right:3px;cursor: pointer;font-weight:700"><?php echo $key ?></span>
    <?php } ?>
</div>
<div id="leaps_page_trace_tab_cont" style="overflow:auto;height:212px;padding: 0; line-height: 24px">
		<?php foreach($trace as $info) { ?>
    <div style="display:none;">
    <ol style="padding: 0; margin:0">
	<?php
	if(is_array($info)){
		foreach ($info as $k=>$val){
		echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">' . (is_numeric($k) ? '' : $k.' : ') . htmlentities($val,ENT_COMPAT,'utf-8') .'</li>';
	    }
	}
    ?>
    </ol>
    </div>
    <?php } ?>
</div>
</div>
<div id="leaps_page_trace_close" style="display:none;text-align:right;height:15px;position:absolute;top:10px;right:12px;cursor: pointer;"><img style="vertical-align:top;" src="data:image/gif;base64,R0lGODlhDwAPAJEAAAAAAAMDA////wAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MUQxMjc1MUJCQUJDMTFFMTk0OUVGRjc3QzU4RURFNkEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MUQxMjc1MUNCQUJDMTFFMTk0OUVGRjc3QzU4RURFNkEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxRDEyNzUxOUJBQkMxMUUxOTQ5RUZGNzdDNThFREU2QSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoxRDEyNzUxQUJBQkMxMUUxOTQ5RUZGNzdDNThFREU2QSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAAAAAAALAAAAAAPAA8AAAIdjI6JZqotoJPR1fnsgRR3C2jZl3Ai9aWZZooV+RQAOw==" /></div>
</div>
<div id="leaps_page_trace_open" style="height:30px;float:right;text-align: right;overflow:hidden;position:fixed;bottom:0;right:0;color:#000;line-height:30px;cursor:pointer;"><div style="background:#232323;color:#FFF;padding:0 6px;float:right;line-height:30px;font-size:14px"><?php echo execute_time().'s ';?></div>
<img width="150" style="" title="ShowPageTrace" src="data:image/png;base64,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"></div>
<script type="text/javascript">
(function(){
var tab_tit  = document.getElementById('leaps_page_trace_tab_tit').getElementsByTagName('span');
var tab_cont = document.getElementById('leaps_page_trace_tab_cont').getElementsByTagName('div');
var open     = document.getElementById('leaps_page_trace_open');
var close    = document.getElementById('leaps_page_trace_close').childNodes[0];
var trace    = document.getElementById('leaps_page_trace_tab');
var cookie   = document.cookie.match(/leaps_show_page_trace=(\d\|\d)/);
var history  = (cookie && typeof cookie[1] != 'undefined' && cookie[1].split('|')) || [0,0];
open.onclick = function(){
	trace.style.display = 'block';
	this.style.display = 'none';
	close.parentNode.style.display = 'block';
	history[0] = 1;
	document.cookie = 'leaps_show_page_trace='+history.join('|')
}
close.onclick = function(){
	trace.style.display = 'none';
this.parentNode.style.display = 'none';
	open.style.display = 'block';
	history[0] = 0;
	document.cookie = 'leaps_show_page_trace='+history.join('|')
}
for(var i = 0; i < tab_tit.length; i++){
	tab_tit[i].onclick = (function(i){
		return function(){
			for(var j = 0; j < tab_cont.length; j++){
				tab_cont[j].style.display = 'none';
				tab_tit[j].style.color = '#999';
			}
			tab_cont[i].style.display = 'block';
			tab_tit[i].style.color = '#000';
			history[1] = i;
			document.cookie = 'leaps_show_page_trace='+history.join('|')
		}
	})(i)
}
parseInt(history[0]) && open.click();
tab_tit[history[1]].click();
})();
</script>
<?php }?>