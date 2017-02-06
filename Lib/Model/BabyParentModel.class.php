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
class BabyParentModel extends ArModel
{
    // 招生表
    public $tableName = 'u_baby_parent';

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
