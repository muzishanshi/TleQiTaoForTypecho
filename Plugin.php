<?php
/**
 * TleQiTao是一个简易的乞讨Typecho插件
 * @package TleQiTao For Typecho
 * @author 二呆
 * @version 1.0.1
 * @link http://www.tongleer.com/
 * @date 2018-09-13
 */

class TleQiTao_Plugin implements Typecho_Plugin_Interface{
    // 激活插件
    public static function activate(){
		$db = Typecho_Db::get();
		$prefix = $db->getPrefix();
		self::createTableQiTaoItem($db);
		//判断目录权限，并将插件文件写入主题目录
		self::funWriteThemePage($db,'page_tleqitao.php');
		//如果数据表没有添加页面就插入
		self::funWriteDataPage($db,'乞讨','tleqitao','page_tleqitao.php','publish');
        return _t('插件已经激活，需先配置微博图床的信息！');
    }

    // 禁用插件
    public static function deactivate(){
		//删除页面模板
		$db = Typecho_Db::get();
		$queryTheme= $db->select('value')->from('table.options')->where('name = ?', 'theme'); 
		$rowTheme = $db->fetchRow($queryTheme);
		@unlink(dirname(__FILE__).'/../../themes/'.$rowTheme['value'].'/page_tleqitao.php');
        return _t('插件已被禁用');
    }

    // 插件配置面板
    public static function config(Typecho_Widget_Helper_Form $form){
		//版本检查
		$version=file_get_contents('http://api.tongleer.com/interface/TleQiTao.php?action=update&version=1');
		$headDiv=new Typecho_Widget_Helper_Layout();
		$headDiv->html('版本检查：'.$version);
		$headDiv->render();
		
        $tleqitaoispayid = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoispayid', null, '', _t('ispayid'), _t('在<a href="https://www.ispay.cn/" target="_blank">ispay官网</a>注册的payId'));
        $form->addInput($tleqitaoispayid->addRule('required', _t('ispayid不能为空！')));
		$tleqitaoispaykey = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoispaykey', null, '', _t('ispaykey'), _t('在<a href="https://www.ispay.cn/" target="_blank">ispay官网</a>注册的payKey'));
        $form->addInput($tleqitaoispaykey->addRule('required', _t('ispayid不能为空！')));
		
		$tleqitaoqq = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoqq', array("value"), '2293338477', _t('QQ号'), _t('通过QQ号自动获取头像地址和联系QQ链接'));
        $form->addInput($tleqitaoqq);
		$tleqitaotalk = new Typecho_Widget_Helper_Form_Element_Text('tleqitaotalk', array("value"), '', _t('想说的话'), _t('如果填写用户可看到你乞讨的缘由'));
        $form->addInput($tleqitaotalk);
    }
	
    // 个人用户配置面板
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
    }

    // 获得插件配置信息
    public static function getConfig(){
        return Typecho_Widget::widget('Widget_Options')->plugin('TleQiTao');
    }
	
	/*创建乞讨数据表*/
	public static function createTableQiTaoItem($db){
		$prefix = $db->getPrefix();
		//$db->query('DROP TABLE IF EXISTS '.$prefix.'weibofile_videoupload');
		$db->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'tleqitao_item` (
		  `orderNumber` varchar(125) COLLATE utf8_bin NOT NULL,
		  `payChannel` varchar(255) COLLATE utf8_bin DEFAULT NULL,
		  `Money` int(11) DEFAULT NULL,
		  `attachData` varchar(255) COLLATE utf8_bin DEFAULT NULL,
		  `status` enum("y","n") COLLATE utf8_bin DEFAULT "n",
		  `instime` datetime DEFAULT NULL,
		  PRIMARY KEY (`orderNumber`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');
	}
	
	/*公共方法：将页面写入数据库*/
	public static function funWriteDataPage($db,$title,$slug,$template,$status="hidden"){
		$query= $db->select('slug')->from('table.contents')->where('template = ?', $template); 
		$row = $db->fetchRow($query);
		if(count($row)==0){
			$contents = array(
				'title'      =>  $title,
				'slug'      =>  $slug,
				'created'   =>  Typecho_Date::time(),
				'text'=>  '<!--markdown-->',
				'password'  =>  '',
				'authorId'     =>  Typecho_Cookie::get('__typecho_uid'),
				'template'     =>  $template,
				'type'     =>  'page',
				'status'     =>  $status,
			);
			$insert = $db->insert('table.contents')->rows($contents);
			$insertId = $db->query($insert);
			$slug=$contents['slug'];
		}else{
			$slug=$row['slug'];
		}
	}
	
	/*公共方法：将页面写入主题目录*/
	public static function funWriteThemePage($db,$filename){
		$queryTheme= $db->select('value')->from('table.options')->where('name = ?', 'theme'); 
		$rowTheme = $db->fetchRow($queryTheme);
		$path=dirname(__FILE__).'/../../themes/'.$rowTheme['value'];
		if(!is_dir($path."/templates/")){
			mkdir ($path."/templates/", 0777, true );
		}
		if(!is_writable($path)){
			Typecho_Widget::widget('Widget_Notice')->set(_t('主题目录不可写，请更改目录权限。'.__TYPECHO_THEME_DIR__.'/'.$rowTheme['value']), 'success');
		}
		if(!file_exists($path."/".$filename)){
			$regfile = @fopen(dirname(__FILE__)."/page/".$filename, "r") or die("不能读取".$filename."文件");
			$regtext=fread($regfile,filesize(dirname(__FILE__)."/page/".$filename));
			fclose($regfile);
			$regpage = fopen($path."/".$filename, "w") or die("不能写入".$filename."文件");
			fwrite($regpage, $regtext);
			fclose($regpage);
		}
	}

}