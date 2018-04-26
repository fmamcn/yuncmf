<?php
// +----------------------------------------------------------------------
// | oneMall [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.rainfer.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 翟翟 <979113800@qq.com>
// +----------------------------------------------------------------------
namespace app\home\controller;

use think\Db;

class Article extends Base
{
    //文章内页
    public function index()
    {
		$page=input('page',1);
		$article=Db::name('article')->alias("a")->join(config('database.prefix').'member b','a.auto =b.member_id')
								->where(array('n_id'=>input('id'),'open'=>1,'back'=>0))->find();
		if(empty($article)){
		    $this->error('无效操作');
		}
		//当有Ueditor分页标识_ueditor_page_break_tag_时进行分页
		$article_data=explode('_ueditor_page_break_tag_',$article['content']);
		$total=count($article_data);
		$article['content']=$article_data[$page-1];
		$article['page']='';
		if($total>1){
			$prevbtn=($page<=1)?'<li class="disabled"><span>&laquo;</span></li>':'<li><a href="' . url('home/Article/index',['id'=>input('id'),'page'=>($page-1)]) . '">&laquo;</a></li>';
			$nextbtn=($page>=$total)?'<li class="disabled"><span>&raquo;</span></li>':'<li><a href="' . url('home/Article/index',['id'=>input('id'),'page'=>($page+1)]) . '">&raquo;</a></li>';
			$link=$this->getLinks($page,$total,input('id'));
			$article['page']=sprintf(
				'<ul class="pagination">%s %s %s</ul>',
				$prevbtn,
				$link,
				$nextbtn
			);
		}
		$menu=Db::name('menu')->find($article['columnid']);
		if(empty($menu)){
		    $this->error('无效操作');
		}
		$tplname=$menu['menu_newstpl'];
    	$tplname=$tplname?$tplname:'news_view';
		//自行根据网站需要考虑，是否需要判断
		$can_do=check_user_action('article'.input('id'),0,false,60);
		if($can_do){
			//更新点击数
			Db::name('article')->where('n_id',input('id'))->setInc('hits',1);
			$article['hits']+=1;
		}
		$next=Db::name('article')->where(array("modified"=>array("egt",$article['modified']), "n_id"=>array('neq',input('id')),"open"=>1,'back'=>0,'columnid'=>$article['columnid']))->order("modified asc")->find();
		$prev=Db::name('article')->where(array("modified"=>array("elt",$article['modified']), "n_id"=>array('neq',input('id')),"open"=>1,'back'=>0,'columnid'=>$article['columnid']))->order("modified desc")->find();
		$t_open=config('comment.t_open');
        if($t_open){
            //获取评论数据
            $comments=Db::name('comments')->alias("a")->join(config('database.prefix').'member b','a.uid =b.member_id')->where(array("a.t_name"=>'article',"a.t_id"=>input('id'),"a.c_status"=>1))->order("a.createtime ASC")->select();
            $count=count($comments);
            $new_comments=array();
            $parent_comments=array();
            if(!empty($comments)){
                foreach ($comments as $m){
                    if($m['parentid']==0){
                        $new_comments[$m['c_id']]=$m;
                    }else{
                        $path=explode("-", $m['path']);
                        $new_comments[$path[1]]['children'][]=$m;
                    }
                    $parent_comments[$m['c_id']]=$m;
                }
            }
            $this->assign("count",$count);
            $this->assign("comments",$new_comments);
            $this->assign("parent_comments",$parent_comments);
        }
    	$linktype=Db::name("plug_linktype")->order("plug_linktype_order asc")->select();
		$this->assign('linktype',$linktype);
        $this->assign("t_open",$t_open);
		$this->assign($article);
		$this->assign('menu',$menu);
		$this->assign("next",$next);
    	$this->assign("prev",$prev);
		return $this->view->fetch(":$tplname");
    }
	//分页链接
	protected function getLinks($page,$total,$id)
	{
		$block = [
			'first'  => null,
			'slider' => null,
			'last'   => null
		];

		$side   = 3;
		$window = $side * 2;

		if ($total < $window + 6) {
			$block['first'] = $this->getUrlRange(1, $total,$id);
		} elseif ($page <= $window) {
			$block['first'] = $this->getUrlRange(1, $window + 2,$id);
			$block['last']  = $this->getUrlRange($total - 1, $total,$id);
		} elseif ($page > ($total - $window)) {
			$block['first'] = $this->getUrlRange(1, 2,$id);
			$block['last']  = $this->getUrlRange($total - ($window + 2), $total,$id);
		} else {
			$block['first']  = $this->getUrlRange(1, 2,$id);
			$block['slider'] = $this->getUrlRange($page - $side, $page + $side,$id);
			$block['last']   = $this->getUrlRange($total - 1, $total,$id);
		}
		$html = '';
		if (is_array($block['first'])) {
			$html .= $this->getUrlLinks($block['first'],$page);
		}
		if (is_array($block['slider'])) {
			$html .= '<li class="disabled"><span>...</span></li>';
			$html .= $this->getUrlLinks($block['slider'],$page);
		}
		if (is_array($block['last'])) {
			$html .= '<li class="disabled"><span>...</span></li>';
			$html .= $this->getUrlLinks($block['last'],$page);
		}
		return $html;
	}
	protected function getUrlLinks(array $urls,$page)
	{
		$html = '';
		foreach ($urls as $text => $url) {
			$html .=($text == $page)?'<li class="active"><span>' . $text . '</span></li>':'<li><a href="' . htmlentities($url) . '">' . $text . '</a></li>';
		}
		return $html;
	}
	protected function getUrlRange($start, $end,$id)
	{
		$urls = [];
		for ($page = $start; $page <= $end; $page++) {
			$urls[$page] = url('home/Article/index',['id'=>$id,'page'=>$page]);
		}
		return $urls;
	}
	//点赞
    public function dolike()
    {
	    $this->check_login();
    	$id=input('id',0,'intval');
    	$can_like=check_user_action('article'.$id,1);
    	if($can_like){
			Db::name("article")->where('n_id',$id)->setInc('like');;
    		$this->success('点赞成功');
    	}else{
    		$this->error('点赞失败');
    	}
    }
    //收藏
    public function dofavorite()
    {
        $this->check_login();
		$key=input('key');
		if($key){
			$id=input('id');
			if($key==encrypt_password('article-'.$id,'article')){
				$uid=session('hid');
				$favorites_model=Db::name("favorites");
				$find_favorite=$favorites_model->where(array('t_name'=>'article','t_id'=>$id,'uid'=>$uid))->find();
				if($find_favorite){
					$this->error('已收藏');
				}else {
                    $data=array(
                        'uid'=>$uid,
                        't_name'=>'article',
                        't_id'=>$id,
                        'createtime'=>time(),
                    );
					$result=$favorites_model->insert($data);
					if($result){
						$this->success('成功收藏');
					}else {
						$this->error('收藏失败');
					}
				}
			}else{
				$this->error('收藏失败');
			}
		}else{
			$this->error('收藏失败');
		}
	}
}
