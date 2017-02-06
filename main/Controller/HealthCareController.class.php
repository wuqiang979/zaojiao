<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ck <yushaohunzhu@sina.com>
 */

/**
 * Default Controller of webapp.
 */

class HealthCareController extends BaseController
{
    public function init()
    {
        parent::init();
        // 设置布局文件
        $this->setLayoutFile('content');

        $headnavs = array(
            '幼儿体检管理' => arU('HealthCare/examinationList'),
            '用药记录' => arU('HealthCare/medicalRecord'),
            '意外伤害记录' => arU('HealthCare/accidentRecord'),
            '事故个案分析' => arU('HealthCare/accidentAnalyze'),
            '生活情况记录' => arU('HealthCare/lifeRecord'),
        );
        $this->assign(array('headnavs' => $headnavs));

    }
    //体检记录
    public function examinationListAction()
    {   
        // 初始化
        $sid = arCfg('current_member.sid');
        $condition=array();
        $arr = array_filter(arPost());// 删除数组中的空值
        if(isset($arr['examination_time'])){$arr['examination_time'] = strtotime($arr['examination_time']);}

        // 添加/修改记录
        if(isset($arr['type'])):
            // 添加记录
            if($arr['type']=='add'):
                unset($arr['type']);
                unset($arr['eid']);
                $arr['sid'] = $sid;
                // 获取当前幼儿信息
                $baby = BabyModel::model()
                    ->getDb()
                    ->where(array('bid'=>$arr['bid']))
                    ->queryRow();
                $arr['bname'] = $baby['name'];
                if(count($arr)>5):
                    $eno = HealthExaminationModel::model()->getDb()
                        ->insert($arr,true);
                else:
                    $this->redirectError();
                endif;
            // 修改记录
            elseif($arr['type']=='modify'):
                $eid = array('eid'=>$arr['eid']);
                unset($arr['type']);
                unset($arr['mid']);
                $eno = HealthExaminationModel::model()->getDb()
                    ->where($eid)
                    ->update($arr,true);
            endif;
            if($eno):
                $this->redirectSuccess();
            else:
                $this->redirectError();
            endif;
        endif;

        // 处理搜索条件
        $condition['sid'] = $sid;
        if(($bname = arGet('searchtext'))):
            $bname = urldecode($bname);
            $condition['bname like'] = '%'.$bname.'%';
        endif;

        // 分页计算
        $total = HealthExaminationModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);

        // 获取数据
        $res = HealthExaminationModel::model()->getDb()
            ->where($condition)
            ->order('eid desc')
            ->limit($page->limit())
            ->queryAll();
        // 获取关联数据
        $res = HealthExaminationModel::model()->getDetailInfo($res);

        // 获取班级列表
        $bb = EduClassroomModel::model()->getDb()
                ->where(array('sid'=>$sid,'status'=>1))
                ->order('class_type desc,cid asc')
                ->queryAll();
        $classSelect = EduClassroomModel::model()->getClassNameSelect($bb);

        // 分配模板
        $this->assign(array('examinationList' => $res,'page'=>$page,'cs'=>$classSelect));

