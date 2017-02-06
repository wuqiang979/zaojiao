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
class BabyEnrolmentProfileModel extends ArModel
{
    // 招生表
    public $tableName = 'u_baby_enrolment_profile';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 状态
    static $STATUS_MAP = array(
         0 => '审核中',
         1 => '通过',
         2 => '禁止',
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
              // 学年信息
            $schoolYear = EduSchoolYearModel::model()
                ->getDb()
                ->where(array('yid' => $bundle['school_year_id']))
                ->queryRow();
            $schoolYear = EduSchoolYearModel::model()->getDetailInfo($schoolYear);
            $bundle['school_year'] = $schoolYear;

            // 教室信息
            $classRoom = EduClassroomModel::model()
                ->getDb()
                ->where(array('cid' => $bundle['class_room_id']))
                ->queryRow();
            $classRoom = EduClassroomModel::model()->getDetailInfo($classRoom);
            $bundle['class_room'] = $classRoom;

            return $bundle;
        endif;

        return $bundles;

    }

}
