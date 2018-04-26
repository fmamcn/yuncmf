<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | Ueditor控制器
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Common;
use think\Db;

class Ueditor extends Common
{
	protected $config;
	protected $type;
	protected function _initialize()
	{
		parent::_initialize();
		$adminid=session('admin_auth.aid');
		$userid=session('hid');
		if(empty($adminid) && empty($userid)){
			exit("非法上传！");
		}
	}
	/**
	 * 上传
	 */
	public function upload()
	{
		$this->type=input('edit_type','');
		date_default_timezone_set("Asia/Chongqing");
		error_reporting(E_ERROR);
		header("Content-Type: text/html; charset=utf-8");
		$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./public/ueditor/config.json")), true);
		$this->config=$CONFIG;
		$action = input('action');
		switch ($action) {
			case 'config':
				$result =  json_encode($CONFIG);
				break;
		
				/* 上传图片 */
			case 'uploadimage':
				$result = $this->_ueditor_upload();
				break;
				/* 上传涂鸦 */
			case 'uploadscrawl':
				$result = $this->_ueditor_upload_scrawl();
				break;
				/* 上传视频 */
			case 'uploadvideo':
				$result = $this->_ueditor_upload(array('maxSize' => 1073741824,/*1G*/'exts'=>array('mp4', 'avi', 'wmv','rm','rmvb','mkv')));
				break;
				/* 上传文件 */
			case 'uploadfile':
				$result = $this->_ueditor_upload(array('exts'=>array('jpg', 'gif', 'png', 'jpeg','txt','pdf','doc','docx','xls','xlsx','zip','rar','ppt','pptx',)));
				break;
		
				/* 列出图片 */
			case 'listimage':
				/* 列出文件 */
			case 'listfile':
				$result = $this->_ueditor_list($action);
				break;		
				/* 抓取远程文件 */
			case 'catchimage':
				$result = $this->_ueditor_upload_catch();
				break;
		
			default:
				$result = json_encode(array('state'=> '请求地址出错'));
				break;
		}
		
		/* 输出结果 */
		if (isset($_GET["callback"]) && false ) {
			if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
				echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
			} else {
				echo json_encode(array(
						'state'=> 'callback参数不合法'
				));
			}
		} else {
			exit($result) ;
		}
	}
	private function _ueditor_list($action)
	{
		/* 判断类型 */
		switch ($action) {
			/* 列出文件 */
			case 'listfile':
				$allowFiles = $this->config['fileManagerAllowFiles'];
				$listSize = $this->config['fileManagerListSize'];
				$prefix='file/';
				break;
			/* 列出图片 */
			case 'listimage':
			default:
				$allowFiles = $this->config['imageManagerAllowFiles'];
				$listSize = $this->config['imageManagerListSize'];
				$prefix='image/';
		}
		$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
		/* 获取参数 */
		$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
		$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
		$end = intval($start) + intval($size);
		/* 获取文件列表 */
		$path = './data/upload/';
		$files = $this->getfiles($path, $allowFiles);
		if (!count($files)) {
			return json_encode(array(
				"state" => "no match file",
				"list" => array(),
				"start" => $start,
				"total" => count($files)
			));
		}
		/* 获取指定范围的列表 */
		$len = count($files);
		for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
			$list[] = $files[$i];
		}
		/* 返回数据 */
		$result = json_encode(array(
			"state" => "SUCCESS",
			"list" => $list,
			"start" => $start,
			"total" => count($files)
		));
		return $result;
	}
	/**
	 * 遍历获取目录下的指定类型的文件
	 * @author  翟翟 <979113800@qq.com>
	 * @param string $path
	 * @param string $allowFiles
	 * @param array $files
	 * @return array
	 */
	private function getfiles($path, $allowFiles, &$files = array())
	{
		if (!is_dir($path)) return null;
		if(substr($path, strlen($path) - 1) != '/') $path .= '/';
		$handle = opendir($path);
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..') {
				$path2 = $path . $file;
				if (is_dir($path2)) {
					$this->getfiles($path2, $allowFiles, $files);
				} else {
					if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
						$files[] = array(
							'url'=> __ROOT__.substr($path2,1),
							'mtime'=> filemtime($path2)
						);
					}
				}
			}
		}
		return $files;
	}
	//上传
	private function _ueditor_upload($config=array())
	{
		$title = '';
		$url='';
		if(!empty($config)){
			$this->config=array_merge($this->config,$config);;
		}
		//本地上传
		$file = request()->file('upfile');
		if($file){
			$validate=array(
				'size'=>1003145728 ,// 设置附件上传大小
				//'ext'=>array('jpg', 'gif', 'png', 'jpeg'),
			);
			$info = $file->validate($validate)->rule('uniqid')->move(ROOT_PATH . config('upload_path') . DS . date('Y-m-d'));
			if($info) {
				$img_url=config('upload_path'). '/' . date('Y-m-d') . '/' . $info->getFilename();
				//写入本地文件数据库
				$data['uptime']=time();
				$data['filesize']=$info->getSize();
				$data['path']=$img_url;
				Db::name('plug_files')->insert($data);
				$title =$info->getFilename();
				$state = 'SUCCESS';
				$url= __ROOT__.$img_url;
			}else{
				$state = $file->getError();
			}
		}else{
			$state = '未接收到文件';
		}
		$response=array(
			"state" => $state,
			"url" => $url,
			"title" => $title,
			"original" =>$title,
		);
		return json_encode($response);
	}
	//涂鸦
	private function _ueditor_upload_scrawl()
	{		
		$data = input('post.' . $this->config ['scrawlFieldName']);
		$url='';
		$title = '';
		$oriName = '';
		if (empty ($data)) {
			$state= 'Scrawl Data Empty!';
		} else {
			//本地存储
			$img = base64_decode($data);
			$savepath = save_storage_content('png', $img);
			if ($savepath) {
				$state = 'SUCCESS';
				$url = __ROOT__.'/'.$savepath;
				//写入数据库
				$file['uptime']=time();
				$file['filesize']=strlen($img);
				$file['path']='/'.$savepath;
				Db::name('plug_files')->insert($file);
			} else {
				$state = 'Save scrawl file error!';
			}
		}
		$response=array(
		"state" => $state,
		"url" => $url,
		"title" => $title,
		"original" =>$oriName ,
		);
		return json_encode($response);
	}
	//抓取远程文件
	private function _ueditor_upload_catch()
	{
		set_time_limit(0);
		$sret = array(
			'state' => '',
			'list' => null
		);
		$savelist = array();
		$flist = input('post.' . $this->config ['catcherFieldName'].'/a');
		if (empty ($flist)) {
			$sret ['state'] = 'ERROR';
		} else {
			$sret ['state'] = 'SUCCESS';
			foreach ($flist as $f) {
				if (preg_match('/^(http|ftp|https):\\/\\//i', $f)) {
					$ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
					if (in_array('.' . $ext, $this->config ['catcherAllowFiles'])) {
						if ($img = file_get_contents($f)) {
							//本地
							$savepath = save_storage_content($ext, $img);
							if ($savepath) {
								$savelist [] = array(
									'state' => 'SUCCESS',
									'url' => __ROOT__.'/'.$savepath,
									'size' => strlen($img),
									'title' => '',
									'original' => '',
									'source' => htmlspecialchars($f)
								);
								//写入数据库
								$data['uptime']=time();
								$data['filesize']=strlen($img);
								$data['path']='/'.$savepath;
								Db::name('plug_files')->insert($data);
							} else {
								$savelist [] = array(
									'state' => 'Save remote file error!',
									'url' => '',
									'size' => '',
									'title' => '',
									'original' => '',
									'source' => htmlspecialchars($f),
								);
							}
						} else {
							$savelist [] = array(
							'state' => 'Get remote file error',
							'url' => '',
							'size' => '',
							'title' => '',
							'original' => '',
							'source' => htmlspecialchars($f),
							);
						}
					} else {
						$sret ['state'] = 'File ext not allowed';
					}
				} else {
					$savelist [] = array(
						'state' => 'not remote image',
						'url' => '',
						'size' => '',
						'title' => '',
						'original' => '',
						'source' => htmlspecialchars($f),
					);
				}
			}
			$sret ['list'] = $savelist;
		}
		return json_encode($sret);
	}
}