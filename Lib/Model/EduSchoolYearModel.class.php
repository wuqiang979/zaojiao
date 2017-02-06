<?php
/**
 * 体检表.
 */
class EduSchoolYearModel extends ArModel {

    // 表名
    public $tableName = 's_edu_school_year';
    // 初始化
    static public function model($class = __CLASS__) {

        return parent::model($class);
    }

    CONST STATUS_STOPED = 0;
    CONST STATUS_APPROVED = 1;
    // 状态
    static $STATUS_SCHOOL_YEAR = array(
         1 => '启用',
         0 => '停运',
    );
    // 获取学年select列表信息
    public function getSelect($condition)
    {
        $arr = EduSchoolYearModel::model()
            ->getDb()
            ->select('yid,first_school_year,last_school_year')
            ->where($condition)
            ->order('yid desc')
            ->queryAll();
        foreach ($arr as $k => $v):
            $res['yid'] = $v['yid'];
            $res['school_year'] = $v['first_school_year']."~".$v['last_school_year'];
            $result[] = $res;
        endforeach;
        return $result;
    }

    // 获取详细信息
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
            $bundle['school_year'] = $bundle['first_school_year'] . "~" . $bundle['last_school_year'];
            return $bundle;
        endif;

        return $bundles;

    }

    // 获取启用的当前SID下的学年
    public function getSchoolYearSelect($sid)
    {
        $res = self::model()
            ->getDb()
            ->where(array('status'=>1,'sid'=>$sid))
            ->order('yid desc')
            ->queryAll();

        foreach($res as $k=>$v):
            $arr[$k]['yid'] = $v['yid'];
            $arr[$k]['school_year'] = $v['first_school_year'].'~'.$v['last_school_year'];
        endforeach;

        return $arr;
    }

    // 获取启用的当前yid下的学年
    public function getSchoolYearSelectByYid($yid)
    {
        $res = EduSchoolYearModel::model()
            ->getDb()
            ->where(array('status'=>1,'yid'=>$yid))
            ->queryAll();

        foreach($res as $k=>$v):
            $arr[$k]['yid'] = $v['yid'];
            $arr[$k]['school_year'] = $v['first_school_year'].'~'.$v['last_school_year'];
        endforeach;

        return $arr;
    }

}
