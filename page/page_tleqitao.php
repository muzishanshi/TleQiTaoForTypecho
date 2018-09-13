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
<link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://css.letvcdn.com/lc04_yinyue/201612/19/20/00/bootstrap.min.css">
<link rel="alternate icon" type="image/png" href="http://www.tongleer.com/wp-content/themes/D8/img/favicon.png">
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
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
						<a href="http://wpa.qq.com/msgrd?v=3&uin=<?=$option->tleqitaoqq;?>&site=qq&menu=yes" target="_blank"><img class="img-circle m-b-xs" style="border: 2px solid #1281FF; margin-left:3px; margin-right:3px;" src="https://q4.qlogo.cn/headimg_dl?dst_uin=<?=$option->tleqitaoqq;?>&spec=100"; width="60px" height="60px" alt="全天24小时要饭"><br></a>
						<?=$option->tleqitaotalk;?>
					</div>
				</center>
				<form id=payform action="<?=$plug_url;?>/TleQiTao/pay.php" method=post target="_blank">
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-shopping-cart"></span> 施舍留言</span>
						<input type="text" maxLength="8" name="attachData" value="好心人施舍" class="form-control" required="required" placeholder="想要对我说些啥" />
					</div>
					<br/>
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-yen"></span> 施舍金额</span>
						<input type="text" id="Money" name="Money" value="1" class="form-control" required="required" placeholder="施舍金额（元）" oninput="if(value.length>3)value=value.slice(0,3)"/>
					</div>        			
					<br/> 
					<center>
						<div class="btn-group btn-group-justified" role="group" aria-label="...">
							<div class="btn-group" role="group">
								<button type="button" id="type_alipay" value="<?=@$_GET['payChannel'];?>" class="btn btn-primary"><font color="#ffffff"><b>支付宝</b></font></button>
							</div>
							
						</div>
						<input type="hidden" name="payChannel" value="<?php if(@$_GET['payChannel']==''){?>alipay<?php }else{echo @$_GET['payChannel'];}?>" />
						<input type="hidden" name="returnurl" value="<?=$url;?>" />
						<p>
							<center>
							<div class="btn-group btn-group-justified" role="group" aria-label="...">
								<div id="submit" class="alert alert-warning">
									<!--选择一种方式后进行施舍...-->确定施舍...-
									<span id="msg"></span>
								</div>
							</div>
							</center>
						</p>
						<p style="text-align:center"><br>Copyright &copy; 2018 Powered by <a href="http://www.tongleer.com" target="_blank">同乐儿</a></p>
					</center> 
				</form>
			</div>
		</div>
		<?php
		$query= $this->db->select()->from('table.tleqitao_item')->order('instime',Typecho_Db::SORT_DESC);
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
							if($value['payChannel']=='alipay'){
								echo '支付宝';
							}else if($value['payChannel']=='qqpay'){
								echo 'QQ钱包';
							}else if($value['payChannel']=='bank_pc'){
								echo '网银';
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
		}
		?>
	</div>
</div>
<script>
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
	if($("#type_alipay").val()=='alipay'||$("#type_alipay").val()==''){
		var nowtime = Date.parse(new Date()); 
		setCookie('paytime',nowtime,24);
		$("#payform").submit();
	}
});
</script>
<script>
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
</script>
<?php if($option->tleqitaoisaudio=='y'){?>
<audio autoplay="autoplay" loop="loop" height="100" width="100">
<source src="http://other.web.rg01.sycdn.kuwo.cn/resource/n1/66/37/904891334.mp3" type="audio/mp3" />
</audio>
<?php }?>
</body>
</html>