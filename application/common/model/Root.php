<?php
namespace app\common\model;

use think\Model;

class Root extends Model
{
    public function add($data)
    {
        return $this->save($data);
    }
    public function getRoot()
    {
        $result = $this->select();
        return $result;
    }
    public function countdata()
    {
        $result = $this->count();
        return $result;
    }
    public function root()
    {
        return $this->hasMany('Member','root_id');
    }
}
