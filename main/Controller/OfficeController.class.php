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

class OfficeController extends BaseController
{
	// 初始加载
	public function init()
    {
         arSeg(array(
                'loader' => array(
                    'plugin' => 'layer',
                    'this' => $this
                )
            )

            );

         parent::init();
        // 设置布局文件
        $this->setLayoutFile('content');

        $headnavs = array(
            '通知公告' => arU('Office/notice'),
            '园所档案' => arU('Office/parkFile'),
            '员工档案' => arU('Office/teacherFile'),
            '请假管理' => arU('Office/leaveManage'),
            '考勤查看' => arU('Office/attendance'),
            '考勤明细' => arU('Office/attendanceDetails')
        );
        $this->assign(array('headnavs' => $headnavs));
    }

	// 通知公告
	public function noticeAction()
	{
		if (arPost()) {
            // 检查是否为园长
            if (!arModule('Lib.Auth')->isYz()) :
                $this->redirectError();
            else:
                $data = array_map('trim', arPost());
                if ($data['nid']) {
                    # edit...
                    $nid = $data['nid'];
                    unset($data['nid']);
                    if(U_company_school_noticeModel::model() -> getDb() -> where('nid='.$nid) -> update($data)):
                        $this->redirectSuccess();
                    else :
                        $this->redirectError();
                    endif;

                }else{
                    # add
                    unset($data['nid']);
                    $data['writetime'] = time();
                    $data['sid'] = arCfg('current_member.sid'); // 是哪所学校
                    if(U_company_school_noticeModel::model() -> getDb() -> insert($data)):
                        $this->redirectSuccess();
                    else :
                        $this->redirectError();
                    endif;
                }
            endif;
		}

		$total = U_company_school_noticeModel::model() -> getDb() -> count();
		$per = 20;
		$Pageone = new Pageone($total,$per);
		$listSql = 'SELECT n.*, s.name as s_name FROM u_company_school_notice as n, u_company_school as s WHERE n.sid='.arCfg('current_member.sid').' and s.sid=n.sid order by n.nid desc '.$Pageone -> limit;
		$pagelist = $Pageone ->fpage();
		$list = U_company_school_noticeModel::model() -> getDb()
				-> sqlQuery($listSql);

		$this -> assign(array('list' => $list, 'pagelist' => $pagelist ));
		$this->display();
	}

	// delNotice 删除通知公告
	public function delNoticeAction(){
		// $nid = arRequest('nid');
        // 检查是否为园长
        if (!arModule('Lib.Auth')->isYz()) :
            $this->showJsonError('没有权限删除');
        else:
            if ($nid = arRequest('nid')) :
                $delResult = U_company_school_noticeModel::model()
                    ->getDb()
                    ->where(array('nid' => $nid))
                    ->delete();
                if ($delResult) :
                    $this->showJsonSuccess();
                else :
                    $this->showJsonError('删除失败');
                endif;

            else :
                $this->showJsonError('参数错误');
            endif;
        endif;
	}

	// searchNotice 搜索通知公告
	public function searchNoticeAction(){
		if(arRequest()){
			$data = array_map('trim', arRequest());
			if ($data['headline'] && $data['classify']) {
				$str = 'and n.headline like "%'.$data['headline'].'%" and n.classify='.$data['classify'];
			}elseif(!$data['headline'] && $data['classify']){
				$str = ' and n.classify='.$data['classify'];
			}elseif ($data['headline'] && !$data['classify']) {
				$str = ' and n.headline like "%'.$data['headline'].'%" ';
			}elseif(!$data['headline'] && !$data['classify']){
				$str = '';
			}
			$listSql = 'SELECT n.*, s.name as s_name FROM u_company_school_notice as n, u_company_school as s WHERE n.sid='.arCfg('current_member.sid').' and s.sid=n.sid '.$str.' order by n.nid desc ';
            //var_dump($listSql);exit;
			$listRes = U_company_school_noticeModel::model() -> getDb() -> sqlQuery($listSql);
			$total = count($listRes);
			$per = 20;
			$Pageone = new Pageone($total,$per);
			$listSql = 'SELECT n.*, s.name as s_name FROM u_company_school_notice as n, u_company_school as s WHERE n.sid='.arCfg('current_member.sid').' and s.sid=n.sid '.$str.' order by n.nid desc '.$Pageone -> limit;
			$pagelist = $Pageone ->fpage();
			$list = U_company_school_noticeModel::model() -> getDb()
					-> sqlQuery($listSql);

			// 获取学校
			$school = U_company_schoolModel::model() -> getDb() -> select('sid, name') -> queryAll();
			$this -> assign(array('list' => $list, 'school' => $school, 'pagelist' => $pagelist ));
			$this->display('notice');
		}

	}

