<?php
/**
 * 幼儿考勤管理表.
 */
class BabyAttendanceModel extends ArModel {

    // 表名
    public $tableName = 'u_baby_attendance';
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
            /**
             * to do 追加数组数据
             */
            // 获取幼儿基本信息
            $res['baby'] = BabyModel::model()
                ->getDb()
                ->where(array('bid'=>$res['bid']))
                ->queryRow();

            // 获取班级信息
            $res['class'] = EduClassroomModel::model()
                ->getDb()
                ->where(array('cid'=>$res['cid']))
                ->queryRow();
            $res['class'] = EduClassroomModel::model()->getDetailInfo($res['class']);
            
            return $res;
        endif;

        return $arr;
    }
}
