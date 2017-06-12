<?php
namespace app\index\controller;
use think\Controller;
class Base extends Controller
{
	public $account;
	public $member;
	public function _initialize(){
			$this->assign('user',$this->getLoginUserinfo());
			$this->assign('useraccount',$this->getLoginUser());	
	}
	public function getLoginUser(){
		if(!$this->account) {
			$this->account = session('Account','','index');
		}
		return $this->account;
	}
	public function getLoginUserinfo(){
		if(!$this->member) {
			$this->member = session('Member','','index');
		}
		return $this->member;
	}
}