	// 园所档案
	public function parkFileAction()
	{
		if (arPost()) :
            // 检查是否为园长
            if (!arModule('Lib.Auth')->isYz()) :
                $this->redirectError();
            else:
                $data = array_map('trim', arPost());
                if ($rid = arRequest('rid')) :
                    // 写入数据库 edit
                    $insertResult = U_company_school_recordModel::model()
                        ->getDb()
                        ->where(array('rid' => $rid))
                        ->update($data, 1);
                else :
                    // 写入数据库 add
                    $data['addtime'] = time();
                    $data['name'] = $data['sname'];
                    $data['cid'] = arCfg('current_member.cid');
                    // 记录到 u_company_school 表
                    $data['sid'] = CompanySchoolModel::model()
                        ->getDb()
                        ->insert($data, 1);
                    // 记录到 u_company_school_record 表
                    $insertResult = U_company_school_recordModel::model()->getDb()->insert($data, 1);
                endif;
                if ($insertResult) :
                    $this->redirectSuccess();
                else :
                    $this->redirectError();
                endif;
            endif;

        endif;

        // 搜索条件
        $condition = array(
        );
        // $condition['sid'] = arCfg('current_member.sid');
        if ($searchtext = urldecode(arRequest('searchtext'))) :
            $condition['sname like '] = '%' . $searchtext . '%';
        endif;

        // 分页计算
        $total = U_company_school_recordModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $per = 10;
        $page = new Pageone($total, $per);

        // 获取数据
        $list = U_company_school_recordModel::model()
            ->getDb()
            ->where($condition)
            ->order('rid desc')
            ->limit($page->limit())
            ->queryAll();
        $pagelist = $page ->fpage();
        // 获取关联表详细信息
        //$schools = CompanySchoolModel::model()->getDetailInfo($schools);

        // 分配模板
        $this->assign(array('list' => $list, 'pagelist' => $pagelist));
		$this->display();
	}

	// delRecord 删除通知园所档案
	public function delRecordAction(){
		// $nid = arRequest('nid');
        if (!arModule('Lib.Auth')->isYz()) :
                $this->showJsonError('没有权限删除');
            else:
                if ($rid = arRequest('rid')) :
                    // 查询到 sid
                    $school = U_company_school_recordModel::model()
                        ->getDb()
                        ->where(array('rid' => $rid))
                        ->queryRow();
                    $delResult = CompanySchoolModel::model()
                        ->getDb()
                        ->where(array('sid' => $school['sid']))
                        ->delete();
                    $delResult = U_company_school_recordModel::model()
                        ->getDb()
                        ->where(array('rid' => $rid))
                        ->delete();
                    if ($delResult) :
                        $this->showJsonSuccess();
                    else :
                        $this->showJsonError('删除失败');
                    endif;

                else :
                    $this->showJsonError('参数错误');
                endif;
            endif;

	}

