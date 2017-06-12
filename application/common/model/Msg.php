<?php
namespace app\common\model;

use think\Model;

class Msg extends Model
{
	public function sendmsg($data)
	{
        $data['create_time']   = time();
		return $this->save($data);
	}
}