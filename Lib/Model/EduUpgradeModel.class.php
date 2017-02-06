<?php
/**
 * 幼儿升级表.
 */
class EduUpgradeModel extends ArModel {

    // 表名
    public $tableName = 's_edu_upgrade';

    // 初始化
    static public function model($class = __CLASS__) {

        return parent::model($class);
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
            // 获取幼儿基本信息
            $bundle['baby'] = BabyModel::model()
                ->getDb()
                ->where(array('bid' => $bundle['bid']))
                ->queryAll();

            // 获取转班前班级基本信息
            $bundle['class_before'] = EduClassroomModel::model()
                ->getDb()
                ->where(array('cid' => $bundle['u_cid_before']))
                ->queryAll();

            // 获取转班后班级基本信息
            $bundle['class_later'] = EduClassroomModel::model()
                ->getDb()
                ->where(array('cid' => $bundle['u_cid_later']))
                ->queryAll();

            return $bundle;
        endif;

        return $bundles;

    }
}
