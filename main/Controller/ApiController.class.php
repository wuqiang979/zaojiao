<?php
class ApiController extends BaseController
{
    public function init()
    {
        parent::init();
        $this->setLayoutFile('');

    }

    /*
    	funnction
		$model  模型
		$id     字段id
		$action 操作类型
    */
	public function doPostBackAction($parameter = '')
	{
		$parameter = arPost('parameter')?arPost('parameter'):$parameter;
		if ($parameter != '') :
			$parameter = explode('$', $parameter);
			list($model,$id,$action) = $parameter;
			switch ($action) {
				case 'linkDel'||'ParamDel':
					$model::model()->getDb()->where(array('id' => $id))->delete();
					break;

				default:
					# code...
					break;
			}
		endif;

	}

	/*
		添加参数入口
		参数一：家庭收入
		参数二：跟进方式
		参数三: 家长关系

	*/

	public function addParametSetAction()
	{
		if ($post = arPost() && arPost('btnParamAdd') != '') :
			list($postDate['name'],$postDate['description'],$postDate['type']) = array(arPost('tbParamName'),arPost('tbParamValue'),arPost('type'));
			ParametSetModel::model()->getDb()->insert($postDate);
			$this->redirect(arPost('refer'));
		endif;
	}

  	// 获取地区数据
    public function getAllregionByPidAction()
    {
        if ($data = arRequest()) :
            $pid = arRequest('pid', 0);
            // 是否查询所有子类
            $sub = arRequest('sub', false);
            // 数据太大 不查所有
            $sub = false;
            $region = RegionModel::model()->getAllreginByPid($pid, $sub);
            $this->showJson($region);
        else :
            $this->showJsonError();
        endif;

    }

    // 获取地区数据
    public function getAllregionBySidAction()
    {
        if ($data = arRequest()) :
            $sid = arRequest('sid', 0);
            $region = RegionModel::model()->getAllreginBySid($sid);
            $this->showJson($region);
        else :
            $this->showJsonError();
        endif;

    }



}