        $this->display('@ExaminationManage/examinationList');
    }
    // 删除用药情况记录
    public function examinationListDelAction()
    {
        if ($eid = arRequest('eid')) :
            $delResult = HealthExaminationModel::model()
                ->getDb()
                ->where(array('eid' => $eid))
                ->delete();
            if ($delResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError('参数错误');
        endif;
    }
    // 用药记录
    public function medicalRecordAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $condition=array();
        $arr = array_filter(arPost());// 删除数组中的空值
        if(isset($arr['date'])){$arr['date'] = strtotime($arr['date']);}

        // 添加/修改记录
        if(isset($arr['type'])):
            // 添加记录
            if($arr['type']=='add'):
                unset($arr['type']);
                unset($arr['mid']);
                $arr['sid'] = $sid;
                if(count($arr)>5):
                    $eno = HealthMedicalRecordModel::model()->getDb()
                        ->insert($arr,true);
                else:
                    $this->redirectError();
                endif;
            // 修改记录
            elseif($arr['type']=='modify'):
                $mid = array('mid'=>$arr['mid']);
                unset($arr['type']);
                unset($arr['mid']);
                $eno = HealthMedicalRecordModel::model()->getDb()
                    ->where($mid)
                    ->update($arr,true);
            endif;
            if($eno):
                $this->redirectSuccess();
            else:
                $this->redirectError();
            endif;
        endif;

        // 处理搜索条件
        $condition['sid'] = $sid;
        if(($bname = arGet('searchtext'))):
            $bname = urldecode($bname);
            $condition[' bname like '] = '%'.$bname.'%';
        endif;

        // 分页计算
        $total = HealthMedicalRecordModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);

        // 获取数据
        $res = HealthMedicalRecordModel::model()->getDb()
            ->where($condition)
            ->order('mid desc')
            ->limit($page->limit())
            ->queryAll();
        // 获取关联数据
        $res = HealthMedicalRecordModel::model()->getDetailInfo($res);

        // 获取班级select列表
        $clsselect = EduClassroomModel::model()->getDb()
            ->where(array('sid'=>$sid,'status'=>1))
            ->order('class_type desc,cid asc')
            ->queryAll();
        $clsselect = EduClassroomModel::model()->getClassNameSelect($clsselect);

        // 分配模板
        $this->assign(array('res'=>$res,'page'=>$page,'cs'=>$clsselect));
        $this->display('');
    }
    // 删除用药情况记录
    public function medicalRecordDelAction()
    {
        if ($mid = arRequest('mid')) :
            $delResult = HealthMedicalRecordModel::model()
                ->getDb()
                ->where(array('mid' => $mid))
                ->delete();
            if ($delResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError('参数错误');
        endif;
    }
    //意外伤害记录
    public function accidentRecordAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $condition=array();
        $arr = array_filter(arPost());// 删除数组中的空值
        if(isset($arr['accident_time'])){$arr['accident_time'] = strtotime($arr['accident_time']);}

        // 添加/修改记录
        if(arPost()):
            // 添加记录
            if($arr['type']=='add'):
                unset($arr['type']);
                unset($arr['arid']);
                $arr['sid'] = $sid;
                $arr['mid'] = arCfg('current_member.mid');
                if(count($arr)>8):
                    $eno = HealthAccidentRecordModel::model()->getDb()
                        ->insert($arr);
                else:
                    $this->redirectError();
                endif;
            // 修改记录
            elseif($arr['type']=='modify'):
                $mid = array('arid'=>$arr['arid']);
                unset($arr['type']);
                unset($arr['arid']);
                $eno = HealthAccidentRecordModel::model()->getDb()
                    ->where($mid)
                    ->update($arr);
            endif;
            if($eno):
                $this->redirectSuccess();
            else:
                $this->redirectError();
            endif;
        endif;

        // 处理搜索条件
        $condition['sid'] = $sid;
        if(($bid = arGet('bid'))||($name = arGet('name'))):
            if($bid):
                $condition['bid'] = $bid;
            else:
                $condition['name'] = $name;
            endif;
        endif;

        // 分页计算
        $total = HealthAccidentRecordModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);

        // 获取数据
        $res = HealthAccidentRecordModel::model()->getDb()
            ->where($condition)
            ->order('arid desc')
            ->limit($page->limit())
            ->queryAll();
        // 获取关联数据
        $res = HealthAccidentRecordModel::model()->getDetailInfo($res);

        //获取班级名称select列表
        $clsselect = EduClassroomModel::model()->getDb()
            ->where(array('sid'=>$sid,'status'=>1))
            ->order('class_type desc,cid asc')
            ->queryAll();
        $clsselect = EduClassroomModel::model()->getClassNameSelect($clsselect);

        // 分配模板
        $this->assign(array('res'=>$res,'page'=>$page,'cs'=>$clsselect));
        $this->display('');
    }
    // 删除意外伤害记录
    public function accidentRecordDelAction()
    {
        if ($arid = arRequest('arid')) :
            $delResult = HealthAccidentRecordModel::model()
                ->getDb()
                ->where(array('arid' => $arid))
                ->delete();
            if ($delResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError('参数错误');
        endif;
    }
    // 事故个案分析
    public function accidentAnalyzeAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $condition=array();
        $arr = array_filter(arPost());// 删除数组中的空值
        if(isset($arr['accident_time'])){$arr['accident_time'] = strtotime($arr['accident_time']);}

        // 添加/修改记录
        if(arPost()):
            // 添加记录
            if($arr['type']=='add'):
                unset($arr['type']);
                unset($arr['aaid']);
                $arr['sid'] = $sid;
                $arr['mid'] = arCfg('current_member.mid');
                // 获取幼儿信息
                $baby = BabyModel::model()
                    ->getDb()
                    ->where(array('bid'=>$arr['bid']))
                    ->queryRow();
                $arr['bname'] = $baby['name'];
                // 插入
                $eno = HealthAccidentAnalyzeModel::model()->getDb()
                    ->insert($arr,true);
            // 修改记录
            elseif($arr['type']=='modify'):
                $mid = array('aaid'=>$arr['aaid']);
                unset($arr['type']);
                unset($arr['aaid']);
                $eno = HealthAccidentAnalyzeModel::model()->getDb()
                    ->where($mid)
                    ->update($arr);
            endif;
            if($eno):
                $this->redirectSuccess();
            else:
                $this->redirectError();
            endif;
        endif;

        // 处理搜索条件
        $condition['sid'] = $sid;
        if(($bname = arGet('searchtext'))):
            $bname = urldecode($bname);
            $condition[' bname like'] = '%'.$bname.'%';
        endif;

        // 分页计算
        $total = HealthAccidentAnalyzeModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);

        // 获取数据
        $res = HealthAccidentAnalyzeModel::model()->getDb()
            ->where($condition)
            ->order('aaid desc')
            ->limit($page->limit())
            ->queryAll();

        // 获取关联数据
        $res = HealthAccidentAnalyzeModel::model()->getDetailInfo($res);

        // 获取班级select列表
        //获取班级名称select列表
        $clsselect = EduClassroomModel::model()->getDb()
            ->where(array('sid'=>$sid,'status'=>1))
            ->order('class_type desc,cid asc')
            ->queryAll();
        $clsselect = EduClassroomModel::model()->getClassNameSelect($clsselect);

        // 分配模板
        $this->assign(array('res'=>$res,'page'=>$page,'cs'=>$clsselect));
        $this->display('');
    }
    // 删除意外伤害记录
    public function accidentAnalyzeDelAction()
    {
        if ($aaid = arRequest('aaid')) :
            $delResult = HealthAccidentAnalyzeModel::model()
                ->getDb()
                ->where(array('aaid' => $aaid))
                ->delete();
            if ($delResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError('参数错误');
        endif;
    }
    //日常生活情况记录
    public function lifeRecordAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $condition=array();
        $arr = array_filter(arPost());// 删除数组中的空值
        if(isset($arr['accident_time'])){$arr['accident_time'] = strtotime($arr['accident_time']);}

        // 添加/修改记录
        if(arPost()):
            // 添加记录
            if($arr['type']=='add'):
                $arr['record_time'] = time();
                unset($arr['type']);
                unset($arr['lid']);
                $arr['sid']= $sid;
                $arr['mid'] = arCfg('current_member.mid');
                // 获取幼儿信息
                $baby = BabyModel::model()
                    ->getDb()
                    ->where(array('bid'=>$arr['bid']))
                    ->queryRow();
                $arr['bname'] = $baby['name'];
                // 插入
                if(count($arr)>10):
                    $eno = HealthLifeRecordModel::model()->getDb()
                        ->insert($arr,true);
                else:
                    $this->redirectError();
                endif;
            // 修改记录
            elseif($arr['type']=='modify'):
                $mid = array('lid'=>$arr['lid']);
                unset($arr['type']);
                unset($arr['lid']);
                $eno = HealthLifeRecordModel::model()->getDb()
                    ->where($mid)
                    ->update($arr,true);
            endif;
            if($eno):
                $this->redirectSuccess();
            else:
                $this->redirectError();
            endif;
        endif;

        // 处理搜索条件
        $condition['sid'] = $sid;
        if(($bname = arGet('searchtext'))):
            $bname = urldecode($bname);
            $condition[' bname like'] = '%'.$bname.'%';
        endif;

        // 分页计算
        $total = HealthLifeRecordModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);

        // 获取数据
        $res = HealthLifeRecordModel::model()->getDb()
            ->where($condition)
            ->order('lid desc')
            ->limit($page->limit())
            ->queryAll();

        // 获取关联数据
        $res = HealthLifeRecordModel::model()->getDetailInfo($res);

        //获取班级名称select列表
        $clsselect = EduClassroomModel::model()->getDb()
            ->where(array('sid'=>$sid,'status'=>1))
            ->order('class_type desc,cid asc')
            ->queryAll();
        $clsselect = EduClassroomModel::model()->getClassNameSelect($clsselect);

        // 分配模板
        $this->assign(array('res'=>$res,'page'=>$page,'cs'=>$clsselect));
        $this->display('');
    }
    // 删除意外伤害记录
    public function lifeRecordDelAction()
    {
        if ($lid = arRequest('lid')) :
            $delResult = HealthLifeRecordModel::model()
                ->getDb()
                ->where(array('lid' => $lid))
                ->delete();
            if ($delResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError('参数错误');
        endif;
    }

}
