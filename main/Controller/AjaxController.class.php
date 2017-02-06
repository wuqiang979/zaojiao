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
// 家园互通
class AjaxController extends BaseController
{
	public function init()
	{
		parent::init();
		$this->setLayoutFile('');

	}
	// 幼儿成长评价
	public function menustatusAction()
	{
		echo 'true';
	}

	// 添加部门弹框
	public function departmentEditAction()
	{
		if ($post = arPost() && arPost('btnSave') != '') :
			$postData['pid'] = arPost('ddlParent');
			$postData['name'] = arPost('tbDepartmentName'); 
			$postData['remark'] = arPost('tbNotes');
			$postData['pname'] = DepartmentModel::model()->getDb()->where(array('id' => arPost('ddlParent')))->queryColumn('name');
			DepartmentModel::model()->getDb()->insert($postData,true);
			$this->assign(array('success' => 1));
		endif;
		$department = DEpartmentModel::getDepartment();
		$this->assign(array('department' => $department));
		$this->display();

	}

	// 获取返回消息
	public function getMessageAction()
	{
		echo 0;

	}


}
