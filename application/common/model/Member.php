<?php
namespace app\common\model;

use think\Model;

class Member extends Model
{
    protected $resultSetType = 'collection';
    public function countdata()
    {
        $result = $this->where('member_status','>=','0')->count();
        return $result;
    }
    public function countdeldata()
    {
        $result = $this->where('member_status','=','-1')->count();
        return $result;
    }
    public function countadmin()
    {
        $result = $this->where('root_id','>','1')->count();
        return $result;
    }
    public function getAdmin()
    {
        $result = $this->where('root_id','>','1')->select();
        return $result;
    }
    public function getMember()
    {
        $result = $this->where('member_status','>=','0')->select();
        return $result;
    }
    public function getMemberdel()
    {
        $result = $this->where('member_status','=','-1')->select();
        return $result;
    }
    public function registeredinfo()
    {
        return $this->belongsTo('Registeredinfo','registered_id');
    }
    public function rootinfo()
    {
        return $this->belongsTo('Root','root_id');
    }
    public function communityinfo()
    {
        return $this->belongsTo('Community','community_id');
    }
    public function apply()
    {
        return $this->hasOne('Join', 'registered_id');
    }
}
