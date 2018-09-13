<?php
/*
 * 查询订单测试
 * 2017-08-02
 * https://www.ispay.cn
 */
require_once 'config.php';
require_once 'lib/Ispay.class.php';
$Ispay = new ispayService($config['payId'], $config['payKey']);
$Request['payId'] = 11099;
$Request['orderNumber'] = '20180318182015610095';
$res=$Ispay->callbackRequestCheck($Request);
var_dump($res);
?>