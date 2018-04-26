<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 文章管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use app\admin\model\Article as ArticleModel;

class Article extends Base
{
	/*
	 * 显示分类列表
	 */
	public function article_classify()
	{
		$classify_list=Db::name('article_classify')->order('classify_order,classify_id desc')->paginate(config('paginate.list_rows'));
		$page = $classify_list->render();
		$this->assign('classify_list',$classify_list);
		$this->assign('page',$page);
		return $this->fetch();
	}
	/*
	 * 添加分类
	 */
	public function classify_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Article/article_classify'));
		}else{
			$data=input('post.');
			Db::name('article_classify')->insert($data);
			$this->success('添加分类成功',url('admin/Article/article_classify'));
		}
	}
	/*
	 * 分类排序
	 */
	public function classify_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Article/article_classify'));
		}else{
			foreach (input('post.') as $classify_id => $classify_order){
				Db::name('article_classify')->where(array('classify_id' => $classify_id ))->setField('classify_order', $classify_order);
			}
			$this->success('排序更新成功',url('admin/Article/article_classify'));
		}
	}
	/*
	 * 删除分类
	 */
	public function classify_del()
	{
		$p=input('p');
		$rst=Db::name('article_classify')->where(array('classify_id'=>input('classify_id')))->delete();
		if($rst!==false){
			$this->success('删除分类成功',url('admin/Article/article_classify',array('p' => $p)));
		}else{
			$this->error('删除分类失败',url('admin/Article/article_classify',array('p' => $p)));
		}
	}
	/*
	 * 返回分类值
	 */
	public function classify_edit()
	{
		$classify_id=input('id');
		$classify=Db::name('article_classify')->where(array('classify_id'=>$classify_id))->find();
		$sl_data['classify_id']=$classify['classify_id'];
		$sl_data['classify_name']=$classify['classify_name'];
		$sl_data['classify_description']=$classify['classify_description'];
		$sl_data['img_w']=$classify['img_w'];
		$sl_data['img_h']=$classify['img_h'];
		$sl_data['classify_order']=$classify['classify_order'];
		$sl_data['code']=1;
		return json($sl_data);
	}
	/*
	 * 修改分类
	 */
	public function classify_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Article/article_classify'));
		}else{
			$sl_data=array(
				'classify_id'=>input('classify_id'),
				'classify_name'=>input('classify_name'),
				'classify_description'=>input('classify_description'),
				'img_w'=>input('img_w'),
				'img_h'=>input('img_h'),
				'classify_order'=>input('classify_order'),
			);
			$rst=Db::name('article_classify')->update($sl_data);
			if($rst!==false){
				$this->success('修改成功',url('admin/Article/article_classify'));
			}else{
				$this->error('修改失败',url('admin/Article/article_classify'));
			}
		}
	}	
	/**
	 * 显示文章列表
	 */
	public function article_list()
	{
		//关键字类型
		$keytype=input('keytype','title');
		$this->assign('keytype',$keytype);
		//关键字
		$key=input('key','');
		$this->assign('keyy',$key);
		//状态
		$opentype_check=input('opentype_check','');
		$this->assign('opentype_check',$opentype_check);
		//分页大小
		$pagesize=input('pagesize',10);
		$this->assign('pagesize',$pagesize);
		//前端菜单分类
		$columnid=input('columnid','');
		$this->assign('columnid',$columnid);
		//文章分类
		$classid=input('classid','');
		$this->assign('classid',$classid);
		//查询：时间格式过滤 获取格式 2017-11-12 - 2017-11-18
		$sldate=input('reservation','');
		$this->assign('sldate',$sldate);
		$arr = explode(" - ",$sldate);
		if(count($arr)==2){
			$arrdateone=strtotime($arr[0]);
			$arrdatetwo=strtotime($arr[1].' 23:59:59');
			$map['time'] = array(array('egt',$arrdateone),array('elt',$arrdatetwo),'AND');
		}
		//是否在回收站
		$map['back']= 0;
		if(!empty($key)){
			if($keytype=='title'){
				$map[$keytype]= array('like',"%".$key."%");
			}elseif($keytype=='author'){
				$map['member_username']= array('like',"%".$key."%");
			}else{
				$map[$keytype]= $key;
			}
		}
		if ($opentype_check!=''){
			$map['open']= $opentype_check;
		}
		if ($columnid!=''){
			$map['columnid']= $columnid;
		}
		if ($classid!=''){
			$map['classid']= $classid;
		}
		$article_model=new ArticleModel;
		$article=$article_model->alias("a")->field('a.*,b.*,c.classify_name,d.menu_name')
								->join(config('database.prefix').'member b','a.auto =b.member_id')
								->join(config('database.prefix').'article_classify c','a.classid =c.classify_id')
								->join(config('database.prefix').'menu d','a.columnid =d.id')
								->where($map)->order('time desc')->paginate($pagesize,false,['query'=>get_query()]);
		$show = $article->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('page',$show);
		//分类数据
		$classify=Db::name('article_classify')->select();
		$this->assign('classify',$classify);
		//前端栏目数据
		$menu_text=menu_text();
		$this->assign('menu',$menu_text);
		$this->assign('article',$article);
		if(request()->isAjax()){
			return $this->fetch('ajax_article_list');
		}else{
			return $this->fetch();
		}		
	}
	/**
	 * 显示添加页面
	 */
	public function article_add()
	{
		//当前选择分类
		$columnid=input('columnid',0,'intval');
		$this->assign('columnid',$columnid);
		//分类数据
		$classify=Db::name('article_classify')->select();
		$this->assign('classify',$classify);
		//标签数据
		$where['flag_type']= 0;
		$flag=Db::name('plug_flag')->where($where)->select();
		$this->assign('flag',$flag);
		//来源数据
		$source=Db::name('plug_source')->select();
		$this->assign('source',$source);
		//菜单数据
		$menu_text=menu_text();
		$this->assign('menu',$menu_text);
		return $this->fetch();
	}
	/**
	 * 添加文章
	 */
	public function article_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Article/article_add'));
		}
		//上传图片部分
		$img_w='';
		$img_h='';
		$picall_url='';
		$file_w = input('pic_w','');
		$file_h = input('pic_h','');
		$files = input('pic_all','');
		if($file_w || $file_h || $files) {
			$validate = config('upload_validate');
			//单图
			if ($file_w || $file_h) {
				$img_w=$file_w;
				$img_h=$file_h;
			}
			//多图
			if ($files) {
				$picall_url=$files;
			}
		}
		//获取标签
		$article_flag=input('post.flag/a');
		$flag=array();
		if(!empty($article_flag)){
			foreach ($article_flag as $v){
				$flag[]=$v;
			}
		}
		//显示时间
		$showtime=input('showdate','');
		$modifytime=($showtime=='')?time():strtotime($showtime);
		$flagdata=implode(',',$flag);
		$sl_data=array(
			'title'=>input('title'),
			'subtitle'=>input('subtitle',''),
			'columnid'=>input('columnid'),
			'classid'=>input('classid'),
			'key'=>input('key',''),
			'flag'=>$flagdata,
			'source'=>input('source',''),
			'img_w'=>$img_w,//封面图片(横屏)路径
			'img'=>$img_h,//封面图片(竖屏)路径
			'pic_allurl'=>$picall_url,//多图路径
			'pic_content'=>input('pic_content',''),
			'comment_status'=>input('comment_status',1),
			'hits'=>input('hits',200),
			'lvtype'=>input('lvtype',0),
			'cpprice'=>input('cpprice',8),
			'scontent'=>input('scontent',''),
			'content'=>htmlspecialchars_decode(input('content')),
			'auto'=>session('admin_auth.member_id'),
			'time'=>time(),
			'modified'=>$modifytime,
			'listorder'=>input('listorder',50,'intval'),
		);
		ArticleModel::create($sl_data);
		$continue=input('continue',0,'intval');
		if($continue){
			$this->success('添加文章成功,继续发布',url('admin/Article/article_add',['columnid'=>input('columnid')]));
		}else{
			$this->success('添加文章成功,返回列表页',url('admin/Article/article_list'));
		}
	}
	/**
	 * 文章排序
	 */
	public function article_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Article/article_list'));
		}else{
			$list=[];
			foreach (input('post.') as $n_id => $order){
				$list[]=['n_id'=>$n_id,'listorder'=>$order];
			}
			$article_model=new ArticleModel;
			$article_model->saveAll($list);
			$this->success('排序更新成功',url('admin/Article/article_list'));
		}
	}
	/**
	 * 删除至回收站(单个)
	 */
	public function article_del()
	{
		$p=input('p');
		$article_model=new ArticleModel;
		$rst=$article_model->where(array('n_id'=>input('n_id')))->setField('back',1);//转入回收站
		if($rst!==false){
			$this->success('文章已转入回收站',url('admin/Article/article_list',array('p' => $p)));
		}else{
			$this -> error("删除文章失败！",url('admin/Article/article_list',array('p'=>$p)));
		}
	}
	/**
	 * 删除至回收站(全选)
	 */
	public function article_alldel()
	{
		$p = input('p');
		$ids = input('n_id/a');
		if(empty($ids)){
			$this -> error("请选择需要删除的文章",url('admin/Article/article_list',array('p'=>$p)));//判断是否选择了文章ID
		}
		if(is_array($ids)){//判断获取文章ID的形式是否数组
			$where = 'n_id in('.implode(',',$ids).')';
		}else{
			$where = 'n_id='.$ids;
		}
		$article_model=new ArticleModel;
		$rst=$article_model->where($where)->setField('back',1);//转入回收站
		if($rst!==false){
			$this->success("成功把多个文章移至回收站！",url('admin/Article/article_list',array('p'=>$p)));
		}else{
			$this -> error("删除文章失败！",url('admin/Article/article_list',array('p'=>$p)));
		}
	}
	/**
	 * 审核文章
	 */
	public function article_state()
	{
		$id=input('x');
		$article_model=new ArticleModel;
		$status=$article_model->where(array('n_id'=>$id))->value('open');
		if($status==1){
			$statedata = array('open'=>0);
			$article_model->where(array('n_id'=>$id))->setField($statedata);
			$this->success('未审');
		}else{
			$statedata = array('open'=>1);
			$article_model->where(array('n_id'=>$id))->setField($statedata);
			$this->success('已审');
		}
	}
	/**
	 * 显示回收站列表
	 */
	public function article_back()
	{
		//关键字类型
		$keytype=input('keytype','title');
		$this->assign('keytype',$keytype);
		//关键字
		$key=input('key','');
		$this->assign('keyy',$key);
		//状态
		$opentype_check=input('opentype_check','');
		$this->assign('opentype_check',$opentype_check);
		//分页大小
		$pagesize=input('pagesize',10);
		$this->assign('pagesize',$pagesize);
		//前端菜单分类
		$columnid=input('columnid','');
		$this->assign('columnid',$columnid);
		//文章分类
		$classid=input('classid','');
		$this->assign('classid',$classid);
		//查询：时间格式过滤 获取格式 2017-11-12 - 2017-11-18
		$sldate=input('reservation','');
		$this->assign('sldate',$sldate);
		$arr = explode(" - ",$sldate);
		if(count($arr)==2){
			$arrdateone=strtotime($arr[0]);
			$arrdatetwo=strtotime($arr[1].' 23:59:59');
			$map['time'] = array(array('egt',$arrdateone),array('elt',$arrdatetwo),'AND');
		}
		//是否在回收站
		$map['back']= 1;
		if(!empty($key)){
			if($keytype=='title'){
				$map[$keytype]= array('like',"%".$key."%");
			}elseif($keytype=='author'){
				$map['member_username']= array('like',"%".$key."%");
			}else{
				$map[$keytype]= $key;
			}
		}
		if ($opentype_check!=''){
			$map['open']= $opentype_check;
		}
		if ($columnid!=''){
			$map['columnid']= $columnid;
		}
		if ($classid!=''){
			$map['classid']= $classid;
		}
		$article_model=new ArticleModel;
		$article=$article_model->alias("a")->field('a.*,b.*,c.classify_name,d.menu_name')
								->join(config('database.prefix').'member b','a.auto =b.member_id')
								->join(config('database.prefix').'article_classify c','a.classid =c.classify_id')
								->join(config('database.prefix').'menu d','a.columnid =d.id')
								->where($map)->order('time desc')->paginate($pagesize,false,['query'=>get_query()]);
		$show = $article->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('page',$show);
		//分类数据
		$classify=Db::name('article_classify')->select();
		$this->assign('classify',$classify);
		//前端栏目数据
		$menu_text=menu_text();
		$this->assign('menu',$menu_text);
		$this->assign('article',$article);
		if(request()->isAjax()){
			return $this->fetch('ajax_article_list');
		}else{
			return $this->fetch();
		}		
	}
	/**
	 * 还原文章
	 */
	public function article_back_open()
	{
		$p=input('p');
		$article_model=new ArticleModel;
		$rst=$article_model->where('n_id',input('n_id'))->setField('back',0);//还原
		if($rst!==false){
			$this->success('还原文章成功',url('admin/Article/article_back',array('p' => $p)));
		}else{
			$this->error("还原文章失败！",url('admin/Article/article_back',array('p' => $p)));
		}
	}
	/**
	 * 彻底删除(单个)
	 */
	public function article_back_del()
	{
		$n_id=input('n_id');
		$p = input('p');
		$article_model=new ArticleModel;
		if (empty($n_id)){
			$this->error('参数错误',url('admin/Article/article_back',array('p' => $p)));
		}else{
			$rst=$article_model->where('n_id',input('n_id'))->delete();
			if($rst!==false){
				$this->success('彻底删除文章成功',url('admin/Article/article_back',array('p' => $p)));
			}else{
				$this -> error("彻底删除文章失败！",url('admin/Article/article_back',array('p' => $p)));
			}
		}
	}
	/**
	 * 彻底删除(全选)
	 */
	public function article_back_alldel()
	{
		$p = input('p');
		$ids = input('n_id/a');
		if(empty($ids)){
			$this -> error("请选择需要删除的文章",url('admin/Article/article_back',array('p'=>$p)));//判断是否选择了文章ID
		}
		if(is_array($ids)){//判断获取文章ID的形式是否数组
			$where = 'n_id in('.implode(',',$ids).')';
		}else{
			$where = 'n_id='.$ids;
		}
		$article_model=new ArticleModel;
		$rst=$article_model->where($where)->delete();
		if($rst!==false){
			$this->success("成功将多个文章删除，不可还原！",url('admin/Article/article_back',array('p'=>$p)));
		}else{
			$this -> error("删除失败！",url('admin/Article/article_back',array('p' => $p)));
		}
	}
	/**
	 * 显示编辑页面
	 */
	public function article_edit()
	{
		$n_id = input('n_id');
		if (empty($n_id)){
			$this->error('参数错误',url('admin/Article/article_list'));
		}
		$article=ArticleModel::get($n_id);
		//多图路径转换
		$allurl_text = $article['pic_allurl'];
		$this->assign('pic_list',$allurl_text);
		//多图说明转换
		$content_text = $article['pic_content'];
		$this->assign('pic_content_list',$content_text);
		//菜单数据
		$menu_text=menu_text();
		$this->assign('menu',$menu_text);
		//分类数据
		$classify=Db::name('article_classify')->select();
		$this->assign('classify',$classify);
		//本文章所属分类
		$classid=$article['classid'];
		$this->assign('classid',$classid);
		//标签数据
		$where['flag_type']= 0;
		$flag=Db::name('plug_flag')->where($where)->select();
		$this->assign('flag',$flag);
		//来源数据
		$source=Db::name('plug_source')->select();
		$this->assign('source',$source);
		$this->assign('article',$article);
		return $this->fetch();
	}
	/**
	 * 编辑文章
	 */
	public function article_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Article/article_list'));
		}
		//上传图片部分
		$img_w='';
		$img_h='';
		$picall_url='';
		$file_w = input('pic_w','');
		$file_h = input('pic_h','');
		$files = input('pic_all','');
		if($file_w || $file_h || $files) {
			$validate = config('upload_validate');
			//单图
			if ($file_w || $file_h) {
				$img_w=$file_w;
				$img_h=$file_h;
			}
			//多图
			if ($files) {
				$picall_url=$files;
			}
		}
		//获取标签
		$article_flag=input('post.flag/a');
		$flag=array();
		if(!empty($article_flag)){
			foreach ($article_flag as $v){
				$flag[]=$v;
			}
		}
		$flagdata=implode(',',$flag);
		//显示时间
		$showtime=input('showdate','');
		$modifytime=($showtime=='')?time():strtotime($showtime);
		$sl_data=array(
			'n_id'=>input('n_id'),
			'title'=>input('title'),
			'subtitle'=>input('subtitle',''),
			'columnid'=>input('columnid'),
			'classid'=>input('classid'),
			'key'=>input('key',''),
			'flag'=>$flagdata,
			'source'=>input('source',''),
			'img_w'=>$img_w,//封面图片(横屏)路径
			'img'=>$img_h,//封面图片(竖屏)路径
			'pic_allurl'=>$picall_url,//多图路径
			'pic_content'=>input('pic_content',''),
			'comment_status'=>input('comment_status',0)?true:false,
			'hits'=>input('hits',200),
			'lvtype'=>input('lvtype',0),
			'cpprice'=>input('cpprice',8),
			'scontent'=>input('scontent',''),
			'content'=>htmlspecialchars_decode(input('content')),
			'auto'=>session('admin_auth.member_id'),
			'modified'=>$modifytime,
		);
		$rst=ArticleModel::update($sl_data);
		if($rst!==false){
			$this->success('文章修改成功,返回列表页',url('admin/Article/article_list'));
		}else{
			$this->error('文章修改失败',url('admin/Article/article_list'));
		}
	}

//结束
}