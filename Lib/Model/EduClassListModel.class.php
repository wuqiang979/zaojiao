<?php
/**
 * 班级列表.
 */
class EduClassListModel extends ArModel {

    // 表名
    public $tableName = 's_edu_class_list';
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
           
            $res['bids'] = explode(',',$res['bids']);
            
            foreach ($res['bids'] as $k=>$v):
                $res['babys'][] = BabyModel::model()->getDb()
                ->where(array('bid' => $v))
                ->queryRow(); 
            endforeach;  
            return $res;
        endif;
        
        return $res['babys'];
       
    }


    // 获取关联表信息
    public function getDetail(array $arr)
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
           
            var_dump($res);
            
            foreach($res as $k=>$v):
                $res['babys'] = BabyModel::model()->getDb()
                ->where(array('bid' => $v[$k]))
                ->queryRow(); 
                echo $v[$k];
            endforeach;
            // var_dump($res);
            return $res;
        endif;
        
        return $res['babys'];
       
    }

    // 检测班级列表中是否有当前幼儿 
    // 参数：cid:班级ID bid:幼儿ID
    public function classListIsHaveBaby($cid,$bid)
    {   
        $eno = null;
        $res = EduClassListModel::model()
            ->getDb()
            ->where(array('cid'=>$cid))
            ->queryRow();
        $bids = explode(',', $res['bids']);
        foreach($bids as $k=>$v):
            if($v==$bid):
                $eno = 1;
            endif;
        endforeach;

        //返回数据
        if($eno==1):
            return true;
        else:
            return false;
        endif;
    }
    
    //删除学生列表中的某个学生
    public function delbid($cid,$bid)
    {
        $classList = EduClassListModel::model()
            ->getDb()
            ->where(array('cid'=>$cid))
            ->queryRow();
        $bids = explode(',', $classList['bids']);
        foreach($bids as $k=>$v):
            if($bid == $v):
                unset($bids[$k]);
            endif;
        endforeach;
        $bids = implode(',', $bids);
        //修改数据库
        $eno = EduClassListModel::model()
            ->getDb()
            ->where(array('cid'=>$cid))
            ->update(array('bids'=>$bids));
        // 返回数据
        if($eno):
            return true;
        else:
            return false;
        endif;
    }

    //向学生列表中添加某个学生
    public function addbid($cid,$bid)
    {
        
        $classList = EduClassListModel::model()
            ->getDb()
            ->where(array('cid'=>$cid))
            ->queryRow();
        $bids = $classList['bids'];
        if(empty($bids)):
            $bids = $bid;
        else:
            $bids = $bids.','.$bid;
        endif;
        // 修改数据库
        $eno = EduClassListModel::model()
            ->getDb()
            ->where(array('cid'=>$cid))
            ->update(array('bids'=>$bids));
        // 返回数据
        if($eno):
            return true;
        else:
            return false;
        endif;
    }
    // 获取学年select列表信息
    // public function getSelect($condition)
    // {
    //     $arr = EduSchoolYearModel::model()
    //         ->getDb()
    //         ->select('yid,first_school_year,last_school_year')
    //         ->where($condition)
    //         ->order('yid desc')
    //         ->queryAll();
    //     foreach ($arr as $k => $v):
    //         $res['yid'] = $v['yid'];
    //         $res['school_year'] = $v['first_school_year']."~".$v['last_school_year'];
    //         $result[] = $res;
    //     endforeach;
    //     return $result;
    // }
}
