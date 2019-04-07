<?php
/**
 * 乞讨页面
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
if(strpos($this->permalink,'?')){
	$url=substr($this->permalink,0,strpos($this->permalink,'?'));
}else{
	$url=$this->permalink;
}
$pluginsname='TleQiTao';
$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin($pluginsname);
$plug_url = $options->pluginUrl;
?>
<?php
date_default_timezone_set('Asia/Shanghai');
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<title>全天24小时乞讨</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=0.9">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://css.letvcdn.com/lc04_yinyue/201612/19/20/00/bootstrap.min.css">
<link rel="alternate icon" type="image/png" href="https://ws3.sinaimg.cn/large/ecabade5ly1fxpiemcap1j200s00s744.jpg">
<body background="https://ww2.sinaimg.cn/large/a15b4afegy1fpp139ax3wj200o00g073.jpg">
<div class="container" style="padding-top:20px;">
	<div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
		<div class="panel panel-primary">
			<div class="panel-heading" style="background: linear-gradient(to right,#8ae68a,#5ccdde,#b221ff);">
				<center><font color="#000000"><b>在下正在沿街乞讨……</b></font></center>
			</div>
			<div class="panel-body">
				<center>
					<div class="alert alert-success">
						<a href="https://wpa.qq.com/msgrd?v=3&uin=<?=$option->tleqitaoqq;?>&site=qq&menu=yes" target="_blank"><img class="img-circle m-b-xs" style="border: 2px solid #1281FF; margin-left:3px; margin-right:3px;" src="https://q4.qlogo.cn/headimg_dl?dst_uin=<?=$option->tleqitaoqq;?>&spec=100"; width="60px" height="60px" alt="全天24小时要饭"><br></a>
						<?=$option->tleqitaotalk;?>
					</div>
				</center>
				<form id=payform action="<?=$plug_url;?>/TleQiTao/pay.php" method=post target="_blank">
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-shopping-cart"></span> 施舍留言</span>
						<input type="text" maxLength="8" id="attachData" name="attachData" value="好心人施舍" class="form-control" required="required" placeholder="想要对我说些啥" />
					</div>
					<br/>
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-yen"></span> 施舍金额</span>
						<input type="text" id="Money" name="Money" value="1" class="form-control" required="required" placeholder="施舍金额（元）" oninput="if(value.length>4)value=value.slice(0,4)"/>
					</div>        			
					<br/> 
					<center>
						<input type="hidden" name="action" value="submit" />
						<p>
							<center>
							<div class="btn-group btn-group-justified" role="group" aria-label="...">
								<div id="submit" class="btn btn-primary">
									确定施舍...
									<span id="msg"></span>
								</div>
							</div>
							</center>
						</p>
					</center> 
				</form>
			</div>
		</div>
		<?php
		$query= $this->db->select()->from('table.tleqitao_item')->order('instime',Typecho_Db::SORT_DESC)->offset(0)->limit(10);
		$rows = $this->db->fetchAll($query);
		if(count($rows)>0){
		?>
		<div class="panel panel-primary">
			<div class="panel-heading" style="background: linear-gradient(to right,#8ae68a,#5ccdde,#b221ff);">
				<center><font color="#000000"><b>来自好心人的施舍记录</b></font></center>
			</div>
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
					<tr>
						<th>订单</th><th>施舍方式</th><th>施舍金额</th><th>施舍留言</th><th>状态</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ($rows as $value) {
					?>
					<tr>
						<td><?=$value['orderNumber'];?></td>
						<td>
							<?php
							if($value['payChannel']=='wx'){
								echo '微信';
							}else{
								echo '其他';
							}
							?>
						</td>
						<td><?=$value['Money'];?>元</td>
						<td>
							<?php
								if($value['attachData']==''){
									echo '无偿奉献';
								}else{
									echo $value['attachData'];
								}
							?>
						</td>
						<td>
							<?php
							if($value['status']=='y'){
								echo '<font color="green">已施舍</font>';
							}else if($value['status']=='n'){
								echo '<font color="red">匆匆过客</font>';
							}
							?>
						</td>
					</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		$sumTodayQuery= $this->db->select('count(*) as total')->from('table.tleqitao_item')->where('status=?','y')->where('DATEDIFF(now(),instime)=?',0);
		$sumTodayRow = $this->db->fetchRow($sumTodayQuery);
		$totalTodayQuery= $this->db->select('sum(Money) as total')->from('table.tleqitao_item')->where('status=?','y')->where('DATEDIFF(now(),instime)=?',0);
		$totalTodayRow = $this->db->fetchRow($totalTodayQuery);
		
		$sumYesterdayQuery= $this->db->select('count(*) as total')->from('table.tleqitao_item')->where('status=?','y')->where('DATEDIFF(now(),instime)=?',1);
		$sumYesterdayRow = $this->db->fetchRow($sumYesterdayQuery);
		$totalYesterdayQuery= $this->db->select('sum(Money) as total')->from('table.tleqitao_item')->where('status=?','y')->where('DATEDIFF(now(),instime)=?',1);
		$totalYesterdayRow = $this->db->fetchRow($totalYesterdayQuery);
		
		$sumQuery= $this->db->select('count(*) as total')->from('table.tleqitao_item')->where('status=?','y');
		$sumRow = $this->db->fetchRow($sumQuery);
		$totalQuery= $this->db->select('sum(Money) as total')->from('table.tleqitao_item')->where('status=?','y');
		$totalRow = $this->db->fetchRow($totalQuery);
		?>
		<div class="panel panel-info">
			<div class="panel-heading" style="background: linear-gradient(to right,#14b7ff,#5ccdde,#b221ff);">
				<center><font color="#000000"><b>站点日志</b></font></center>
			</div>
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td align="center"><font color="#808080"><b>今日施舍总数</b></br><code><?=$sumTodayRow['total'];?></code></br>次</font></td>
						<td align="center"><font color="#808080"><b>今日施舍金额</b></br><code><?=$totalTodayRow['total']!=''?$totalTodayRow['total']:0;?></code></br>元</font></td>
					</tr>
					<tr>
						<td align="center"><font color="#808080"><b>昨日施舍总数</b></br><code><?=$sumYesterdayRow['total'];?></code></br>次</font></td>
						<td align="center"><font color="#808080"><b>昨日施舍金额</b></br><code><?=$totalYesterdayRow['total']!=''?$totalYesterdayRow['total']:0;?></code></br>元</font>
					</td>
					</tr>
					<tr height=50>
						<td align="center"><font color="#808080"><b>累计施舍总数</b></br><code><?=$sumRow['total'];?></code></br>次</font></td>
						<td align="center"><font color="#808080"><b>累计施舍金额</b></br><code><?=$totalRow['total']!=''?$totalRow['total']:0;?></code></br>元</font>
					</td>
					</tr>
				<tbody>
			</table>
		</div>
		<?php
		}
		?>
		<p style="text-align:center"><br>&copy; <?=date("Y");?> 后端:<a href="https://me.tongleer.com/qitao" target="_blank">二呆</a> and 前端:<a href="https://www.yyhy.me/yf/" target="_blank">烟雨寒云</a>. All rights reserved.</p>
	</div>
</div>
<script src="https://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/layer/2.3/layer.js"></script>
<script>
$(function(){
	//随机金额
	function randomData(){
	   var moneys=[[0.66,'66大顺'],[0.88,'恭喜发财'],[1.1,'一生一世'],[2.33,'笑看人生'],[3.14,'数学之美'],[5.20,'爱你哟'],[6.66,'真的很6']];
	   var value = moneys[Math.round(Math.random()*(moneys.length-1))];
	   $('#attachData').val(value[1]);
	   $('#Money').val(value[0]);
	}
	randomData();
	/*限制键盘只能按数字键、小键盘数字键、退格键*/
	$("#Money").keyup(function(){
		$("#Money").val($("#Money").val().replace(/[^\d.]/g,""));
		$("#Money").val($("#Money").val().replace(/\.{2,}/g,"."));
		$("#Money").val($("#Money").val().replace(/^\./g,""));
		$("#Money").val($("#Money").val().replace(".","$#$").replace(/\./g,"").replace("$#$","."));
		$("#Money").val($("#Money").val().replace(/^(\-)*(\d+)\.(\d\d).*$/,"$1$2.$3"));
	});
	$("#submit").click(function(){
		var timer;
		var oldtime = getCookie('paytime');
		var nowtime = Date.parse(new Date()); 
		if((nowtime-oldtime)/1000<=10){
			$("#msg").html('<font color="red">施舍太快，我会脸红的^_^</font>');
			timer=setTimeout(function() { 
				clearTimeout(timer);
				$("#msg").html('');
			},1000) 
			return;
		}
		if($("#Money").val()==''||$("#Money").val()==0){
			return;
		}
		var Money = $("#Money").val();
		var str = "老板，谢谢打赏<br>打赏金额：￥"+Money;
		layer.confirm(str, {
			btn: ['我要打赏','不打赏了']
		}, function(){
			var ii = layer.load(2, {shade:[0.1,'#fff']});
			$.ajax({
				type : "POST",
				url : "<?php echo $plug_url.'/TleQiTao/pay.php';?>",
				data : {"action":"submit","Money":$("#Money").val(),"attachData":$("#attachData").val()},
				dataType : 'json',
				success : function(data) {
					layer.close(ii);
					if(data.status=="ok"){
						str="<center><div>支持微信付款</div><div><img src='"+data.qrcode+"' width='200' /></div></center>";
						var nowtime = Date.parse(new Date()); 
						setCookie('paytime',nowtime,24);
					}else{
						str="<center><div>请求支付过程出了一点小问题，稍后重试一次吧！</div></center>";
					}
					layer.confirm(str, {
						btn: ['已打赏','后悔了']
					},function(index){
						window.location.reload();
						layer.close(index);
					});
				},error:function(data){
					layer.close(ii);
					layer.msg('服务器错误');
					return false;
				}
			});
		}, function(){
			layer.msg('老板行行好吧....我已经3天没吃饭了', {
				time: 5000,/*20s后自动关闭*/
				btn: ['再考虑一下~']
			});
		});
	});
	/*对象转数组*/
	function objToArray(array) {
		var arr = []
		for (var i in array) {
			arr.push(array[i]); 
		}
		console.log(arr);
		return arr;
	}
	/*Cookie操作*/
	function clearCookie(){ 
		var keys=document.cookie.match(/[^ =;]+(?=\=)/g); 
		if (keys) { 
			for (var i = keys.length; i--;) 
			document.cookie=keys[i]+'=0;expires=' + new Date( 0).toUTCString() 
		} 
	}
	function setCookie(name,value,hours){  
		var d = new Date();
		d.setTime(d.getTime() + hours * 3600 * 1000);
		document.cookie = name + '=' + value + '; expires=' + d.toGMTString();
	}
	function getCookie(name){  
		var arr = document.cookie.split('; ');
		for(var i = 0; i < arr.length; i++){
			var temp = arr[i].split('=');
			if(temp[0] == name){
				return temp[1];
			}
		}
		return '';
	}
	function removeCookie(name){
		var d = new Date();
		d.setTime(d.getTime() - 10000);
		document.cookie = name + '=1; expires=' + d.toGMTString();
	}
	$('#bgAudio')[0].volume = 0.01;
});
</script>
<?php if($option->tleqitaoisaudio=='y'){?>
<audio id="bgAudio" autoplay="autoplay" loop="loop" height="100" width="100">
<source src="http://other.web.rg01.sycdn.kuwo.cn/resource/n1/66/37/904891334.mp3" type="audio/mp3" />
</audio>
<?php }?>
</body>
</html>