<?php
/**
 * 体检表.
 */
class EduClassroomModel extends ArModel {

    // 表名
    public $tableName = 's_edu_classroom';
    // 初始化
    static public function model($class = __CLASS__) {

        return parent::model($class);
    }

    CONST STATUS_STOPED = 0;
    CONST STATUS_APPROVED = 1;
    // 状态
    static $STATUS_MAP = array(
         1 => '启用',
         0 => '停用',
         2 => '<span style="color:green">已升级</span>',
         3 => '<span style="color:blue">已毕业</span>',
    );

    // 类型
    static $TYPE_CLASS = array(
        // null =>'请选择',
         1 => '托班',
         2 => '小班',
         3 => '中班',
         4 => '大班',
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
            /**
             * to do 追加数组数据
             */
            $res['first_school_year'] = EduSchoolYearModel::model()
                ->getDb()
                ->where(array('yid' => $res['yid']))
                ->queryColumn('first_school_year');
	        $res['last_school_year'] = EduSchoolYearModel::model()
                ->getDb()
	            ->where(array('yid' => $res['yid']))
	            ->queryColumn('last_school_year');
            $res['teacher'] = CompanySchoolMemberModel::model()
                ->getDb()
                ->select('realname')
                ->where(array('mid'=>$res['mid']))
                ->queryRow();
            return $res;
        endif;

        return $arr;

    }


    // 获取班级select信息
    public function getClassNameSelect(array $arr)
    {
        // $arr = EduClassroomModel::model()->getDb()->queryAll();
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($arr)) :// 检测是否对维数组
            foreach ($arr as &$res) :
                $res = $this->getClassNameSelect($res);
            endforeach;
        else :
            $res = $arr;
            /**
             * to do 追加数组数据
             */
            $res['schoolYear'] = EduSchoolYearModel::model()->getDb()
                ->where(array('yid' => $res['yid']))
                ->queryRow();
            foreach ($res as $k=>$v):
                $res['SY'] = $res['schoolYear']['first_school_year'].'~'.$res['schoolYear']['last_school_year'].' '.$res['class_name'];
            endforeach;
            // echo($res['schoolYear']['first_school_year']);


            // switch ($res['class_type'])
            // {
            //     case '1':
            //         $res['cname'] = $res['class']['first_school_year']."~".$res['class']['last_school_year']." "."托班"."-".$res['class_name'];
            //         break;
            //     case '2':
            //         $res['cname'] = $res['class']['first_school_year']."~".$res['class']['last_school_year']." "."小班"."-".$res['class_name'];
            //         break;
            //     case '3':
            //         $res['cname'] = $res['class']['first_school_year']."~".$res['class']['last_school_year']." "."中班"."-".$res['class_name'];
            //         break;
            //     case '4':
            //         $res['cname'] = $res['class']['first_school_year']."~".$res['class']['last_school_year']." "."大班"."-".$res['class_name'];
            //         break;
            //     default:
            //         break;
            // }
            // switch ($res['class_type'])
            // {
            //     case '1':
            //         $res['cname'] = "托班"."-".$res['class_name'];
            //         break;
            //     case '2':
            //         $res['cname'] = "小班"."-".$res['class_name'];
            //         break;
            //     case '3':
            //         $res['cname'] = "中班"."-".$res['class_name'];
            //         break;
            //     case '4':
            //         $res['cname'] = "大班"."-".$res['class_name'];
            //         break;
            //     default:
            //         break;
            // }
            return $res;
        endif;
        return $arr;

    }

}
