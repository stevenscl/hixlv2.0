<?php
namespace app\common\model;

use think\Model;

class Registeredinfo extends Model
{
    public function add($data)
    {
        $data['registered_status'] = 1;
        $data['create_time']=time();
        return $this->save($data);
    }
     public function registered($data)
    {
        // $data['registered_status'] = 0;
        $data['create_time']=time();
        return $this->save($data);
    }
    public function getRegistered()
    {
        $result = $this->where('registered_status', 1)->select();
        return $result;
    }
    public function countdata()
    {
        $result = $this->where('registered_status', 1)->count();
        return $result;
    }
    public function getRegister()
    {
        $result = $this->where('registered_status', 0)->select();
        return $result;
    }
    public function countRegister()
    {
        $result = $this->where('registered_status', 0)->count();
        return $result;
    }
    public function member()
    {
        return $this->hasOne('Member', 'registered_id');
    }
    public function createCommunity()
    {
        return $this->hasMany('Community','registered_id');
    }
    public function deputy()
    {
        return $this->hasOne('Communityinfo', 'community_deputyid');
    }
    public function apply()
    {
        return $this->hasMany('Join','registered_id');
    }
}
