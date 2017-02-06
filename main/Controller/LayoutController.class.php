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
// 最外层布局
class LayoutController extends BaseController
{
	// 最外层框架
	public function indexAction()
	{	
		$this->setLayoutFile('layout');
		$this->display();
       
	}

}
