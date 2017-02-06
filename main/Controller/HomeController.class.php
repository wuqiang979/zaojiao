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
// 家园互通
class HomeController extends BaseController
{
    public function init()
    {
        parent::init();
        // 设置布局文件
        $this->setLayoutFile('content');

        $headnavs = array(
            '活动管理' => arU('Home/activityManage'),
            '活动签到' => arU('Home/eventSign'),
            '活动签到记录' => arU('Home/eventSignRecord'),
            '开放日管理' => arU('Home/openDayManage'),
        );
        $this->assign(array('headnavs' => $headnavs));
    }
    //幼儿成长评价
    public function babyGrowAction()
    {
        $this->display('');
    }
    //活动管理
    public function activityManageAction()
    {
        // 初始化
        $sid         = arCfg('current_member.sid');
        $data        = arPost();
        $data['sid'] = $sid;
        $eno         = null;
        if(isset($data['type'])):
            $type = $data['type'];
            unset($data['type']);
            $data['activity_time'] = strtotime($data['activity_time']);
        endif;

        // 操作
        if(isset($type)):
            // 添加
            if($type=='add'):
                $data['add_time']      = time();
                $data['mid_creater']   = arCfg('current_member.mid');
                unset($data['aid']);
                $eno = ActivityModel::model()
                    ->getDb()
                    ->insert($data,true);
            // 修改
            elseif($type=='modify'):
                $aid = $data['aid'];
                unset($data['aid']);
                unset($data['sid']);
                $eno = ActivityModel::model()
                    ->getDb()
                    ->where(array('aid'=>$aid))
                    ->update($data,true);
            endif;
            // 错误号
            if($eno):
                $this->redirectSuccess();
            else:
                $this->redirectError();
            endif;
        endif;
        // 搜索条件
        $condition = array(
            'sid' =>$sid,
        );
        $theme = urldecode(arGet('searchtext'));
        $condition[' activity_theme like '] = '%'.$theme.'%';
        $condition['status'] = arGet('status');
        $condition = array_filter($condition);
        // var_dump($condition);

        // 分页计算
        $total = ActivityModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total,10);

        // 获取数据
        $res = ActivityModel::model()
            ->getDb()
            ->where($condition)
            ->order('aid desc')
            ->limit($page->limit())
            ->queryAll();


        // 获取关联数据
        $res = ActivityModel::model()->getDetailInfo($res);

        // 获取其他数据
        // 获取教师列表
        $teachers = EduClassTeacherModel::model()
            ->getDb()
            ->where(array('sid'=>$sid))
            ->queryAll();
        $teachers = EduClassTeacherModel::model()->getDetailInfo($teachers);

        // 分配模板
        $this->assign(array('res'=>$res,'page'=>$page,'teachers'=>$teachers));

        // 数据输出
        // echo('<pre>');
        //     print_r($res);
        // echo('</pre>');

