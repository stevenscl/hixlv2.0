<?php
namespace app\common\model;

use think\Model;

class News extends Model
{
    protected $resultSetType = 'collection';

    public function add($data)
    {
        $data['news_status'] = 0;
        $data['news_time']   = time();
        return $this->save($data);

    }
    public function countnews()
    {
        return $this->where('news_status', '>', '-1')->count();
    }
    public function getnews()
    {
        return $this->where('news_status', '>', '-1')->select();
    }
    public function getpublishnews()
    {
        return $this->where('news_status','=','1')->order('news_listorder','desc')->count();
    }
    public function news()
    {
        return $this->belongsTo('Community', 'community_id');
    }
    public function publishnews()
    {
        return $this->where('news_status','=','1')->order('news_listorder','desc')->select();
    }
    public function getbananer()
    {
        return $this->where('news_status','=','1')->limit(5)->order('news_listorder','desc')->column('news_image','news_id');
    }

  public function countnewsdelete()
    {
        return $this->where('news_status', '=', '-1')->count();
    }
    public function getnewsdelete()
    {
        return $this->where('news_status', '=', '-1')->select();
    }
}
