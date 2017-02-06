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

// 连锁管理
class LinkSetsMaController extends BaseController
{
    // 公司管理
    public function FilmManagementAction()
    {
        $this->display('FilmManagement');
    }



    // 园区管理
    public function ZoneMaAction()
    {
        $this->display('ZoneMa');
    }

    // 园区申请
    public function ApplyZoneAction()
    {
        $this->display('ApplyZone');
    }

    // 物料管理
    public function materialMaAction()
    {
        $this->display('materialMa');
    }

    // 库存管理
    public function stockMaAction()
    {
        $this->display('stockMa');
    }




    // // 幼儿设置 家庭收入
    // public function familyIncomeAction()
    // {
    //     $this->display('@InfantSet/familyIncome');
    // }

}