	// 员工档案
	public function teacherFileAction()
	{
		if (arPost()) :
			$data = array_map('trim', arPost());
            $data['sid'] = arCfg('current_member.sid');
            $data['cid'] = arCfg('current_member.cid');
            $data['mobile'] =$data['telephone'];
            if ($rid = arRequest('tid')) :
                // 写入数据库 edit
                if ($_FILES):
	                # 有上传图片
	                $data['picture'] = arComp('ext.upload')->upload('picture', arComp('url.route')->pathToDir(AR_SERVER_PATH.'Upload'));
	                // var_dump(arComp('ext.upload') -> errorMsg());exit;
	            else:
	               unset($data['picture']);
	            endif;
                $name = $data['account_name'];
	            // password 处理
	            unset($data['account_pwd']);
                unset($data['account_name']);

                // 记录到另一个成员表中
                CompanySchoolMemberModel::model() -> getDb() ->where(array('account_name' => $name)) -> update($data, 1);
                $insertResult = U_company_school_member_recordModel::model()
                    ->getDb()
                    ->where(array('tid' => $rid))
                    ->update($data, 1);
            else :
                // 检查是否为园长
                if (!arModule('Lib.Auth')->isYz()) :
                    // $this->redirectError();
                    $insertResult = '';
                else:
                    // 两次输入的密码相同
                    if ($data['account_name'] && $data['account_pwd'] && $data['account_pwd'] === $data['account_pwd2'] ):
                        // 上传图片的处理
                        if ($_FILES):
                            # 有上传图片
                            $data['picture'] = arComp('ext.upload')->upload('pic', arComp('url.route')->pathToDir(AR_SERVER_PATH.'Upload'));
                            // var_dump(arComp('ext.upload') -> errorMsg());exit;
                        else:
                           $data['picture'] = '';
                        endif;
                        // 登录账号是否已存在
                        $account = CompanySchoolMemberModel::model()
                            ->getDb()
                            ->where(array('account_name' => $data['account_name']))
                            ->queryRow();

                        if($account):
                            $data['account_pwd'] = CompanySchoolMemberModel::gPwd($data['account_pwd']);
                            $data['recordtime'] = $data['reg_time'] = time();
                            $data['grid'] = 2;
                            // 记录到另一个成员表中
                            $data['mid'] = CompanySchoolMemberModel::model() -> getDb() -> insert($data, 1);
                            // 写入数据库 add
                            $insertResult = U_company_school_member_recordModel::model()->getDb()->insert($data, 1);
                        else:
                            $insertResult = '';
                        endif;
                    else:
                        $insertResult = '';
                    endif;
                endif;
            endif;
            if ($insertResult) :
                $this->redirectSuccess();
            else :
                $this->redirectError();
            endif;
        endif;


		// 班级列表
		$classRoom = EduClassroomModel::model()
            ->getDb()
            ->select('cid, class_name')
            ->where('sid='.arCfg('current_member.sid'))
            ->queryAll();

		// 搜索条件
        $condition = array(
        );
        $condition['sid'] = arCfg('current_member.sid');
        if ($searchtext = urldecode(arRequest('realname'))) :
            $condition['realname like '] = '%' . $searchtext . '%';
            // $condition['realname'] = $searchtext;
        endif;
        if ($account_name = urldecode(arRequest('account_name'))) :
            $condition['account_name'] = $account_name;
        endif;
        if ($telephone = urldecode(arRequest('telephone'))) :
            $condition['telephone'] = $telephone;
        endif;
        if (arRequest('isonjob') !== null) :
            $isonjob = arRequest('isonjob');
            $condition['isonjob'] = $isonjob;
        endif;

        // 检查是否为园长
        if (!arModule('Lib.Auth')->isYz()) :
            $condition['mid'] = arCfg('current_member.mid');
        // else:
        //     if ($searchtext = urldecode(arRequest('mid'))) :
        //         $condition['mid'] = $searchtext;
        //     endif;
        endif;

        // 分页计算
        $total = U_company_school_member_recordModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $per = 10;
        $page = new Pageone($total, $per);

        // 获取数据
        $list = U_company_school_member_recordModel::model()
            ->getDb()
            ->where($condition)
            ->order('tid desc')
            ->limit($page->limit())
            ->queryAll();
        $list = U_company_school_member_recordModel::model() -> getDetailInfo($list);
        $pagelist = $page ->fpage();
        // var_dump($condition);
        foreach ($list as $key => &$value) {
            if ($value['sex'] == 0) {
                $value['sexhan'] = '男';
            }elseif ($value['sex'] == 1) {
                $value['sexhan'] = '女';
            }
        }
		$this -> assign(array('list' => $list, 'classRoom' => $classRoom, 'pagelist' => $pagelist ));
		$this->display('');
	}

