<?php
/**
 * 班级教师表.
 */
class EduClassTeacherModel extends ArModel {

    // 表名
    public $tableName = 's_edu_class_teacher';
    // 初始化
    static public function model($class = __CLASS__) {

        return parent::model($class);
    }   

    // 获取关联表信息
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;
            /**
             * to do
             */
            $bundle['teacher'] = U_company_school_member_recordModel::model()
                ->getDetailInfo($bundle);
            $bundle['teacher'] = $bundle['teacher']['member']['realname'];
            return $bundle;
        endif;

        return $bundles;
    }
}
