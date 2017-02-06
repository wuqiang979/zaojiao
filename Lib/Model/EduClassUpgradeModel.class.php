<?php
/**
 * 班级升级表.
 */
class EduClassUpgradeModel extends ArModel {

    // 表名
    public $tableName = 's_edu_class_upgrade';

    // 初始化
    static public function model($class = __CLASS__) {

        return parent::model($class);
    }   

    // 获取关联表信息
    public function getDetailInfo(array $arr)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($arr)) :// 检测是否对维数组
            foreach ($arr as &$res) :
                $res = $this->getDetailInfo($res);
            endforeach;
        else :
            $res = $arr;
            
            // 获取升级前班级信息
            $res['class_before'] = EduClassroomModel::model()
                ->getDb()
                ->where(array('cid'=>$res['cid_before']))
                ->queryRow();
            $res['class_before']['school_year'] = EduClassroomModel::model()
                ->getDetailInfo($res['class_before']);

            // 获取升级后班级信息
            $res['class_later'] = EduClassroomModel::model()
                ->getDb()
                ->where(array('cid'=>$res['cid_later']))
                ->queryRow();
            $res['class_later']['school_year'] = EduClassroomModel::model()
                ->getDetailInfo($res['class_later']);

            // 获取登记人信息
            $res['member'] = CompanySchoolMemberModel::model()
                ->getDb()
                ->select('realname')
                ->where(array('mid'=>$res['mid']))
                ->queryRow();

            return $res;
        endif;
        
        return $arr;
       
    }
}
