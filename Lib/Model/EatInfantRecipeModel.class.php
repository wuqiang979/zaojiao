<?php
/**
 * 教师食谱表.
 */
class EatInfantRecipeModel extends ArModel {

    // 表名
    public $tableName = 's_eat_baby_recipe';

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
            // 获取食材信息**
            return $res;
        endif;
        return $arr;
    }

    // 获取启用的当前yid下的学年
    public function getSchoolYearSelectByYid($res)
    {
        $arr = EduSchoolYearModel::model()
            ->getDb()
            ->where(array('status'=>1,'yid'=>$res['yid']))
            ->queryRow();
        $res['school_year'] = $arr['first_school_year'].'~'.$arr['last_school_year'];

        return $res;
    }

    // 获取菜式信息
    public function getCuisine($res)
    {
        for ($i=1; $i <= 5 ; $i++):
            $cona = array(
                'cid' => $res['cuisine_a'.$i],
            );
            $conb = array(
                'cid' => $res['cuisine_b'.$i],
            );
            $conc = array(
                'cid' => $res['cuisine_c'.$i],
            );
            $cond = array(
                'cid' => $res['cuisine_d'.$i],
            );
            $res['cuisine_a'.$i] = EatCuisineModel::model()
                ->getDb()
                ->where($cona)
                ->queryRow();
            $res['cuisine_b'.$i] = EatCuisineModel::model()
                ->getDb()
                ->where($conb)
                ->queryRow();
            $res['cuisine_c'.$i] = EatCuisineModel::model()
                ->getDb()
                ->where($conc)
                ->queryRow();
            $res['cuisine_d'.$i] = EatCuisineModel::model()
                ->getDb()
                ->where($cond)
                ->queryRow();
            $res['cuisine_e'.$i] = EatCuisineModel::model()
                ->getDb()
                ->where($cond)
                ->queryRow();
        endfor;

        return $res;
    }
}
