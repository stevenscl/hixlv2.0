<?php
namespace app\common\model;

use think\Model;

class Activity extends Model
{
    protected $resultSetType = 'collection';

    public function add($data)
    {
        $data['activity_status'] = 0;
        $data['create_time']   = time();
       
        return $this->save($data);

    }
    public function countactivity()
    {
        return $this->where('activity_status', '>', '-1')->count();
    }
    public function getactivity()
    {
        
        return $this->where('activity_status', '>', '-1')->select();
    }
    public function activity()
    {
        return $this->belongsTo('Community', 'community_id');
    }
    public function getstandbyactivity()
   {
        return $this->where('activity_status','=','0')->order('activity_starttime','desc')->select();
   }
   public function standbyactivity()
   {
        return $this->where('activity_status','=','0')->count();
   }

   public function getpublishactivity()
   {
        return $this->where('activity_status','=','1')->order('activity_listorder','desc')->select();
   }
   public function publishactivity()
   {
        return $this->where('activity_status','=','1')->count();
   }

  public function countactivitydelete()
    {
        return $this->where('activity_status', '=', '-1')->count();
    }
    public function getactivitydelete()
    {
        return $this->where('activity_status', '=', '-1')->select();
    }
     public function communityinfo()
    {
        return $this->belongsTo('Community','community_id');
    }

}
