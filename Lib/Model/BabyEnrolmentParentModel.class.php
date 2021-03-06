<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 *  招生数据库模型.
 */
class BabyEnrolmentParentModel extends ArModel
{
    // 招生表
    public $tableName = 'u_baby_enrolment_parent';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 类型
    static $SEX_MAP = array(
         0 => '男',
         1 => '女',
         2 => '保密',
    );

    // 获取所有分类 默认获取所有分类
    public function getAllMenuByPid($pid = 0, $getall = false)
    {
        $con['ppid'] = $pid;
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
                    $sub_menu['sub_menu'] = $this->getAllMenuByPid($sub_menu['pid'], $getall);
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
            return $bundle;
        endif;

        return $bundles;

    }

}
