<?php
/**
 * 活动表.
 */
class ActivityModel extends ArModel {

    // 表名
    public $tableName = 's_activity';

    // 初始化
    static public function model($class = __CLASS__) {

        return parent::model($class);
    }  

    CONST STATUS_APPROVED = 1;
    CONST STATUS_STOPED = 2;
    CONST STATUS_ENDING = 3;
    // 状态
    static $STATUS_ACTIVITY = array(
         1 => '启用',
         2 => '停用',
         3 => '已结束',
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

            return $res;
        endif;
        return $arr;
    }
}
