<?php
namespace app\admin\controller;

use think\Controller;

class Newss extends Controller
{
    private $obj;
    public function _initialize()
    {
        $this->obj = model('community');
    }
    public function index()
    {

        $newslist = model('news')->getnews();
        $count    = model('news')->countnews();
        for ($x = 0; $x < $count; $x++) {
            $newslist[$x]['community_name'] = empty(model('news')->get(intval($newslist[$x]['news_id']))->news->community_name) ? null : model('news')->get(intval($newslist[$x]['news_id']))->news->community_name;
        }
        return $this->fetch('', [
            'newslist' => $newslist,
            'count'    => $count,
        ]);
    }
    public function add()
    {
        $community = $this->obj->communityinfo();
        return $this->fetch('', [
            'community' => $community,
        ]);
    }
    public function news()
    {
        
        if(!request()->isPost()){
            $this->error('请求错误');
        }
        $data = input('post.');
        $validate = validate('News');
         if(!$validate->check($data)){
                $this->error($validate->getError());
            }
        if(!empty($data['news_id'])){
            return $this->update($data);
        }
    
            model('News')->add($data);
            print_r('上传成功');
    }
 

    //修改状态
    public function status($news_id,$news_status)
    {
        // $data = model('news')->get($news_id);
        /*  $validate= validate('News');
        if(!$validate->scene('news_status')->check($data)){
        $this->error($validate->getError());
        }*/
        $res = model('news')->save(['news_status' => $news_status], ['news_id' => $news_id]);
        if ($res) {
            $this->success('状态跟新成功');
        } else {
            $this->error('状态跟新失败');
        }
    }

    public function newsdelete()
    {

        $communitylist = model('community')->communityinfo();
        $newslist      = model('news')->getnewsdelete();
        $count         = model('news')->countnewsdelete();
        for ($x = 0; $x < $count; $x++) {
            $newslist[$x]['community_name'] = empty(model('news')->get(intval($newslist[$x]['news_id']))->news->community_name) ? null : model('news')->get(intval($newslist[$x]['news_id']))->news->community_name;
        }
        return $this->fetch('', [
            'newslist' => $newslist,
            'count'    => $count,
        ]);
    }
      public function detail($news_id=0)
    {
        
        if (intval($news_id)<1) {
            return $this->error('ID错误');
            
        }
        $newslist = model('news')->get($news_id);
        $community = $this->obj->communityinfo();
      
        return $this->fetch('', [
            'newslist' => $newslist,
            'community'=>$community,
        ]);
    }
    public function update($data){
        {
            $this->obj=model('news');
        }
        $res = $this->obj->save($data,['news_id'=>intval($data['news_id'])]);
        if($res) {
            $this->success('跟新成功');
        } else{
            $this->error('跟新失败');
        }
    }
    

}
