<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ck <yushaohunzhu@sina.com>
 */

/**
 * Default Controller of webapp.
 */
// 我的桌面
class IndexController extends BaseController
{
	// 我的桌面
	public function indexAction()
	{	
		$this->display('index');

	}

	public function bodyAction()
	{
		$this->setLayoutFile();
		$this->display();

	}

	// 收费管理
	public function sfdjAction()
	{
		$this->display('sfdj');
	}

	// 活动管理
	public function hdglAction()
	{
		$this->display('hdgl');
	}

	// 活动签到
	public function hdqdAction()
	{
		$this->display('hdqd');
	}

	// 活动签到记录
	public function hdqdjlAction()
	{
		$this->display('hdqdjl');
	}

	// 退费管理
	public function tfdjAction()
	{
		$this->display('tfdj');
	}


	// 开放日管理
	public function kfrglAction()
	{
		$this->display('kfrgl');
	}

}

