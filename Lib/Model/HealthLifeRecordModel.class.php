<?php
/**
 * 日常生活情况记录表.
 */
class HealthLifeRecordModel extends ArModel {

    // 表名
    public $tableName = 's_health_life_record';

    static public function model($class = __CLASS__) {

        return parent::model($class);

    }

    // 状态分数
    static $LIFE_RECORD_SCORE = array(
         1 => '1',
         2 => '2',
         3 => '3',
         4 => '4',
         5 => '5',
         6 => '6',
         7 => '7',
         8 => '8',
         9 => '9',
         10 => '10',
    );   

     // 获取关联表信息（u_baby）
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
            $res['class'] = EduClassroomModel::model()->getDb()
                ->where(array('cid' => $res['cid']))
                ->queryRow();
            $res['baby'] = BabyModel::model()->getDb()
                ->where(array('bid' => $res['bid']))
                ->queryRow();
            $res['member'] = CompanySchoolMemberModel::model()->getDb()
                ->where(array('mid' => $res['mid']))
                ->queryRow();
            return $res;
        endif;

        return $arr;
    }

}
