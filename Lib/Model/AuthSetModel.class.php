<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 *  数据库模型.
 */
class AuthSetModel extends ArModel
{
    // 集团学校部门表
    public $tableName = 'u_authority_set';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取所有
    public function sortAll($condition = array())
    {
        $sortData = array();
        $menu = $this->getAllMenuBysid(0, true, $condition);
        $titlePadMap = array(
            '│', '├─', '└─',
        );
        if ($menu) :
            $padStringUnit = '&nbsp;&nbsp;&nbsp;&nbsp;';
            // parent 代表当前母体
            $sortData[] = $menu[0];
            if (!is_array($menu[0]['sub_menu'])) :
                return $sortData;
            endif;
            // 精确到两级
            foreach ($menu[0]['sub_menu'] as $sub_menu) :
                $sub_menu['name'] = $padStringUnit . $titlePadMap[1] . $sub_menu['name'];
                $sortData[] = $sub_menu;
                if (is_array($sub_menu['sub_menu'])) :
                    foreach ($sub_menu['sub_menu'] as $second_menu) :
                        $second_menu['name'] = $padStringUnit . $padStringUnit . $titlePadMap[1] . $second_menu['name'];
                        $sortData[] = $second_menu;
                    endforeach;
                endif;
            endforeach;
        endif;
        return $sortData;

    }

    // 获取所有分类 默认获取所有分类
    public function getAllMenuBysid($sid = 0, $getall = false, $condition = array())
    {
        $con['psid'] = $sid;
        $con = array_merge($con, $condition);
        $submenu = self::model()
            ->getDb()
            ->order('sorder desc')
            ->where($con)
            ->queryAll();
        if (!$submenu) :
            return false;
        endif;

        if (!$getall) :
            return $submenu;
        else :
            if (!empty($submenu)) :
                foreach ($submenu as &$sub_menu) :
                    $sub_menu['authdetail'] = $this->getDetailInfo($sub_menu);
                    $sub_menu['sub_menu'] = $this->getAllMenuBysid($sub_menu['sid'], $getall);
                endforeach;
            endif;
        endif;
        return $submenu;

    }

    // 获取产品详细信息
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;
            /**
             * to do
             */
            $bundle['parent_name'] = self::model()
                ->getDb()
                ->where(array('sid' => $bundle['psid']))
                ->queryColumn('name');

            $bundle['authlist'] = AuthListModel::model()
                ->getDb()
                ->where(array('sid' => $bundle['sid']))
                ->queryAll();

            return $bundle;
        endif;

        return $bundles;

    }

}
