<?php
namespace app\admin\controller;

use think\Controller;

class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }
    public function index2()
    {
        return $this->fetch();
    }
    public function welcome()
    {
    	// echo 'scl';
    	// return \Email::send('934547890@qq.com','小联','hello 小联');
    }
    public function testemail(){
      return \Email::send();
   }
}
