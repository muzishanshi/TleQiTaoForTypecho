<?php
/*
 * 统一下单支付页面
 * 2017-08-02
 * https://www.ispay.cn
 */
require_once 'config.php';
require_once 'lib/Ispay.class.php';
$Ispay = new ispayService($config['payId'], $config['payKey']);
//设置时区
date_default_timezone_set('Asia/Shanghai');
//商户编号
$Request['payId'] = $config['payId'];
//支付通道
if (isset($_GET['payChannel'])) {
	$Request['payChannel'] = $_GET['payChannel'];
} else {
	$Request['payChannel'] = "alipay";
}
//订单标题
$Request['Subject'] = "测试订单";
//交易金额（单位分）
$Request['Money'] = 500;
//随机生成订单号
$Request['orderNumber'] = date("YmdHis") . rand(100000, 999999);
//附加数据（没有可不填）
$Request['attachData'] = "test";
//异步通知地址
$Request['Notify_url'] = "https://www.baidu.com";
//客户端同步跳转通知地址
$Request['Return_url'] = "https://www.ispay.cn/";
//签名（加密算法详见开发文档）
$Request['Sign'] = $Ispay -> Sign($Request);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>ISPAY支付接口测试</title>
		<link href="css/style.min.css" rel="stylesheet">
		<script src="js/jquery.min.js"></script>
	</head>
	<body>
		<div class="header">
			<div class="container black">
				<div class="qrcode"></div>
			</div>
			<div class="container">
				<div class="nav">
					<a href="https://www.ispay.cn/" class="logo">
						<img src="https://www.ispay.cn/images/logo.png" height="50px">
					</a>
				</div>
			</div>
			<div class="container blue">
				<div class="title">
					2.0支付接口测试
				</div>
			</div>
		</div>
		<div class="content">
			<form name="form1" action="https://pay.ispay.cn/core/api/request/pay/" class="alipayform" method="post" target="_blank" onsubmit="return checkMobile();">
				<div class="element">
					<div class="etitle">
						商户编号(payId):
					</div>
					<div class="einput">
						<input type="text" name="payId" value="<?php echo $Request['payId']; ?>">
					</div>
				</div>
				<div class="element">
					<div class="etitle">
						支付通道(payChannel):
					</div>
					<select class="einput einputselect" id="payChannel" name="payChannel">
						<option value ="alipay" <?php
						if (isset($_GET['payChannel'])) {
							$payChannel = $_GET['payChannel'];
						} else {
							$payChannel = 'alipay';
						}
						if ($payChannel == 'alipay') {echo 'selected = "selected"';
						}
						?>>支付宝(alipay)</option>
						<option value ="wxpay" <?php
						if ($payChannel == 'wxpay') {echo 'selected = "selected"';
						}
						?>>微信(wxpay)</option>
						<option value="qqpay" <?php
						if ($payChannel == 'qqpay') {echo 'selected = "selected"';
						}
						?>>QQ钱包(qqpay)</option>
						<option value="bank_pc" <?php
						if ($payChannel == 'bank_pc') {echo 'selected = "selected"';
						}
						?>>网银(bank_pc)</option>
						<option value="wxgzhpay" <?php
						if ($payChannel == 'wxgzhpay') {echo 'selected = "selected"';
						}
						?>>微信公众号(wxgzhpay)</option>
					</select>
				</div>
				<div class="element">
					<div class="etitle">
						订单标题(Subject):
					</div>
					<div class="einput">
						<input type="text" name="Subject" value="<?php echo $Request['Subject']; ?>">
					</div>
					<br>
				</div>
				<div class="element">
					<div class="etitle">
						金额(Money)
						<a style="color:#F00">
							单位分
						</a>
						:
					</div>
					<div class="einput">
						<input type="text" name="Money" value="<?php echo $Request['Money']; ?>">
					</div>
					<br>
				</div>
				<div class="element">
					<div class="etitle">
						订单号(orderNumber):
					</div>
					<div class="einput">
						<input type="text" name="orderNumber" value="<?php echo $Request['orderNumber']; ?>">
					</div>
					<br>
				</div>
				<div class="element">
					<div class="etitle">
						附加数据(attachData):
					</div>
					<div class="einput">
						<input type="text" name="attachData" value="<?php echo $Request['attachData']; ?>">
					</div>
					<br>
				</div>
				<div class="element">
					<div class="etitle">
						异步通知(Notify_url):
					</div>
					<div class="einput">
						<input type="text" name="Notify_url" value="<?php echo $Request['Notify_url']; ?>">
					</div>
					<br>
				</div>
				<div class="element">
					<div class="etitle">
						同步通知(Return_url):
					</div>
					<div class="einput">
						<input type="text" name="Return_url" value="<?php echo $Request['Return_url']; ?>">
					</div>
					<br>
				</div>
				<div class="element">
					<div class="etitle">
						签名(Sign):
					</div>
					<div class="einput">
						<input type="text" name="Sign" value="<?php echo $Request['Sign']; ?>">
					</div>
					<br>
				</div>
				<div class="element">
					<input type="submit" class="alisubmit" value="确认支付">
				</div>
			</form>
		</div>
		<div class="footer">
			<p class="footer-sub">
				<a href="https://www.ispay.cn">
					https://www.ispay.cn
				</a>
				<br>
				<span>安徽八八四八网络科技有限公司&nbsp;&nbsp;2016-2017&nbsp;&nbsp;ICP备：皖ICP备17000505号-2</span>
			</p>
		</div>
		<script>$("#payChannel").change(function() {
	if($("#payChannel").val() == 'alipay') {
		location.href = "index.php?payChannel=alipay";
	}
	if($("#payChannel").val() == 'wxpay') {
		location.href = "index.php?payChannel=wxpay";
	}
	if($("#payChannel").val() == 'qqpay') {
		location.href = "index.php?payChannel=qqpay";
	}
	if($("#payChannel").val() == 'bank_pc') {
		location.href = "index.php?payChannel=bank_pc";
	}
	if($("#payChannel").val() == 'wxgzhpay') {
		location.href = "index.php?payChannel=wxgzhpay";
	}
})</script>
	</body>
</html>