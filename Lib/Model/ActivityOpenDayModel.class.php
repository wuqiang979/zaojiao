<?php
/**
 * 开放日管理表.
 */
class ActivityOpenDayModel extends ArModel {

    // 表名
    public $tableName = 's_activity_open_day';

    // 初始化
    static public function model($class = __CLASS__) {

        return parent::model($class);
    }  

    CONST STATUS_APPROVED = 1;
    CONST STATUS_STOPED   = 2;
    CONST STATUS_ENDING   = 3;
    
    CONST TYPE_OPEN_IN    = 1;
    CONST TYPE_OPEN_OUT   = 2;

    // 状态
    static $STATUS_ACTIVITY_OPEN = array(
         1 => '启用',
         2 => '停用',
         3 => '已结束',
    ); 
    static $TYPE_ACTIVITY_OPEN = array(
         1 => '园内',
         2 => '园外',
    ); 

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
            // 获取负责人信息
            $res['leader'] = CompanySchoolMemberModel::model()
                ->getDb()
                ->where(array('mid'=>$res['mid_leader']))
                ->queryRow();
            // 获取创建人信息
            $res['creater'] = CompanySchoolMemberModel::model()
                ->getDb()
                ->where(array('mid'=>$res['mid_creater']))
                ->queryRow();
            // 获取班级信息
            $res['class'] = EduClassroomModel::model()
                ->getDb()
                ->where(array('cid'=>$res['cid']))
                ->queryRow();
            $res['class']['school_year'] = EduClassroomModel::model()
                ->getDetailInfo($res['class']);

            return $res;
        endif;
        return $arr;
    }
}
