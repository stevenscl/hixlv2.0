<?php
namespace app\common\model;

use think\Model;

class Joinactivity extends Model
{
	public function join($data)
	{
		$data['joinactivity_status'] = 1;
		return $this->save($data);
	}
	public function count()
    {
        $result = $this->where('joinactivity_status','=','1')->count();
        return $result;
    }
    public function getlist()
    {
        $result = $this->where('joinactivity_status','=','1')->select();
        return $result;
    }
        public function eachcount($activity_id)
    {
        $result = $this->where('joinactivity_status','=','1')->where('activity_id','in',$activity_id)->count();
        return $result;
    }
    public function eachgetlist($activity_id)
    {
        $result = $this->where('joinactivity_status','=','1')->where('activity_id','in',$activity_id)->select();
        return $result;
    }
}