        $this->display('');
    }
    // 删除活动
    public function activityDelAction()
    {
        if ($aid = arRequest('aid')) :
            $delResult = ActivityModel::model()
                ->getDb()
                ->where(array('aid' => $aid))
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
    //活动签到
    public function eventSignAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $data = arPost();

        if(arPost()):
            // 数据处理
            $data['sid'] = $sid;
            $data['sign_time']  =  time();
            $data['today'] = mktime(0,0,0,date('m',time()),date('d',time()),date('Y',time()));

            // 获取幼儿信息
            $baby = BabyModel::model()
                ->getDb()
                ->where(array('bid'=>$data['bid']))
                ->queryRow();
            $data['bname'] = $baby['name'];

            // 获取活动主题
            $activity = ActivityModel::model()
                ->getDb()
                ->where(array('aid'=>$data['aid']))
                ->queryRow();
            $data['activity_theme'] = $activity['activity_theme'];

            // 签到操作
            $eno = ActivitySignRecordModel::model()
                ->getDb()
                ->insert($data,true);

            if($eno):
                $this->redirectSuccess();
            else:
                $this->redirectError();
            endif;
        endif;

        // 获取班级列表
        $bb = EduClassroomModel::model()->getDb()
                ->where(array('sid'=>$sid,'status'=>1))
                ->order('class_type desc,cid asc')
                ->queryAll();
        $classSelect = EduClassroomModel::model()->getClassNameSelect($bb);

        // 获取活动列表
        $activities = ActivityModel::model()
            ->getDb()
            ->where(array('sid'=>$sid,'status'=>1))
            ->queryAll();

        // echo('<pre>');
        // print_r($data);
        // echo('</pre>');


        // 分配模板
        $this->assign(array('cs'=>$classSelect,'activities'=>$activities));

        $this->display('');
    }
    //活动签到记录
    public function eventSignRecordAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $data = arPost();
        if(isset($data['type'])):
            $type = $data['type'];
            $data['sign_time'] = strtotime($data['sign_time']);
            $data['today'] = mktime(0,0,0,data('m',time()),data('d',time()),data('Y',time()));
            unset($data['type']);
        endif;
        // 操作

        // 搜索条件
        $condition = array(
            'sid' =>$sid,
        );
        $stheme = urldecode(arGet('stheme'));
        $sbname = urldecode(arGet('sbname'));
        $sign_time = arGet('ssign_time');
        $condition[' activity_theme like '] = '%'.$stheme.'%';
        $condition[' bname like '] = '%'.$sbname.'%';
        $condition['today'] = strtotime($sign_time);
        $condition = array_filter($condition);

        // 分页计算
        $total = ActivitySignRecordModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total,10);

        // 获取数据
        $res = ActivitySignRecordModel::model()
            ->getDb()
            ->where($condition)
            ->order('asid desc')
            ->limit($page->limit())
            ->queryAll();
        // 获取关联数据
        $res = ActivitySignRecordModel::model()->getDetailInfo($res);
        // 获取其他数据↓

        // 分配模板
        $this->assign(array('res'=>$res,'page'=>$page,));

        $this->display('');
    }
    //开放日管理
    public function openDayManageAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $data = arPost();
        $data['sid'] = $sid;
        if(isset($data['type'])):
            $type = $data['type'];
            $data['open_time'] = strtotime($data['open_time']);
            unset($data['type']);
        endif;

        // 操作
        if(isset($type)):
            // 添加
            if($type=='add'):
                unset($data['odid']);
                $data['mid_creater'] = arCfg('current_member.mid');
                $eno = ActivityOpenDayModel::model()
                    ->getDb()
                    ->insert($data,true);
            // 修改
            elseif($type=='modify'):
                $odid = $data['odid'];
                unset($data['odid']);
                unset($data['sid']);
                $eno = ActivityOpenDayModel::model()
                    ->getDb()
                    ->where(array('odid'=>$odid))
                    ->update($data,true);
            endif;
            // 错误号
            if($eno):
                $this->redirectSuccess();
            else:
                $this->redirectError();
            endif;
        endif;

        // 搜索条件
        $condition = array(
            'sid' => $sid,
        );
        $condition['cid'] = arGet('scid');
        $condition['open_type'] = arGet('sopen_type');
        $condition['mid_leader'] = arGet('smid');
        $condition = array_filter($condition);

        // 分页计算
        $total = ActivityOpenDayModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total,10);

        // 获取数据
        $res = ActivityOpenDayModel::model()
            ->getDb()
            ->where($condition)
            ->order('odid desc')
            ->limit($page->limit())
            ->queryAll();

        // 获取关联数据
        $res = ActivityOpenDayModel::model()->getDetailInfo($res);
        // 获取其他数据↓

        //获取班级名称select列表
        $clsselect = EduClassroomModel::model()->getDb()
            ->where(array('sid'=>$sid,'status'=>1))
            ->order('class_type desc,cid asc')
            ->queryAll();
        $clsselect = EduClassroomModel::model()->getClassNameSelect($clsselect);

        // 获取教师列表
        $teachers = EduClassTeacherModel::model()
            ->getDb()
            ->where(array('sid'=>$sid))
            ->queryAll();
        $teachers = EduClassTeacherModel::model()->getDetailInfo($teachers);

        // 分配模板
        $this->assign(array('res'=>$res,'page'=>$page,'cs'=>$clsselect,'teachers'=>$teachers));

        $this->display('');
    }
    // 删除开放日
    public function activityOpenDayDelAction()
    {
        if ($odid = arRequest('odid')) :
            $delResult = ActivityOpenDayModel::model()
                ->getDb()
                ->where(array('odid' => $odid))
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
