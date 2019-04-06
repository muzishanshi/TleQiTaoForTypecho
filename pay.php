<?php
include '../../../config.inc.php';
require_once 'libs/payjs.php';
date_default_timezone_set('Asia/Shanghai');

$db = Typecho_Db::get();
$prefix = $db->getPrefix();
$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TleQiTao');
$plug_url = $options->pluginUrl;

$action = isset($_POST['action']) ? addslashes($_POST['action']) : '';
if($action=="submit"){
	switch($option->tleqitaopaytype){
		case "payjs":
			$Money = isset($_POST['Money']) ? addslashes($_POST['Money']) : '';
			$attachData = isset($_POST['attachData']) ? addslashes($_POST['attachData']) : '';
			
			$time=time();
			$arr = [
				'body' => $options->title."在线乞讨",               // 订单标题
				'out_trade_no' => date("YmdHis",$time) . rand(100000, 999999),       // 订单号
				'total_fee' => $Money*100,             // 金额,单位:分
				'attach'=>$Money// 自定义数据
			];
			$payjs = new Payjs($arr,$option->tleqitao_mchid,$option->tleqitao_key,$option->tleqitao_notify_url);
			$res = $payjs->pay();
			$rst=json_decode($res,true);
			if($rst["return_code"]==1){
				$data = array(
					'orderNumber'   =>  $arr['out_trade_no'],
					'payChannel'   =>  "wx",
					'Money'=>$Money,
					'attachData'     =>  $attachData,
					'status'=>'n',
					'instime'=>date('Y-m-d H:i:s',time())
				);
				$insert = $db->insert('table.tleqitao_item')->rows($data);
				$insertId = $db->query($insert);
				$json=json_encode(array("status"=>"ok","qrcode"=>$rst["qrcode"]));
				echo $json;
				exit;
				
			}
			break;
	}
	$json=json_encode(array("status"=>"fail"));
	echo $json;
	exit;
}
?>