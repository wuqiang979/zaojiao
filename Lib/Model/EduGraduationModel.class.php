<?php
/**
 * 幼儿毕业表.
 */
class EduGraduationModel extends ArModel {

    // 表名
    public $tableName = 's_edu_graduation';

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
            $bundle['member'] = CompanySchoolMemberModel::model()
                ->getDb()
                ->select('realname')
                ->where(array('mid' => $bundle['mid']))
                ->queryRow();

            // 获取毕业前班级基本信息
            $bundle['class_before'] = EduClassroomModel::model()
                ->getDb()
                ->where(array('cid' => $bundle['graduation_class_cid']))
                ->queryRow();
            $bundle['class_before']['school_year'] = EduClassroomModel::model()
                ->getDetailInfo($bundle['class_before']);
                
            return $bundle;
        endif;

        return $bundles;

    }
}