    // 教师档案删除
    public function delTeacherRecordAction(){
        // 检查是否为园长
        if (!arModule('Lib.Auth')->isYz()) :
            $this->showJsonError('没有权限删除');
        else:
            if ($rid = arRequest('tid')) :
                $delResult = U_company_school_member_recordModel::model()
                    ->getDb()
                    ->where(array('tid' => $rid))
                    ->delete();
                if ($delResult) :
                    $this->showJsonSuccess();
                else :
                    $this->showJsonError('删除失败');
                endif;

            else :
                $this->showJsonError('参数错误');
            endif;
        endif;
    }

	// 部门分配
    public function departmentsAssignAction()
    {
        if ($data = arPost()) :
            if ($mid = $data['mid']) :
                $condition = array(
                    'mid' => $mid,
                );
                $dids = implode(',', $data['dids']);
                $rightsData = array(
                    'mid' => $mid,
                    'dids' => $dids,
                    'department' => $dids,
                );
                // 成员部门、岗位表
                $exist = CompanySchoolMemberProfileModel::model()->getDb()->where($condition)->count();
                if ($exist > 0) :
                    CompanySchoolMemberProfileModel::model()->getDb()->where($condition)->update($rightsData, 1);
                else :
                    CompanySchoolMemberProfileModel::model()->getDb()->insert($rightsData, 1);
                endif;
                // 修改教师档案表中
                U_company_school_member_recordModel::model()->getDb()->where($condition)->update($rightsData, 1);
                $this->redirectSuccess(array('', array('mid' => $mid)));
            else :
                $this->redirectError('');
            endif;
        endif;

        if ($mid = arGet('mid')) :
            $hasRights = U_company_school_member_recordModel::model()->getDb()
                ->where(array('mid' => $mid))
                ->queryRow();
            if ($hasRights['department']) :
                $hasRights = explode(',', $hasRights['department']);
            else :
                $hasRights = array();
            endif;
            $this->assign(array('hasRights' => $hasRights));

        else :
            $this->redirectError('ar_up', 'mid参数错误');
        endif;

        $departSet = CompanySchoolDepartmentModel::model()->getAllMenuByDid(1, true);
        $departDetail = CompanySchoolDepartmentModel::model()->getDetailInfo($departSet);
        $this->assign(array('nonav' => true, 'departDetail' => $departDetail));
        $this->display();

    }

    // 岗位分配
    public function postsAssignAction()
    {
        if ($data = arPost()) :
            if ($mid = $data['mid']) :
                $condition = array(
                    'mid' => $mid,
                );
                $postids = implode(',', $data['postids']);
                $rightsData = array(
                    'mid' => $mid,
                    'postids' => $postids,
                    'job' => $postids,
                );
                 // 成员部门、岗位表
                $exist = CompanySchoolMemberProfileModel::model()->getDb()->where($condition)->count();
                if ($exist > 0) :
                    CompanySchoolMemberProfileModel::model()->getDb()->where($condition)->update($rightsData, 1);
                else :
                    CompanySchoolMemberProfileModel::model()->getDb()->insert($rightsData, 1);
                endif;
                // 修改教师档案表中
                U_company_school_member_recordModel::model()->getDb()->where($condition)->update($rightsData, 1);
                $this->redirectSuccess(array('', array('mid' => $mid)));
            else :
                $this->redirectError('');
            endif;
        endif;

        if ($mid = arGet('mid')) :
            $hasRights = U_company_school_member_recordModel::model()->getDb()
                ->where(array('mid' => $mid))
                ->queryRow();
            if ($hasRights['job']) :
                $hasRights = explode(',', $hasRights['job']);
            else :
                $hasRights = array();
            endif;
            $this->assign(array('hasRights' => $hasRights));
        else :
            $this->redirectError('ar_up', 'mid参数错误');
        endif;

        $postSet = CompanySchoolPostModel::model()->getAllMenuByPid(1, true);
        $postDetail = CompanySchoolPostModel::model()->getDetailInfo($postSet);
        // 无导航
        $this->assign(array('nonav' => true, 'postDetail' => $postDetail));
        $this->display();

    }


