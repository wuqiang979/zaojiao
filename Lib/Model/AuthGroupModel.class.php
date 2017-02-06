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
class AuthGroupModel extends ArModel
{
    // 权限分组表
    public $tableName = 'u_authority_group';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 获取bundle详细信息 万能方法
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;

            $bundle['authlist'] = AuthListModel::model()
                ->getDb()
                ->where(array('sid' => $bundle['sid']))
                ->queryAll();

            return $bundle;
        endif;

        return $bundles;

    }

}
