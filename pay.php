<?php
include '../../../config.inc.php';
require_once 'libs/payjs.php';
date_default_timezone_set('Asia/Shanghai');

$db = Typecho_Db::get();
$prefix = $db->getPrefix();
$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TleQiTao');
$plug_url = $options->pluginUrl;
$time=time();

$action = isset($_POST['action']) ? addslashes($_POST['action']) : '';
if($action=="submit"){
	switch($option->tleqitaopaytype){
		case "payjs":
			$qitao_payjstype = isset($_POST['qitao_payjstype']) ? addslashes($_POST['qitao_payjstype']) : '';
			switch($qitao_payjstype){
				case "native":
					$url = isset($_POST['url']) ? addslashes($_POST['url']) : '';
					$Money = isset($_POST['Money']) ? addslashes($_POST['Money']) : '';
					$attachData = isset($_POST['attachData']) ? addslashes($_POST['attachData']) : '';
					
					$arr = [
						'body' => $options->title."在线乞讨",               // 订单标题
						'out_trade_no' => date("YmdHis",$time) . rand(100000, 999999),       // 订单号
						'total_fee' => $Money*100,             // 金额,单位:分
						'attach'=>$Money// 自定义数据
					];
					$tleqitao_return_url=$option->tleqitao_return_url."?id=".$arr['out_trade_no']."&url=".base64_encode($url);
					$payjs = new Payjs($arr,$option->tleqitao_mchid,$option->tleqitao_key,$option->tleqitao_notify_url,$tleqitao_return_url);
					$res = $payjs->pay();
					$rst=json_decode($res,true);
					if($rst["return_code"]==1){
						$data = array(
							'orderNumber'   =>  $arr['out_trade_no'],
							'payChannel'   =>  "wx",
							'Money'=>$Money,
							'attachData'     =>  $attachData,
							'status'=>'n',
							'instime'=>date('Y-m-d H:i:s',$time)
						);
						$insert = $db->insert('table.tleqitao_item')->rows($data);
						$insertId = $db->query($insert);
						$json=json_encode(array("status"=>"ok","type"=>"native","qrcode"=>$rst["qrcode"]));
						echo $json;
						exit;
						
					}
					break;
				case "cashier":
					$json=json_encode(array("status"=>"ok","type"=>"cashier"));
					echo $json;
					exit;
					break;
			}
			break;
	}
	$json=json_encode(array("status"=>"fail"));
	echo $json;
	exit;
}else{
	switch($option->tleqitaopaytype){
		case "payjs":
			$qitao_payjstype = isset($_GET['qitao_payjstype']) ? addslashes($_GET['qitao_payjstype']) : '';
			switch($qitao_payjstype){
				case "cashier":
					$url = isset($_GET['url']) ? addslashes($_GET['url']) : '';
					$Money = isset($_GET['Money']) ? addslashes($_GET['Money']) : '';
					$attachData = isset($_GET['attachData']) ? addslashes($_GET['attachData']) : '';
					
					$cashierapi="https://payjs.cn/api/cashier";
					$arr = [
						'body' => $options->title."在线乞讨",               // 订单标题
						'out_trade_no' => date("YmdHis",$time) . rand(100000, 999999),       // 订单号
						'total_fee' => $Money*100,             // 金额,单位:分
						'attach'=>$Money// 自定义数据
					];
					$tleqitao_return_url=$option->tleqitao_return_url."?id=".$arr['out_trade_no']."&url=".base64_encode($url);
					$payjs = new Payjs($arr,$option->tleqitao_mchid,$option->tleqitao_key,$option->tleqitao_notify_url,$tleqitao_return_url,$cashierapi);
					$data = $arr;
					$data['mchid'] = $option->tleqitao_mchid;
					$data['callback_url'] = $tleqitao_return_url;
					$data['notify_url'] = $option->tleqitao_notify_url;
					$data['auto'] = 1;
					$data['hide'] = 1;
					$sign = $payjs->sign($data);
					$data = array(
						'orderNumber'   =>  $arr['out_trade_no'],
						'payChannel'   =>  "wx",
						'Money'=>$Money,
						'attachData'     =>  $attachData,
						'status'=>'n',
						'instime'=>date('Y-m-d H:i:s',$time)
					);
					$insert = $db->insert('table.tleqitao_item')->rows($data);
					$insertId = $db->query($insert);
					echo '
						<form id="payform" action="'.$cashierapi.'" method="post">
							<input type="hidden" name="mchid" value="'.$option->tleqitao_mchid.'" />
							<input type="hidden" name="total_fee" value="'.$arr["total_fee"].'" />
							<input type="hidden" name="out_trade_no" value="'.$arr["out_trade_no"].'" />
							<input type="hidden" name="body" value="'.$arr["body"].'" />
							<input type="hidden" name="attach" value="'.$arr["attach"].'" />
							<input type="hidden" name="callback_url" value="'.$tleqitao_return_url.'" />
							<input type="hidden" name="notify_url" value="'.$option->tleqitao_notify_url.'" />
							<input type="hidden" name="auto" value="1" />
							<input type="hidden" name="hide" value="1" />
							<input type="hidden" name="sign" value="'.$sign.'" />
						</form>
						<script>document.getElementById("payform").submit();</script>
					';
					break;
			}
			$json=json_encode(array("status"=>"fail"));
			echo $json;
			exit;
			break;
	}
}
?>