	// 请假管理
	public function leaveManageAction()
	{
        if (arPost()) :
            $data = array_map('trim', arPost());
            if ($rid = arRequest('id')) :
                // 写入数据库 edit
                $insertResult = TeacherLeaveModel::model()
                    ->getDb()
                    ->where(array('id' => $rid))
                    ->update($data, 1);
            else :
                $data['sid'] = arCfg('current_member.sid');
                $data['recordtime'] = time();
                // 写入数据库 add
                $insertResult = TeacherLeaveModel::model()->getDb()->insert($data, 1);
            endif;
            if ($insertResult) :
                $this->redirectSuccess();
            else :
                $this->redirectError();
            endif;
        endif;

        // 搜索条件
        $condition = array(
        );
        $condition['sid'] = arCfg('current_member.sid');

        // 检查是否为园长
        if (!arModule('Lib.Auth')->isYz()) :
            $condition['mid'] = arCfg('current_member.mid');
        else:
            if ($searchtext = urldecode(arRequest('mid'))) :
                $condition['mid'] = $searchtext;
            endif;
        endif;

        if (arRequest('recordtime1') && arRequest('recordtime2')) :
            $star = strtotime(arRequest('recordtime1'));
            $end = strtotime(arRequest('recordtime2')) + 24*60*60;
            $condition[] = ' recordtime between '. $star .' and ' . $end ;
        elseif(!arRequest('recordtime1') && arRequest('recordtime2')):
            $star = strtotime(arRequest('recordtime2'));
            $end = strtotime(arRequest('recordtime2')) + 24*60*60;
            $condition[] = ' recordtime between '. $star .' and ' . $end ;
        elseif(arRequest('recordtime1') && !arRequest('recordtime2')):
            $star = strtotime(arRequest('recordtime1'));
            $end = strtotime(arRequest('recordtime1')) + 24*60*60;
            $condition[] = ' recordtime between '. $star .' and ' . $end ;
        endif;


        // 分页计算
        $total = TeacherLeaveModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $per = 10;
        $page = new Pageone($total, $per);

        // 获取数据
        $list = TeacherLeaveModel::model()
            ->getDb()
            ->where($condition)
            ->order('id desc')
            ->limit($page->limit())
            ->queryAll();
        $list = TeacherLeaveModel::model() -> getDetailInfo($list);
        $pagelist = $page ->fpage();
        // 所有的员工(非离职)
        $teacher = U_company_school_member_recordModel::model() -> getDb() -> select('mid, realname') -> where('isonjob not in (3) and sid='.arCfg('current_member.sid')) -> queryAll();
        // echo '<pre>';
        // var_dump($condition);
        $this -> assign(array('list' => $list,'teacher' => $teacher, 'pagelist' => $pagelist ));
		$this->display('');
	}

