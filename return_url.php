<?php
/*
 * 同步回调通知页面
 */
include '../../../config.inc.php';
require_once 'libs/ispay/lib/Ispay.class.php';

$db = Typecho_Db::get();
$pluginsname='TleQiTao';
$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin($pluginsname);
$plug_url = $options->pluginUrl;

$Ispay = new ispayService($option->tleqitaoispayid, $option->tleqitaoispaykey);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
$Array['payChannel'] = @$_POST['payChannel'];
$Array['Money'] = @$_POST['Money'];
$Array['orderNumber'] = @$_POST['orderNumber'];
$Array['attachData'] = @$_POST['attachData'];
$Array['callbackSign'] = @$_POST['callbackSign'];
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
			<div class="panel-body">
				<center>
					<div class="alert alert-success">
						<?php
						//回调签名校验
						if(!$Ispay->callbackSignCheck($Array)){
							echo "难道是天意？居然施舍失败了……";
							echo '<br /><a href="'.BLOG_URL.'qitao">返回</a>';
						}else{
							$update = $db->update('table.tleqitao_item')->rows(array('status'=>'y'))->where('orderNumber=?',$Array['orderNumber']);
							$updateRows= $db->query($update);
							?>
							<div>
								<h2>施舍成功</h2>
								<p>谢谢您施舍<?php echo @$Array['Money']/100; ?> 元，祝您好人有好报！</p>
							</div>
							<a href="<?php echo $Array['attachData'];?>">返回</a>
							<?php
						}
						?>
					</div>
				</center>
			</div>
		</div>
	</div>
</div>
</body>
</html>