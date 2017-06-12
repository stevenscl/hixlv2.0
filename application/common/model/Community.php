<?php
namespace app\common\model;

use think\Model;

class Community extends Model
{
	protected $resultSetType = 'collection';
	public function countdata()
	{
		$result = $this->where('community_status','=','0')->count();
		return $result;
	}
	public function count()
	{
		$result = $this->where('community_status','=','1')->count();
		return $result;
	}
	public function countdel()
	{
		$result = $this->where('community_status','=','-1')->count();
		return $result;
	}
	public function getCommunity()
	{
		$result = $this->where('community_status','=','0')->select();
		return $result;
	}
	public function communityinfo()
	{
		$result = $this->where('community_status','=','1')->select();
		return $result;
	}
	public function delcommunityinfo()
	{
		$result = $this->where('community_status','=','-1')->select();
		return $result;
	}
	public function add($data)
    {
    	$data['community_status'] = 1;
        $data['create_time']=time();
        return $this->save($data);
    }
    public function applycommunity($data)
    {
        $data['create_time']=time();
        return $this->save($data);
    }
    public function communityCreater()
    {
    	return $this->belongsTo('Registeredinfo','registered_id');
    }
    public function info()
    {
    	return $this->hasOne('Communityinfo','community_id');
    }
    public function apply()
    {
    	return $this->hasMany('Join','community_id');
    }
    public function community() //这里要改
    {
    	return $this->hasMany('News','community_id');
    }
    public function activitys() //这里要改
    {
    	return $this->hasMany('Activity','community_id');
    }
}