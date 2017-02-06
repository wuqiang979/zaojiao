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
class ClassPicModel extends ArModel
{
    // 招生表
    public $tableName = 's_edu_class_pic';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 类型
    static $TYPE_MAP = array(
        1 => '学生图片',
        2 => '班级活动',
        3 => '学校活动',
        4 => '其他',
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

            // 获取学校信息
            $school = CompanySchoolModel::model()
                ->getDb()
                ->where(array('sid' => $bundle['sid']))
                ->queryRow();
            $bundle['s_name'] = $school['name'];
            // 幼儿名字
            $profile = BabyModel::model()
                ->getDb()
                ->where(array('bid' => $bundle['bid']))
                ->queryRow();
            $bundle['b_name'] = $profile['name'];

//            // 添加人
//            $bundle['add_user'] = CompanySchoolMemberModel::model()->getDb()
//                ->where(array('mid' => $bundle['amid']))
//                ->queryRow();

            return $bundle;
        endif;

        return $bundles;

    }

}