    // 教师请假条删除
    public function leaveDelAction(){
        // $nid = arRequest('nid');
        if ($rid = arRequest('id')) :

            $delResult = TeacherLeaveModel::model()
                ->getDb()
                ->where(array('id' => $rid))
                ->delete();
            if ($delResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError('删除失败');
            endif;

        else :
            $this->showJsonError('参数错误');
        endif;

    }
	// 考勤明细
	public function attendanceDetailsAction()
	{

        // 搜索条件
        $condition = array(
        );
        $condition['sid'] = arCfg('current_member.sid');
        // 检查是否为园长
        if (!arModule('Lib.Auth')->isYz()) :
            $condition['mid'] = arCfg('current_member.mid');
        else:
            if ($account_name = urldecode(arRequest('mid'))) :
                $condition['mid'] = $account_name;
            endif;
        endif;

        $start =  strtotime(date('Y-m-01', time()));
        // $end = strtotime('+1 day', strtotime(date('Y-m-d', time())));
        $end = strtotime(date('Y-m-d', time()));

        if ($searchtext = urldecode(arRequest('Ymd'))) :
            if(date('Y-m', time()) > $searchtext):
                // 是否在入职期以内
                $postdate = U_company_school_member_recordModel::model()
                    ->getDb()
                    ->where(array('mid' => $condition['mid']))
                    ->queryRow();
                $postdateFalge = explode('-', $postdate['postdate']);
                array_pop($postdateFalge);
                $postdateFalge = implode($postdateFalge, '-');
                if($postdate['postdate']):
                    if($postdateFalge > $searchtext):
                        $this->redirectError('attendanceDetails', '入职时间之前');
                    elseif($postdateFalge == $searchtext):
                        $start =  strtotime($postdate['postdate']);
                        $end = strtotime('+1 month', strtotime($postdateFalge.'-01'));
                    else:
                        $start =  strtotime($searchtext.'-01');
                        $end = strtotime('+1 month', strtotime($searchtext.'-01'));
                    endif;
                else:
                    $this->redirectError('attendanceDetails', '入职时间有误');
                endif;
            elseif(date('Y-m', time()) < $searchtext):
                $this->redirectError('attendanceDetails', '不能搜索当前月之后的月份');
            endif;

        endif;

        // 分页计算
        $total = U_company_school_member_recordModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $per = 10;
        $page = new Pageone($total, $per);

        // 获取数据
        $list = U_company_school_member_recordModel::model()
            ->getDb()
            -> select('mid, realname')
            ->where($condition)
            ->order('tid desc')
            ->limit($page->limit())
            ->queryAll();

        // $list = U_company_school_member_recordModel::model() -> getDetailInfo($list);
        foreach ($list as $key => &$value) :
            for ($i=$start, $k = 1, $leaveTimes = $workDays = $workingDays =0 ; $i < $end; $i = $i+3600*24) :
                $value['month'][$k]['xq'] = date('N', $i);
                $dayy = date('Y-m-d', $i);
                if ($value['month'][$k]['xq'] != 6 && $value['month'][$k]['xq'] != 7):
                    $workDays++;
                    $condition['mid'] = $value['mid'];
                    // 考勤情况
                    unset( $condition['leavetime']);
                    $condition['day'] = $dayy;
                    $kaoqin = TeacherAttendanceModel::model()
                        ->getDb()
                        ->where($condition)
                        ->queryRow();
                    if ($kaoqin):
                        // 上班
                        if ($kaoqin['remarkon'] == '正常' && $kaoqin['remarkoff'] == '正常'):
                            $value['month'][$k]['leaveF'] = 0; // 正常上班
                            $workingDays++;
                        elseif($kaoqin['remarkon'] != '正常' || $kaoqin['remarkoff'] != '正常'):
                            $value['month'][$k]['leaveF'] = 1; // 不正常上班
                            $leaveTimes++; // 不正常上班天数
                            // 是否请假
                            unset( $condition['day']);
                            $condition['leavetime'] = date('Y-m-d', $i);
                            $value['month'][$k]['leave'] = TeacherLeaveModel::model()
                                ->getDb()
                                ->where($condition)
                                -> queryAll();

                        endif;
                    else:
                        // 未上班
                        $value['month'][$k]['leaveF'] = 2;
                    endif;

                endif;
                $k = $k+1;
            endfor;
            $value['leaveTimes'] = $leaveTimes; // 不正常上班天数
            $value['workDays'] = $workDays; // 应该工作的天数
            $value['workingDays'] = $workingDays; // 正常上班天数
        endforeach;


        $pagelist = $page ->fpage();

        // 所有的教师(非离职)
        $teacherCondition = array(
            'isonjob != ' => 3,
            'sid' => arCfg('current_member.sid'),
        );


        $teacher = U_company_school_member_recordModel::model()
            ->getDb()
            ->select('mid, realname')
            ->where($teacherCondition)
            ->queryAll();
        // 星期几
        $whatDay = TeacherAttendanceModel::model()
            ->whatDay($start, $end);

        // 号数
        $howDays = TeacherAttendanceModel::model()
            ->howDays($start, $end);
        $this -> assign(array(
            'howDays' => $howDays,
            'list' => $list,
            'whatDay' => $whatDay,
            'teacher' => $teacher,
            'pagelist' => $pagelist
        ));
		$this->display('');

	}

    // 考勤查看
    public function attendanceAction()
    {
        if (arPost()):
            $data = array_map('trim', arPost());
            if ($rid = arRequest('aid')) :
                // 检查是否为园长
                if (!arModule('Lib.Auth')->isYz()) :
                    $this->redirectError('attendance', '你没有权限');
                else:
                    // 写入数据库 edit
                    if ($data['editon'] == 1) :
                        $data['on'] = strtotime($data['day'] . '09:00:00');
                        $data['remarkon'] = '正常';
                        $data['onreason'] = '';
                    endif;

                    if ($data['editoff'] == 1) :
                        $data['off'] = strtotime($data['day'] . '18:00:00');
                        $data['remarkoff'] = '正常';
                        $data['offreason'] = '';
                        $data['overtime'] = '';
                    endif;

                    $insertResult = TeacherAttendanceModel::model()
                        ->getDb()
                        ->where(array('aid' => $rid))
                        ->update($data, 1);
                    if ($insertResult):
                        $this->redirectSuccess();
                    else:
                        $this->redirectError();
                    endif;
                endif;
            else :
                //var_dump($data);
                $kaoqin = TeacherAttendanceModel::model()
                    ->kaoqin($data);
                switch ($kaoqin) {
                    case '1':
                        $this->redirectSuccess();
                        break;

                    case '2':
                        $this->redirectError('Office/attendance', '下班卡已打！');
                        break;

                    case '3':
                        $this->redirectError('Office/attendance', '上班卡已打！');
                        break;

                    case '4':
                        $this->redirectError('Office/attendance', '今天考勤已记录！');
                        break;

                    default:
                        $this->redirectError('Office/attendance', '数据有误！');
                        break;
                }
            endif;

        endif;

        // 搜索条件
        $condition = array(
        );
        $condition['sid'] = arCfg('current_member.sid');

        if (arRequest('time1') && arRequest('time2')) :
            $star = arRequest('time1');
            $end = arRequest('time2');
            $condition[] = ' day between "'. $star . '" and "' . $end .'"' ;
        elseif(!arRequest('time1') && arRequest('time2')):
            $condition['day'] = arRequest('time2') ;
        elseif(arRequest('time1') && !arRequest('time2')):
            $condition['day'] = arRequest('time1') ;
        elseif(!arRequest('time1') && !arRequest('time2')):
            // 当前月
            $month = date('Y-m-01', time());
            $star = date('Y-m-d', strtotime('-1 day', strtotime($month)));
            $end = date('Y-m-d', strtotime('+1 month', strtotime($month)));
            $condition[] = ' day between "'. $star . '" and "' . $end .'"' ;
        endif;

        // 检查是否为园长
        if (!arModule('Lib.Auth')->isYz()) :
            $condition['mid'] = arCfg('current_member.mid');
        else:
            if ($account_name = urldecode(arRequest('mid'))) :
                $condition['mid'] = $account_name;
            endif;
        endif;
        // 分页计算
        $total = TeacherAttendanceModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $per = 10;
        $page = new Pageone($total, $per);

        // 获取数据
        $list = TeacherAttendanceModel::model()
            ->getDb()
            ->where($condition)
            ->order('aid desc')
            ->limit($page->limit())
            ->queryAll();

        $list = TeacherAttendanceModel::model() -> getDetailInfo($list);
        $pagelist = $page ->fpage();
        // 所有的教师(非离职)
        $teacherCondition = array(
            'isonjob != ' => 3,
            'sid' => arCfg('current_member.sid'),
        );

        $teacher = U_company_school_member_recordModel::model()
            ->getDb()
            ->select('mid, realname')
            ->where($teacherCondition)
            ->queryAll();
        // echo '<pre>';
        // var_dump($condition);
        $this -> assign(array('list' => $list, 'teacher' => $teacher, 'pagelist' => $pagelist ));
        $this->display();
    }

    // 删除考勤
    public function delAttendanceAction()
    {
        if ($rid = arRequest('aid')) :

            $delResult = TeacherAttendanceModel::model()
                ->getDb()
                ->where(array('aid' => $rid))
                ->delete();
            if ($delResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError('删除失败');
            endif;

        else :
            $this->showJsonError('参数错误');
        endif;
    }

    public function notepadAction()
    {
        $year=(!isset($_GET['year'])||$_GET['year']=="")?date("Y"):$_GET['year']; 
        $month=(!isset($_GET['month'])||$_GET['month']=="")?date("n"):$_GET['month'];
        $this->assign(array(
        )); 
        $this->display();
    }

	// // 园务管理
	// public function parkManageAction()
	// {
	// 	$this->display('');
	// }
	// // 维修记录
	// public function maintenanceRecordsAction()
	// {
	// 	$this->display('');
	// }

}
