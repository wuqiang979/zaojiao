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
//连锁管理
class ChainManagementController extends BaseController
{
    //公司管理
    public function companyManagementAction()
    {
        $this->display('companyManagement');
    }

    public function zoneManagementAction()
    {
        $this->display('zoneManagement');
    }

    public function materialManagementAction()
    {
        $this->display('materialManagement');
    }

     public function stockManagementAction()
    {
        $this->display('stockManagement');
    }


}
