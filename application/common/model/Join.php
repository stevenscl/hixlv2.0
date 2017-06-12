<?php
namespace app\common\model;

use think\Model;

class Join extends Model
{
	public function countjoin()
	{
		return $this->where('join_status','=','0')->count();
	}
	public function getjoin()
	{
		return $this->where('join_status','=','0')->select();
	}
	public function countdel()
	{
		return $this->where('join_status','=','-1')->count();
	}
	public function getdel()
	{
		return $this->where('join_status','=','-1')->select();
	}
	public function joincommunitycount($registered_id)
	{
		return $this->where(['registered_id' =>$registered_id,'join_status' => 1,])->count();
	}
	public function joincommunity($registered_id)
	{
		return $this->where(['registered_id' =>$registered_id,'join_status' => 1,])->column('community_id');
	}
	public function applyuserinfo()
    {
    	return $this->belongsTo('Registeredinfo','registered_id');
    }
    public function applycommunityinfo()
	{
		return $this->belongsTo('Community','community_id');
	}
	public function modifycommunity()
	{
		return $this->belongsTo('Member','registered_id');
	}
	public function counteachjoin($community_id)
	{
		return $this->where('join_status','=','0')->where('community_id','=',$community_id)->count();
	}
	public function geteachjoin($community_id)
	{
		return $this->where('join_status','=','0')->where('community_id','=',$community_id)->select();
	}
	public function counteachdel($community_id)
	{
		return $this->where('join_status','=','-1')->where('community_id','=',$community_id)->count();
	}
	public function geteachdel($community_id)
	{
		return $this->where('join_status','=','-1')->where('community_id','=',$community_id)->select();
	}
	public function apply($data)
	{
        $data['join_time']   = time();
		return $this->save($data);
	}

}