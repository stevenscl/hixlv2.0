<?php
namespace app\common\model;

use think\Model;

class Communityinfo extends Model
{
	public function info()
	{
		return $this->belongsTo('Community','community_id');
	}
	public function deputyname()
	{
		return $this->belongsTo('Registeredinfo','community_deputyid');
	}
	public function getcommunityinfo(){
     $data = [
      
     ];
   return $this ->where($data)
     ->select();
    }
    public function community()
    {
    	return $this->hasMany('Savenews','community_id');
    }
   
}