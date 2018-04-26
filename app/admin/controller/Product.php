<?php
// +----------------------------------------------------------------------
// | YunCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2018 http://www.yuninf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: LuckyZhaiZhai <979113800@qq.com>
// +----------------------------------------------------------------------
// | 产品管理
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Db;
use app\admin\model\Product as ProductModel;

class Product extends Base
{
	/*
	 * 显示第一级列表
	 */
	public function product_classify()
	{
		$pagesize=38;
		$classify=Db::name('product_classify')->where('type','1')->order('id asc')->paginate($pagesize);
		$page = $classify->render();
		$this->assign('classify',$classify);
		$this->assign('page',$page);
		return $this->fetch();
	}
	/*
	 * 添加第一级分类
	 */
	public function classify_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/product_classify'));
		}else{
			$data=input('post.');
			Db::name('product_classify')->insert($data);
			$this->success('添加成功',url('admin/Product/product_classify'));
		}
	}
	/*
	 * 返回第一级分类值
	 */
	public function classify_edit()
	{
		$id=input('id');
		$classify=Db::name('product_classify')->where(array('id'=>$id))->find();
		$sl_data['id']=$classify['id'];
		$sl_data['pid']=$classify['pid'];
		$sl_data['name']=$classify['name'];
		$sl_data['key']=$classify['key'];
		$sl_data['img_w']=$classify['img_w'];
		$sl_data['img_h']=$classify['img_h'];
		$sl_data['code']=1;
		return json($sl_data);
	}
	/*
	 * 修改第一级分类
	 */
	public function classify_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/product_classify'));
		}else{
			$sl_data=array(
				'id'=>input('id'),
				'pid'=>input('pid'),
				'name'=>input('name'),
				'key'=>input('key'),
				'img_w'=>input('img_w'),
				'img_h'=>input('img_h'),
			);
			$rst=Db::name('product_classify')->update($sl_data);
			if($rst!==false){
				$this->success('修改成功',url('admin/Product/product_classify'));
			}else{
				$this->error('修改失败',url('admin/Product/product_classify'));
			}
		}
	}
	/*
	 * 删除第一级分类
	 */
	public function classify_del()
	{
		//删除第一级分类
		$pvrst=Db::name('product_classify')->where(array('id'=>input('id')))->delete();
		//获取第二级分类id
		$second_id=Db::name('product_classify')->where(array('pid'=>input('id')))->column('id');
		if(!empty($second_id)){
			//删除分类下所有第三级分类
			for($i=0;$i<count($second_id);$i++){
				$corst=Db::name('product_classify')->where(array('pid'=>$second_id[$i]))->delete();
			}
			//删除第二级分类
			$ctrst=Db::name('product_classify')->where(array('pid'=>input('id')))->delete();
		}
		if($pvrst!==false || $corst!==false || $ctrst!==false){
			$this->success('删除成功',url('admin/Product/product_classify'));
		}else{
			$this->error('删除失败',url('admin/Product/product_classify'));
		}
	}
	/*
	 * 第二级分类列表
	 */
	public function classify_2nd()
	{
		$pagesize=100;
		$pid=input('id');
		$classify=Db::name('product_classify')->where(array('pid'=>input('id')))->order('id asc')->paginate($pagesize);
		$ppname=Db::name('product_classify')->where(array('id'=>input('id')))->value('name');
		$page = $classify->render();
		$this->assign('pid',$pid);
		$this->assign('ppname',$ppname);
		$this->assign('classify',$classify);
		$this->assign('page',$page);
		return $this->fetch();
	}
	/*
	 * 添加第二级分类
	 */
	public function classify2nd_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/classify_2nd',array('id' =>input('pid'))));
		}else{
			$data=input('post.');
			Db::name('product_classify')->insert($data);
			$this->success('添加成功',url('admin/Product/classify_2nd',array('id' =>input('pid'))));
		}
	}
	/*
	 * 返回第二级分类值
	 */
	public function classify2nd_edit()
	{
		$id=input('id');
		$classify2nd=Db::name('product_classify')->where(array('id'=>$id))->find();
		$sl_data['id']=$classify2nd['id'];
		$sl_data['pid']=$classify2nd['pid'];
		$sl_data['name']=$classify2nd['name'];
		$sl_data['key']=$classify2nd['key'];
		$sl_data['img_w']=$classify2nd['img_w'];
		$sl_data['img_h']=$classify2nd['img_h'];
		$sl_data['code']=1;
		return json($sl_data);
	}
	/*
	 * 修改第二级分类
	 */
	public function classify2nd_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/classify_2nd',array('id' =>input('pid'))));
		}else{
			$sl_data=array(
				'id'=>input('id'),
				'pid'=>input('pid'),
				'name'=>input('name'),
				'key'=>input('key'),
				'img_w'=>input('img_w'),
				'img_h'=>input('img_h'),
			);
			$rst=Db::name('product_classify')->update($sl_data);
			if($rst!==false){
				$this->success('修改成功',url('admin/Product/classify_2nd',array('id' =>input('pid'))));
			}else{
				$this->error('修改失败',url('admin/Product/classify_2nd',array('id' =>input('pid'))));
			}
		}
	}
	/*
	 * 删除第二级分类
	 */
	public function classify2nd_del()
	{
		//删除第二级分类
		$rst=Db::name('product_classify')->where(array('id'=>input('id')))->delete();
		//删除第三级分类
		$prst=Db::name('product_classify')->where(array('pid'=>input('id')))->delete();
		if($rst!==false && $prst!==false){
			$this->success('删除成功',url('admin/Product/classify_2nd',array('id' =>input('pid'))));
		}else{
			$this->error('删除失败',url('admin/Product/classify_2nd',array('id' =>input('pid'))));
		}
	}
	/*
	 * 第三级分类列表
	 */
	public function classify_3rd()
	{
		$pagesize=100;
		$pid=input('id');
		$classify=Db::name('product_classify')->where(array('pid'=>input('id')))->order('id asc')->paginate($pagesize);
		//上级ID
		$ppid=Db::name('product_classify')->where(array('id'=>input('id')))->value('pid');
		$this->assign('ppid',$ppid);
		//上级名称
		$ppname=Db::name('product_classify')->where(array('id'=>input('id')))->value('name');
		$this->assign('ppname',$ppname);
		//上上级ID
		$pppid=Db::name('product_classify')->where(array('id'=>$ppid))->value('pid');
		$this->assign('pppid',$pppid);
		//上上级名称
		$pppname=Db::name('product_classify')->where(array('id'=>$ppid))->value('name');
		$this->assign('pppname',$pppname);
		$page = $classify->render();
		$this->assign('pid',$pid);
		$this->assign('classify',$classify);
		$this->assign('page',$page);
		return $this->fetch();
	}
	/*
	 * 添加第三级分类
	 */
	public function classify3rd_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/classify_3rd',array('id' =>input('pid'))));
		}else{
			$data=input('post.');
			Db::name('product_classify')->insert($data);
			$this->success('添加成功',url('admin/Product/classify_3rd',array('id' =>input('pid'))));
		}
	}
	/*
	 * 返回第三级分类值
	 */
	public function classify3rd_edit()
	{
		$id=input('id');
		$classify3rd=Db::name('product_classify')->where(array('id'=>$id))->find();
		$sl_data['id']=$classify3rd['id'];
		$sl_data['pid']=$classify3rd['pid'];
		$sl_data['name']=$classify3rd['name'];
		$sl_data['key']=$classify3rd['key'];
		$sl_data['img_w']=$classify3rd['img_w'];
		$sl_data['img_h']=$classify3rd['img_h'];
		$sl_data['code']=1;
		return json($sl_data);
	}
	/*
	 * 修改第三级分类
	 */
	public function classify3rd_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/classify_3rd',array('id' =>input('pid'))));
		}else{
			$sl_data=array(
				'id'=>input('id'),
				'pid'=>input('pid'),
				'name'=>input('name'),
				'key'=>input('key'),
				'img_w'=>input('img_w'),
				'img_h'=>input('img_h'),
			);
			$rst=Db::name('product_classify')->update($sl_data);
			if($rst!==false){
				$this->success('修改成功',url('admin/Product/classify_3rd',array('id' =>input('pid'))));
			}else{
				$this->error('修改失败',url('admin/Product/classify_3rd',array('id' =>input('pid'))));
			}
		}
	}
	/*
	 * 删除第三级分类
	 */
	public function classify3rd_del()
	{
		$rst=Db::name('product_classify')->where(array('id'=>input('id')))->delete();
		if($rst!==false){
			$this->success('删除成功',url('admin/Product/classify_3rd',array('id' =>input('pid'))));
		}else{
			$this->error('删除失败',url('admin/Product/classify_3rd',array('id' =>input('pid'))));
		}
	}
	/*
	 * 显示品牌分类列表
	 */
	public function product_brand()
	{
		$classify_list=Db::name('product_brand')->order('classify_order,classify_id desc')->paginate(config('paginate.list_rows'));
		$page = $classify_list->render();
		$this->assign('classify_list',$classify_list);
		$this->assign('page',$page);
		return $this->fetch();
	}
	/*
	 * 添加品牌分类
	 */
	public function brand_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/product_brand'));
		}else{
			$data=input('post.');
			Db::name('product_brand')->insert($data);
			$this->success('添加分类成功',url('admin/Product/product_brand'));
		}
	}
	/*
	 * 品牌分类排序
	 */
	public function brand_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/product_brand'));
		}else{
			foreach (input('post.') as $classify_id => $classify_order){
				Db::name('product_brand')->where(array('classify_id' => $classify_id ))->setField('classify_order', $classify_order);
			}
			$this->success('排序更新成功',url('admin/Product/product_brand'));
		}
	}
	/*
	 * 删除品牌分类
	 */
	public function brand_del()
	{
		$p=input('p');
		$rst=Db::name('product_brand')->where(array('classify_id'=>input('classify_id')))->delete();
		if($rst!==false){
			$this->success('删除分类成功',url('admin/Product/product_brand',array('p' => $p)));
		}else{
			$this->error('删除分类失败',url('admin/Product/product_brand',array('p' => $p)));
		}
	}
	/**
	 * 显示编辑品牌页面
	 */
	public function product_brand_edit()
	{
		$classify_id = input('classify_id');
		if (empty($classify_id)){
			$this->error('参数错误',url('admin/Product/product_brand'));
		}
		$classify=Db::name('product_brand')->where(array('classify_id'=>$classify_id))->find();
		//多图路径转换
		$allurl_text = $classify['pic_allurl'];
		$this->assign('pic_list',$allurl_text);
		//多图说明转换
		$content_text = $classify['pic_content'];
		$this->assign('pic_content_list',$content_text);
		$this->assign('classify',$classify);
		return $this->fetch();
	}
	/**
	 * 编辑品牌
	 */
	public function brand_runedit()
	{
//		if (!request()->isAjax()){
//			$this->error('提交方式不正确',url('admin/Product/product_brand'));
//		}
		//上传图片部分
		$img_w='';
		$img_h='';
		$picall_url='';
		$file_w = input('img_w','');
		$file_h = input('img_h','');
		$files = input('pic_all','');
		if($file_w || $file_h || $files) {
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
		$sl_data=array(
			'classify_name'=>input('classify_name'),
			'classify_description'=>input('classify_description',''),
			'img_w'=>$img_w,//封面图片(横屏)路径
			'img_h'=>$img_h,//封面图片(竖屏)路径
			'pic_allurl'=>$picall_url,//多图路径
			'pic_content'=>input('pic_content',''),
			'evaluate_status'=>input('evaluate_status',0)?true:false,
			'like'=>input('like',0),
			'hate'=>input('hate',0),
			'content'=>htmlspecialchars_decode(input('content'))
		);
		$rst=Db::name('product_brand')->where(array('classify_id'=>input('classify_id')))->update($sl_data);
		if($rst!==false){
			$this->success('品牌修改成功,返回列表页',url('admin/Product/product_brand'));
		}else{
			$this->error('品牌修改失败',url('admin/Product/product_brand'));
		}
	}
	/**
	 * 产品列表
	 */
	public function product_list()
	{
		//关键字类型
		$keytype=input('keytype','title');
		//关键字
		$key=input('key','');
		//状态
		$opentype_check=input('opentype_check','');
		//分页大小
		$pagesize=input('pagesize',20);
		//第一级分类
		$classifyy=input('classify','');
		//第二级分类
		$classify2nd=input('classify2nd','');
		//第三级分类
		$classify3rd=input('classify3rd','');
		//查询：时间格式过滤 获取格式 2017-11-12 - 2017-11-18
		$sldate=input('reservation','');
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
		if ($classifyy!=''){
			$map['classify']= $classifyy;
		}
		if ($classify2nd!=''){
			$map['classify2nd']= $classify2nd;
		}
		if ($classify3rd!=''){
			$map['classify3rd']= $classify3rd;
		}
		$product_model=new productModel;
		$product=$product_model->alias("a")->field('a.*,b.*,c.name')
					->join(config('database.prefix').'member b','a.auto =b.member_id')
					->join(config('database.prefix').'product_classify c','a.classify3rd =c.id')
					->where($map)->order('time desc')->paginate($pagesize,false,['query'=>get_query()]);
		$show = $product->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('page',$show);
		//分类数据
		$classify=Db::name('product_classify')->where(array('pid'=>0))->select();
		$this->assign('classify',$classify);
		$this->assign('opentype_check',$opentype_check);
		$this->assign('pagesize',$pagesize);
		$this->assign('keytype',$keytype);
		$this->assign('keyy',$key);
		$this->assign('sldate',$sldate);
		$this->assign('product',$product);
		if(request()->isAjax()){
			return $this->fetch('ajax_product_list');
		}else{
			return $this->fetch();
		}		
	}
	//AJAX配件列表
	public function parts() {
	    $where['classify3rd'] = input("classify3rd","");
	    $where['isparts']= 1;
	    $where['back']= 0;
	    $where['open']= 1;
        $parts=Db::name("product")->where($where)->field('n_id,title,img_w')->select();
		ajaxReturn($parts);
	}

	/**
	 * 显示添加页面
	 */
	public function product_add()
	{
		//品牌数据
		$brand=Db::name('product_brand')->select();
		$this->assign('brand',$brand);
		//分类分级显示
		$classify=Db::name('product_classify')->where(array('pid'=>0))->select();
		$this->assign('classify',$classify);
		//标签数据
		$where['flag_type']= 2;
		$flag=Db::name('plug_flag')->where($where)->select();
		$this->assign('flag',$flag);
		//来源数据
		$source=Db::name('plug_source')->select();
		$this->assign('source',$source);
		return $this->fetch();
	}
	/**
	 * 添加产品
	 */
	public function product_runadd()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/product_add'));
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
		$product_flag=input('post.flag/a');
		$flag=array();
		if(!empty($product_flag)){
			foreach ($product_flag as $v){
				$flag[]=$v;
			}
		}
		$flagdata=implode(',',$flag);
		//获取配件数据
		$product_parts=input('post.parts/a');
		$parts=array();
		if(!empty($product_parts)){
			foreach ($product_parts as $w){
				$parts[]=$w;
			}
		}
		$partsdata=implode(',',$parts);
		$sl_data=array(
			'classify'=>input('classify'),//第一级分类
			'classify2nd'=>input('classify2nd'),//第二级分类
			'classify3rd'=>input('classify3rd'),//第三级分类
			'brandid'=>input('brandid'),//品牌分类
			'title'=>input('title'),//标题
			'subtitle'=>input('subtitle',''),//副标题
			'key'=>input('key',''),//关键词
			'flag'=>$flagdata,//标签数据
			'source'=>input('source',''),//来源
			'img_w'=>$img_w,//封面图片(横屏)路径
			'img'=>$img_h,//封面图片(竖屏)路径
			'scontent'=>input('scontent',''),//摘要
			'intro'=>htmlspecialchars_decode(input('intro')),//产品详情
			'parts'=>$partsdata,//配件数据
			'weight'=>input('weight',''),//重量
			'cpprice'=>input('cpprice',''),//初始显示价
			'mprice'=>input('mprice',''),//市场价
			'pic_allurl'=>$picall_url,//颜色图路径
			'pic_content'=>input('pic_content',''),//颜色说明
			'size'=>input('produce_size',''),//尺寸数据
			'price'=>input('produce_price',''),//价格数据
			'hits'=>input('hits',300),
			'bypay'=>input('bypay',200),
			'comment_status'=>input('comment_status',1),
			'isparts'=>input('isparts',0),
			'isfreeship'=>input('isfreeship',0),
			'freeshipcon'=>input('freeshipcon',2),
			'promotion_type'=>input('promotion_type',0),
			'promotion_free'=>input('promotion_free',1000),
			'promotion_rebate'=>input('promotion_rebate',0.98),
			'auto'=>session('admin_auth.member_id'),
			'time'=>time(),
			'listorder'=>input('listorder',3,'intval'),
		);
		$result=ProductModel::create($sl_data);
		if($result){
			$this->success('添加产品成功',url('admin/Product/product_list',['columnid'=>input('columnid')]));
		}else{
			$this->success('添加失败');
		}
	}
	/**
	 * 产品排序
	 */
	public function product_order()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/product_list'));
		}else{
			$list=[];
			foreach (input('post.') as $n_id => $order){
				$list[]=['n_id'=>$n_id,'listorder'=>$order];
			}
			$product_model=new ProductModel;
			$product_model->saveAll($list);
			$this->success('排序更新成功',url('admin/Product/product_list'));
		}
	}
	/**
	 * 删除至回收站(单个)
	 */
	public function product_del()
	{
		$p=input('p');
		$product_model=new ProductModel;
		$rst=$product_model->where(array('n_id'=>input('n_id')))->setField('back',1);//转入回收站
		if($rst!==false){
			$this->success('产品已转入回收站',url('admin/Product/product_list',array('p' => $p)));
		}else{
			$this -> error("删除产品失败！",url('admin/Product/product_list',array('p'=>$p)));
		}
	}
	/**
	 * 删除至回收站(全选)
	 */
	public function product_alldel()
	{
		$p = input('p');
		$ids = input('n_id/a');
		if(empty($ids)){
			$this -> error("请选择需要删除的产品",url('admin/Product/product_list',array('p'=>$p)));//判断是否选择了产品ID
		}
		if(is_array($ids)){//判断获取产品ID的形式是否数组
			$where = 'n_id in('.implode(',',$ids).')';
		}else{
			$where = 'n_id='.$ids;
		}
		$product_model=new ProductModel;
		$rst=$product_model->where($where)->setField('back',1);//转入回收站
		if($rst!==false){
			$this->success("成功把多个产品移至回收站！",url('admin/Product/product_list',array('p'=>$p)));
		}else{
			$this -> error("删除产品失败！",url('admin/Product/product_list',array('p'=>$p)));
		}
	}
	/**
	 * 产品审核
	 */
	public function product_state()
	{
		$id=input('x');
		$product_model=new ProductModel;
		$status=$product_model->where(array('n_id'=>$id))->value('open');
		if($status==1){
			$statedata = array('open'=>0);
			$product_model->where(array('n_id'=>$id))->setField($statedata);
			$this->success('未审');
		}else{
			$statedata = array('open'=>1);
			$product_model->where(array('n_id'=>$id))->setField($statedata);
			$this->success('已审');
		}
	}
	/**
	 * 回收站列表
	 */
	public function product_back()
	{
		//关键字类型
		$keytype=input('keytype','title');
		//关键字
		$key=input('key','');
		//状态
		$opentype_check=input('opentype_check','');
		//分页大小
		$pagesize=input('pagesize',20);
		//第一级分类
		$classifyy=input('classify','');
		//第二级分类
		$classify2nd=input('classify2nd','');
		//第三级分类
		$classify3rd=input('classify3rd','');
		//查询：时间格式过滤 获取格式 2017-11-12 - 2017-11-18
		$sldate=input('reservation','');
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
		if ($classifyy!=''){
			$map['classify']= $classifyy;
		}
		if ($classify2nd!=''){
			$map['classify2nd']= $classify2nd;
		}
		if ($classify3rd!=''){
			$map['classify3rd']= $classify3rd;
		}
		$product_model=new productModel;
		$product=$product_model->alias("a")->field('a.*,b.*,c.name')
					->join(config('database.prefix').'member b','a.auto =b.member_id')
					->join(config('database.prefix').'product_classify c','a.classify3rd =c.id')
					->where($map)->order('time desc')->paginate($pagesize,false,['query'=>get_query()]);
		$show = $product->render();
		$show=preg_replace("(<a[^>]*page[=|/](\d+).+?>(.+?)<\/a>)","<a href='javascript:ajax_page($1);'>$2</a>",$show);
		$this->assign('page',$show);
		//分类数据
		$classify=Db::name('product_classify')->where(array('pid'=>0))->select();
		$this->assign('classify',$classify);
		$this->assign('opentype_check',$opentype_check);
		$this->assign('pagesize',$pagesize);
		$this->assign('keytype',$keytype);
		$this->assign('keyy',$key);
		$this->assign('sldate',$sldate);
		$this->assign('product',$product);
		if(request()->isAjax()){
			return $this->fetch('ajax_product_back');
		}else{
			return $this->fetch();
		}		
	}
	/**
	 * 还原产品
	 */
	public function product_back_open()
	{
		$p=input('p');
		$product_model=new ProductModel;
		$rst=$product_model->where('n_id',input('n_id'))->setField('back',0);//还原
		if($rst!==false){
			$this->success('还原产品成功',url('admin/Product/product_back',array('p' => $p)));
		}else{
			$this->error("还原产品失败！",url('admin/Product/product_back',array('p' => $p)));
		}
	}
	/**
	 * 彻底删除(单个)
	 */
	public function product_back_del()
	{
		$n_id=input('n_id');
		$p = input('p');
		$product_model=new ProductModel;
		if (empty($n_id)){
			$this->error('参数错误',url('admin/Product/product_back',array('p' => $p)));
		}else{
			$rst=$product_model->where('n_id',input('n_id'))->delete();
			if($rst!==false){
				$this->success('彻底删除产品成功',url('admin/Product/product_back',array('p' => $p)));
			}else{
				$this -> error("彻底删除产品失败！",url('admin/Product/product_back',array('p' => $p)));
			}
		}
	}
	/**
	 * 彻底删除(全选)
	 */
	public function product_back_alldel()
	{
		$p = input('p');
		$ids = input('n_id/a');
		if(empty($ids)){
			$this -> error("请选择需要删除的产品",url('admin/Product/product_back',array('p'=>$p)));//判断是否选择了产品ID
		}
		if(is_array($ids)){//判断获取产品ID的形式是否数组
			$where = 'n_id in('.implode(',',$ids).')';
		}else{
			$where = 'n_id='.$ids;
		}
		$product_model=new ProductModel;
		$rst=$product_model->where($where)->delete();
		if($rst!==false){
			$this->success("成功将多个产品删除，不可还原！",url('admin/Product/product_back',array('p'=>$p)));
		}else{
			$this -> error("删除失败！",url('admin/Product/product_back',array('p' => $p)));
		}
	}
	/**
	 * 显示编辑页面
	 */
	public function product_edit()
	{
		$n_id = input('n_id');
		if (empty($n_id)){
			$this->error('参数错误',url('admin/Product/product_list'));
		}
		$product=ProductModel::get($n_id);
		//多图路径转换
		$allurl_text = $product['pic_allurl'];
		$pic_list = array_filter(explode(",", $allurl_text));
		$this->assign('pic_list',$allurl_text);
		//多图说明转换
		$content_text = $product['pic_content'];
		$pic_content_list = array_filter(explode(",", $content_text));
		$this->assign('pic_content_list',$content_text);
		//尺寸转换
		$allsize_text = $product['size'];
		$this->assign('size_list',$allsize_text);
		//价格转换
		$price_text = $product['price'];
		$this->assign('price_content_list',$price_text);
		//品牌数据
		$brand=Db::name('product_brand')->select();
		$this->assign('brand',$brand);
		//第一级分类数据
		$classify=Db::name('product_classify')->where(array('pid'=>0))->select();
		$this->assign('classify',$classify);
		//第二级分类数据
		$classify2nd=Db::name('product_classify')->where(array('pid'=>1))->select();
		$this->assign('classify2nd',$classify2nd);
		//第三级分类数据
		$classify3rd=Db::name('product_classify')->where(array('pid'=>2))->select();
		$this->assign('classify3rd',$classify3rd);
		//本产品所属第一级分类
		$c_id=$product['classify'];
		$this->assign('c_id',$c_id);
		//本产品所属第二级分类
		$nd_id=$product['classify2nd'];
		$this->assign('nd_id',$nd_id);
		//本产品所属第三级分类
		$rd_id=$product['classify3rd'];
		$this->assign('rd_id',$rd_id);
		//本产品所属品牌
		$brand_id=$product['brandid'];
		$this->assign('brand_id',$brand_id);
		//标签数据
		$where['flag_type']= 2;
		$flag=Db::name('plug_flag')->where($where)->select();
		$this->assign('flag',$flag);
		//配件数据
	    $partsid= $product['parts'];
	    $partswhere = 'n_id in('.$partsid.')';
	    $map['isparts']= 1;
	    $map['back']= 0;
	    $map['open']= 1;
	    if(empty($partsid)){
	    	$parts=Db::name("product")->where($map)->field('n_id,title,img_w')->select();
	    }else{
	    	$parts=Db::name("product")->where($map)->where($partswhere)->field('n_id,title,img_w')->select();
	    }
        
		$this->assign('parts',$parts);
		//来源数据
		$source=Db::name('plug_source')->select();
		$this->assign('source',$source);
		$this->assign('product',$product);
		return $this->fetch();
	}
	/**
	 * 编辑产品
	 */
	public function product_runedit()
	{
		if (!request()->isAjax()){
			$this->error('提交方式不正确',url('admin/Product/product_list'));
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
		$product_flag=input('post.flag/a');
		$flag=array();
		if(!empty($product_flag)){
			foreach ($product_flag as $v){
				$flag[]=$v;
			}
		}
		$flagdata=implode(',',$flag);
		//获取配件数据
		$product_parts=input('post.parts/a');
		$parts=array();
		if(!empty($product_parts)){
			foreach ($product_parts as $w){
				$parts[]=$w;
			}
		}
		$partsdata=implode(',',$parts);
		$sl_data=array(
			'n_id'=>input('n_id'),
			'classify'=>input('classify'),//第一级分类
			'classify2nd'=>input('classify2nd'),//第二级分类
			'classify3rd'=>input('classify3rd'),//第三级分类
			'brandid'=>input('brandid'),//品牌分类
			'title'=>input('title'),//标题
			'subtitle'=>input('subtitle',''),//副标题
			'key'=>input('key',''),//关键词
			'flag'=>$flagdata,//标签数据
			'source'=>input('source',''),//来源
			'img_w'=>$img_w,//封面图片(横屏)路径
			'img'=>$img_h,//封面图片(竖屏)路径
			'scontent'=>input('scontent',''),//摘要
			'intro'=>htmlspecialchars_decode(input('intro')),//产品详情
			'parts'=>$partsdata,//配件数据
			'weight'=>input('weight',''),//重量
			'cpprice'=>input('cpprice',''),//初始显示价
			'mprice'=>input('mprice',''),//市场价
			'pic_allurl'=>$picall_url,//颜色图路径
			'pic_content'=>input('pic_content',''),//颜色说明
			'size'=>input('produce_size',''),//尺寸数据
			'price'=>input('produce_price',''),//价格数据
			'hits'=>input('hits',300),
			'bypay'=>input('bypay',200),
			'comment_status'=>input('comment_status',1),
			'isparts'=>input('isparts',0),
			'isfreeship'=>input('isfreeship',0),
			'freeshipcon'=>input('freeshipcon',2),
			'promotion_type'=>input('promotion_type',0),
			'promotion_free'=>input('promotion_free',1000),
			'promotion_rebate'=>input('promotion_rebate',0.98),
			'auto'=>session('admin_auth.member_id'),
			'modified'=>time(),
		);
		$rst=ProductModel::update($sl_data);
		if($rst!==false){
			$this->success('产品修改成功,返回列表页',url('admin/Product/product_list'));
		}else{
			$this->error('产品修改失败',url('admin/Product/product_list'));
		}
	}

//结束
}