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

class SenateController extends BaseController
{
	public function init()
    {
        parent::init();
        // 设置布局文件
        $this->setLayoutFile('content');

        $headnavs = array(
            '学年管理	' => arU('Senate/schoolYear'),
            '班级管理' => arU('Senate/class'),
            '班级名单' => arU('Senate/classList'),
            '转班管理' => arU('Senate/changeClass'),
            // '升级管理' => arU('Senate/upgradeManage'),
            '班级升级' => arU('Senate/classUpgrade'),
            '班级毕业' => arU('Senate/classGraduation'),
            '幼儿请假管理' => arU('Senate/childLeaveManage'),
            '考勤明细查看' => arU('Senate/attendanceView'),
            '幼儿考勤' => arU('Senate/attendanceStatistics'),
        );
        $this->assign(array('headnavs' => $headnavs));

    }
	// 学年管理
	public function schoolYearAction()
	{
		// 获取session
        $session = arComp('list.session')->get('member');

		// 初始化
		$sid = $session['sid'];
		$eno = null; //错误号
		$res = array();//返回值
		$arr = array();//数组列表
		$condition = array();//条件数组
		$arr = arPost();

		// 添加修改
		if(isset($arr['type'])):
			$type= $arr['type'];
			unset($arr['type']);
			if($type=='add'):
				$arr['sid'] = $sid;//追加机构ID
				$arr['create_time'] = time();//追加创建时间
				$eno = EduSchoolYearModel::model()->getDb()
					->insert($arr,true);
			elseif($type=='modify'):
				// var_dump($arr);
				$arrw = array('yid'=>$arr['yid']);
				$eno = EduSchoolYearModel::model()->getDb()
					->where($arrw)
					->update($arr,true);
			endif;
			if($eno):
				$this->redirectSuccess();
			else:
				$this->redirectError();
			endif;
		endif;
		// 搜索条件
		$condition = array('sid'=>$arr['sid']=$sid);

		// 分页处理
		$count = EduSchoolYearModel::model()->getDb()
			->where($condition)
			->count();
		$page = new Page($count,10);

		// 获取数据
		$res = EduSchoolYearModel::model()->getDb()
			->where($condition)
			->order('yid desc')
			->limit($page->limit())
			->queryAll();

		// 获取关联数据

		// 分配模板
		$this->assign(array('res'=>$res,'page'=>$page));

		$this->display('');
	}

	// 删除学年
    public function schoolYearDelAction()
    {
        if ($yid = arRequest('yid')) :
            $delResult = EduSchoolYearModel::model()
                ->getDb()
                ->where(array('yid' => $yid))
                ->delete();
            if ($delResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError('');
            endif;
        else :
            $this->showJsonError('参数错误');
        endif;
    }

	// 班级管理
	public function classAction()
	{
		// 获取session
        $session = arComp('list.session')->get('member');
		$sid = $session['sid'];
		// echo("<pre>");
		// 初始化
		$data = arPost();
		$b = null;

		// 添加修改
		if (isset($data['type'])) :
            if ($data['type']=='modify'):
            	$cid = $data['cid'];
            	unset($data['type']);
            	unset($data['cid']);
                // 修改数据库
                $eno = EduClassroomModel::model()
                    ->getDb()
                    ->where(array('cid' => $cid))
                    ->update($data, true);
                $eno1 = EduClassListModel::model()
                    ->getDb()
                    ->where(array('cid' => $cid))
                    ->update(array('status'=>$data['status']));
                if(isset($data['status'])):
                	$arru = array('status'=>$data['status']);
                	$eno1 = EduClassListModel::model()
                		->getDb()
                		->where(array('cid'=>$cid))
                		->update($arru,true);
                endif;
            elseif($data['type']=='add') :
                $data['created_time'] = time();
                $data['sid'] = $sid;
            	unset($data['type']);
            	// var_dump($data);
                // 添加班级基本信息
                $eno = EduClassroomModel::model()->getDb()->insert($data,true);
                // 添加班级名单列表信息
                if($eno):
                	$oneClass = EduClassroomModel::model()->getDb()->order('cid desc')->queryRow();
                	$arr = array(
                		'sid'=>$sid,
                		'cid'=>$oneClass['cid'],
                		);
                	EduClassListModel::model()->getDb()->insert($arr,true);
                endif;
            endif;
            if ($eno) :
                $this->redirectSuccess();
            else :
                $this->redirectError();
            endif;
        endif;

		// 搜索条件
		$condition = array('sid'=>$sid);

		// 分页计算
		$total = EduClassroomModel::model()->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);

		// 获取数据
		$res = EduClassroomModel::model()->getDb()
			->where($condition)
			->order('cid desc')
			->limit($page->limit())
			->queryAll();
		// 获取关联数据
		$res = EduClassroomModel::model()->getDetailInfo($res);

		// 获取教师列表数据
		$classTeacher = EduClassTeacherModel::model()
			->getDb()
			->where(array('sid'=>$sid))
			->queryAll();
		$classTeacher = EduClassTeacherModel::model()->getDetailInfo($classTeacher);

		// 获取学年信息
		$schoolYear = EduSchoolYearModel::model()->getSchoolYearSelect($sid);
		// var_dump($schoolYear);

		// 分配模板
		$this->assign(array('res'=>$res,'page'=>$page,'teacherList'=>$classTeacher,'schoolYear'=>$schoolYear));

		// echo("<pre>");
		// // print_r($classTeacher);
		// var_dump(arPost());
		// echo("</pre>");
		$this->display('');
	}

	// 删除班级 以及班级名单列表
    public function classDelAction()
    {
        if ($cid = arRequest('cid')) :
            $delResult = EduClassroomModel::model()
                ->getDb()
                ->where(array('cid' => $cid))
                ->delete();
            $delClassList = EduClassListModel::model()
                ->getDb()
                ->where(array('cid' => $cid))
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

	// 班级名单
	public function classListAction()
	{
		// 获取session
        $session = arComp('list.session')->get('member');
		// 初始化
		$class = null;
		$res = array();
		$page = null;
		$sid = $session['sid'];
		$data = arPost();
		$data = array_filter($data);
		// print_r($data);

		// 添加修改
		if(isset($data['type'])):
			// 向班级列表中添加学生
			if($data['type']=='add'):
				$classList = EduClassListModel::model()->getDb()
					->where(array('cid'=>$data['cid']))
					->queryRow();
				if(!empty($classList['bids'])):
					$classBids = $classList['bids'].",".$data['bid'];
				else:
					$classBids = $data['bid'];
				endif;
				unset($data['type']);
				$eno = EduClassListModel::model()
					->getDb()
					->where(array('cid'=>$data['cid']))
					->update(array('bids'=>$classBids));
			endif;
			if($eno):
				$this->redirectSuccess();
			else:
				$this->redirectError();
			endif;
		endif;

		if($cid = arRequest('cid')):
			// 搜索条件
			$condition = array(
				'sid'=>$sid,
				'cid'=>$cid,
				'class_type'=>arRequest('class_type'),
				);
			$condition = array_filter($condition);

			// 分页计算
			$total = EduClassListModel::model()->getDb()
	            ->where($condition)
	            ->count();
	        $page = new Page($total, 10);


			// 获取数据
			$res = EduClassListModel::model()
				->getDb()
				->where($condition)
				->queryAll();

			// 获取班级信息
			$class = EduClassroomModel::model()->getDb()
				->where(array('cid'=>$res[0]['cid']))
				->queryRow();
			$class = EduClassroomModel::model()->getDetailInfo($class);

			// 获取关联数据-学生信息及学生家长信息
			$res = EduClassListModel::model()->getDetailInfo($res);

		endif;

		//获取班级名称select列表
		$clsselect = EduClassroomModel::model()->getDb()
			->where(array('sid'=>$sid,'status'=>1))
			->order('class_type desc,cid asc')
			->queryAll();
		$clsselect = EduClassroomModel::model()->getClassNameSelect($clsselect);


		// 分配模板
		$this->assign(array('res'=>$res,'page'=>$page,'clsselect'=>$clsselect,'class'=>$class));
		$this->display('');
	}

	// 删除班级名单中的学员
    public function classListDelAction()
    {
    	if (($cid = arPost('cid'))&&($bid = arPost('bid'))) :
    		$classList = EduClassListModel::model()->getDb()
    			->where(array('cid'=>$cid))
    			->queryRow();
    		$classList = explode(',', $classList['bids']);
    		foreach($classList as $k=>$v):
    			if($v==$bid):
    				unset($classList[$k]);
    			endif;
    		endforeach;
    		$classList = implode(',',$classList);

            $delResult = EduClassListModel::model()
                ->getDb()
                ->where(array('cid' => $cid))
                ->update(array('bids'=>$classList));

            if ($delResult) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError();
            endif;
        else :
            $this->showJsonError('参数错误');
        endif;
    }

	// 幼儿请假管理
	public function childLeaveManageAction()
	{
		// 初始化
		$pa = arPost();
		$session = arComp('list.session')->get('member');
		$sid = $session['sid'];
		if(arGet())://处理URL
			$cond[' bname like '] = '%'.urldecode(arGet('bname')).'%';
			$cond['leave_start_time'] = strtotime(arGet('leave_time'));
			$cond['sid'] = $sid;
			$cond = array_filter($cond);
		endif;
		if(arPost()):
			$data = array_filter(arPost());
			$data['leave_start_time'] = strtotime($data['leave_start_time']);
			$data['leave_end_time'] = strtotime($data['leave_end_time']);
			$data['leave_days'] = $data['leave_end_time']-$data['leave_start_time'];
			$data['leave_days'] = date('d',$data['leave_days']);
			$data['sid'] = $sid;
			$data['created_time'] = time();
		endif;
		$res = array();

		// 添加修改
		if(arPost()):
			// 修改
			if(isset($data['blid'])&&$blid=$data['blid']):
				unset($data['blid']);
				$eno = BabyLeaveModel::model()->getDb()
					->where(array('blid'=>$blid))
					->update($data,true);
			// 添加
			else:
				$bname = BabyModel::model()
					->getDb()
					->where(array('bid'=>$data['bid']))
					->queryRow();
				$data['bname'] = $bname['name'];

				$eno = BabyLeaveModel::model()->getDb()
					->insert($data,true);
			endif;
			// 错误号
			if($eno):
				$this->redirectSuccess();
			else:
				$this->redirectError();
			endif;
		endif;

		// 搜索条件
		$condition = array();
		if($cond):
			$condition = $cond;
		endif;

		// 分页计算
		$total = BabyLeaveModel::model()->getDb()
	            ->where($condition)
	            ->count();
	    $page = new Page($total, 10);

		// 获取数据
		$res = BabyLeaveModel::model()->getDb()
			->where($condition)
			->order('blid desc')
			->limit($page->limit())
			->queryAll();

		// 获取班级列表
		$bb = EduClassroomModel::model()->getDb()
				->where(array('sid'=>$sid,'status'=>1))
				->order('class_type desc,cid asc')
				->queryAll();
		$classSelect = EduClassroomModel::model()->getClassNameSelect($bb);

		// 分配模板
		$this->assign(array('res'=>$res,'page'=>$page,'cs'=>$classSelect,));
		$this->display('');
	}
	// 删除幼儿请假
    public function babyLeaveDelAction()
    {
        if ($blid = arRequest('blid')) :
            $delResult = BabyLeaveModel::model()
                ->getDb()
                ->where(array('blid' => $blid))
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
	// 考勤明细查看
	public function attendanceViewAction()
	{
		// 初始化
		$session = arComp('list.session')->get('member');
		$sid = $session['sid'];

		//-----------------------------------------------
		$classid = array('cid'=>1);

		// 时间处理
		$sdate = time(); //当前时间
		if(arGet('sdate')):
			$sdate = strtotime(arGet('sdate')); //获取的时间
			$classid = array('cid'=>arGet('cid'));
		endif;
		$y = date('Y',$sdate);  //年
		$m = date('m',$sdate);	//月
		$days = cal_days_in_month(CAL_GREGORIAN, $m, $y); //每月的天数
		$date['y'] = $y;
		$date['m'] = $m;
		$date['days'] = $days;

		// 班级信息
		$classInfo = EduClassroomModel::model()
			->getDb()
			->where(array('cid'=>$classid))
			->queryRow();

		// 班级列表信息
		$classList = EduClassListModel::model()
			->getDb()
			->where($classid)
			->queryRow();

		// 获取当前班级中的幼儿bids
		$bids = explode(',', $classList['bids']);
		foreach ($bids as $k => $v):
			// 获取当前班级中的幼儿的基本信息
			$kaoqin[$k] = BabyModel::model()
				->getDb()
				->select('bid,name')
				->where(array('bid'=>$v))
				->queryRow();
			// 获取幼儿当月考勤数据
			for ($xiu=0,$workDays=0,$i=1; $i <= $date['days'] ; $i++):
				$w = date('w',mktime(0,0,0,$m,$i,$date['y']));

				// 查询条件
				$wCondition = array(
					'sid'   => $sid,
					'today' => mktime(0,0,0,$m,$i,$date['y']),
					'bid'   => $v,
					'cid'   => $classid,
				);

                if($w == 6||$w==0):
					$kaoqin[$k]['month'][$i]['status'] = '3';
					$xiu++;
				else:
					$kaoqin[$k]['month'][$i] = BabyAttendanceModel::model()
                    ->getDb()
                    ->select('status')
                    ->where($wCondition)
                    ->queryRow();
				endif;
			endfor;
			foreach ($kaoqin[$k]['month'] as $kk => $v):
				if($v['status']==1):
					$kaoqin[$k]['workDays'] = ++$workDays;
				endif;
			endforeach;
		endforeach;
		$date['xiu'] = $xiu;
		$date['trueDays'] = $date['days']-$xiu;





		// echo("<pre>");
		// var_dump($wCondition);
		// echo("<hr>");
		// var_dump($kaoqin);
		// var_dump($bids);
		// var_dump($classInfo);
		// echo("</pre>");
		//-----------------------------------------------

		// 搜索条件
		$condition = array();

		// 分页计算
		$total = BabyAttendanceModel::model()->getDb()
	 	           ->where($condition)
	 	           ->count();
	    $page = new Page($total, 10);

	    // 获取数据
	    $res = BabyAttendanceModel::model()->getDb()
	    	->queryAll();
	    // 获取关联数据 getDetailInfo
	    // $res = BabyAttendanceModel::model()->getDetailInfo($res);

		// 获取班级名称select列表
		$bb = EduClassroomModel::model()->getDb()
				->where(array('sid'=>$sid,'status'=>1))
				->order('class_type desc,cid asc')
				->queryAll();
		$classSelect = EduClassroomModel::model()->getClassNameSelect($bb);

		// 分配模板
		$this->assign(array('cls'=>$classSelect,'page'=>$page,'date'=>$date,'res'=>$res,'list'=>$kaoqin,'classInfo'=>$classInfo,));
		$this->display('');
	}

	// 考勤明细
	// public function attendanceDetailAction()
	// {
	// 	$this->display('');
	// }

	// 幼儿考勤管理
	public function attendanceStatisticsAction()
	{
		// 初始化
		$sid = arCfg('current_member.sid');
		$data = array_filter(arPost());
		$data['sid'] = $sid;
		if(isset($data['type'])):
			$type = $data['type'];
			$attendance_type = $data['attendance_type'];
			unset($data['attendance_type']);
			unset($data['type']);
		endif;

		// 打卡考勤
		if(isset($type)):
			if($type=='attendance'):
				// 数据处理
				$t = time();
				$today = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
				$data['today'] = $today;
				unset($data['name']);
				if($attendance_type=='on'):
					// 判断当前幼儿是否已经签到
					$onekaoqin = BabyAttendanceModel::model()
						->getDb()
						->where(array('today'=>$today,'bid'=>$data['bid']))
						->queryRow();
					$baid = $onekaoqin['baid'];
					// 获取该幼儿姓名
					$bname = BabyModel::model()
						->getDb()
						->where(array('bid'=>$data['bid']))
						->queryRow();
					$bname = $bname['name'];

					if(!$baid):
						//插入数据库
						$data['bname'] = $bname;
						$data['on'] = time();
						$eno = BabyAttendanceModel::model()
							->getDb()
							->insert($data,true);
					else:
						var_dump($data);
						$this->redirectError('','该幼儿已经签到！');
					endif;

				elseif($attendance_type=='off'):
					// 判断当前幼儿是否已经签退
					$onekaoqin = BabyAttendanceModel::model()
						->getDb()
						->where(array('today'=>$today,'bid'=>$data['bid']))
						->queryRow();
					$off = $onekaoqin['off'];
					if(empty($off)):
						// 修改数据库
						$data['off'] = time();
						unset($data['today']);
						$arrd = array(
							'off'	=> $data['off'],
							'status'	=> 1,
							'note'	=> $data['note'],
						);
						$arrd = array_filter($arrd);
						$eno = BabyAttendanceModel::model()
							->getDb()
							->where(array('today'=>$today,'bid'=>$data['bid']))
							->update($arrd,true);
					else:
						$this->redirectError('','已经签退！');
					endif;
				endif;
				if($eno):
					$this->redirectSuccess();
				else:
					$this->redirectError();
				endif;
			endif;
		endif;

		// 搜索条件
		$condition = array(
			'sid' => $sid,
		);
		$searchtext = urldecode(arGet('bname'));
		$condition[' bname like '] = '%'.$searchtext.'%';
		$condition['today'] = strtotime(arGet('today'));
		$condition = array_filter($condition);
		// 分页计算
		$total = BabyAttendanceModel::model()
			->getDb()
			->where($condition)
			->count();
		$page = new Page($total,10);

		// 获取数据
		$res = BabyAttendanceModel::model()
			->getDb()
			->where($condition)
			->order('baid desc')
			->queryAll();
		// 获取关联数据
		$res = BabyAttendanceModel::model()->getDetailInfo($res);

		// 获取其他数据
		// 获取班级名称select列表
		$bb = EduClassroomModel::model()->getDb()
				->where(array('sid'=>$sid,'status'=>1))
				->order('class_type desc,cid asc')
				->queryAll();
		$classSelect = EduClassroomModel::model()->getClassNameSelect($bb);

		// 分配模板
		$this->assign(array('res'=>$res,'page'=>$page,'cs'=>$classSelect));

		// 数据输出
		// echo("<pre>");
		// // var_dump($res);
		// print_r($res);
		// echo("</pre>");
		$this->display('');
	}
	// 班级出勤分析
	// public function attendanceAnalysisAction()
	// {
	// 	$this->display('');
	// }
	// 转班管理
	public function changeClassAction()
	{
		// 初始化
		$eno = null;
		$sid = arCfg('current_member.sid');
		$data = array_filter(arPost());
		if(isset($data['type']))://检测type的值
			$type = $data['type'];
			unset($data['type']);
		endif;

		// 添加修改-转班
		if(isset($type)):
			if($type=='changeClass')://转班操作
				//数据处理
				unset($data['name']);
				$data['sid'] = $sid;
				$data['change_class_time'] = strtotime($data['change_class_time']);
				$data['created_time'] = time();

				$cidb = $data['cid_change_class_before'];
				$cidl = $data['cid_change_class_later'];
				$bid = $data['bid'];
				//检测班级中是否有该学生
				$cb = EduClassListModel::model()->classListIsHaveBaby($cidb,$bid);
				$cl = EduClassListModel::model()->classListIsHaveBaby($cidl,$bid);

				// 获取该幼儿姓名
				$bname = BabyModel::model()
					->getDb()
					->select('name')
					->where(array('bid'=>$bid))
					->queryRow();
				$bname = $bname['name'];

				if(!$cl&&$cb):
					$data['bname'] = $bname;
					//插入数据到转班表
					$eno = EduChangeClassModel::model()
						->getDb()
						->insert($data,true);
					//修改转班前班级列表中的bids
					$eno1 = EduClassListModel::model()->delbid($cidb,$bid);
					//修改转班后班级列表中的bids
					$eno2 = EduClassListModel::model()->addbid($cidl,$bid);
				endif;
			endif;
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
		$searchtext = urldecode(arGet('searchtext'));
		$condition[' bname like '] = '%'.$searchtext.'%';
		$condition = array_filter($condition);

		// 分页计算
		$total = EduChangeClassModel::model()
			->getDb()
			->where($condition)
			->count();
		$page = new Page($total,10);

		// 获取数据
		$res = EduChangeClassModel::model()
			->getDb()
			->where($condition)
			->order('ccid desc')
			->limit($page->limit())
			->queryAll();

		// 获取关联数据
		$res = EduChangeClassModel::model()->getDetailInfo($res);

		// 获取其他数据
		$bb = EduClassroomModel::model()->getDb()
				->where(array('sid'=>$sid,'status'=>1))
				->order('class_type desc,cid asc')
				->queryAll();
		$classSelect = EduClassroomModel::model()->getClassNameSelect($bb);

		// 分配模板
		$this->assign(array('res'=>$res,'cs'=>$classSelect,'page'=>$page));

		// 输出数据
		// echo("<pre>");
		// // print_r($data);
		// echo("</pre>");

		$this->display('');
	}

	// 班级升级
	public function classUpgradeAction()
	{
		// 初始化
		$eno         = null;
		$sid         = arCfg('current_member.sid');
		$mid         = arCfg('current_member.mid');
		$data        = arPost();
		$data['sid'] = $sid;
		if(isset($data['type'])):
			$type = $data['type'];
			unset($data['type']);
		endif;

		// 操作
		if(isset($type)):
			// 班级升级
			if($type=='upgrade'):
				// 数据处理
				$data['upgrade_time'] = strtotime($data['upgrade_time']);
				$data['mid']          = $mid;
				$data['add_time']     = time();
				// 检测班级学生名单是否为空 并 获取班级学生名单
				$cb = EduClassListModel::model()
					->getDb()
					->select('bids')
					->where(array('cid'=>$data['cid_before']))
					->queryRow();
				$cb = $cb['bids'];

				$cl = EduClassListModel::model()
					->getDb()
					->select('bids')
					->where(array('cid'=>$data['cid_later']))
					->queryRow();
				$cl = $cl['bids'];

				// print_r($cb);
				// echo('<hr>');
				// var_dump($cl);

				// 如果原班级名单不为空 目的班级名单为空 则执行班级升级
				if(!$cl&&$cb):
					// 添加数据到班级升级表
					$eno = EduClassUpgradeModel::model()
						->getDb()
						->insert($data,true);

					// 设置原班级状态
					$eno1 = EduClassroomModel::model()
						->getDb()
						->where(array('cid'=>$data['cid_before']))
						->update(array('status'=>2));
					$eno2 = EduClassListModel::model()
						->getDb()
						->where(array('cid'=>$data['cid_before']))
						->update(array('status'=>2));

					// 添加学生名单到目的班级
					$eno3 = EduClassListModel::model()
						->getDb()
						->where(array('cid'=>$data['cid_later']))
						->update(array('bids'=>$cb));
				else:
					$this->redirectError('','原班级没有人或者目的班级中有人！');
				endif;
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
			'sid'	=> $sid,
		);

		// 分页计算
		$total = EduClassUpgradeModel::model()
			->getDb()
			->where($condition)
			->count();
		$page = new Page($total,10);

		// 获取数据
		$res = EduClassUpgradeModel::model()
			->getDb()
			->where($condition)
			->order('cuid desc')
			->limit($page->limit())
			->queryAll();

		// 获取关联数据
		$res = EduClassUpgradeModel::model()->getDetailInfo($res);

		// 获取其他数据
		// 获取班级select信息
		$cls = EduClassroomModel::model()->getDb()
				->where(array('sid'=>$sid,'status'=>1))
				->order('class_type desc,cid asc')
				->queryAll();
		$cs = EduClassroomModel::model()->getClassNameSelect($cls);

		// 分配模板
		$this->assign(array('res'=>$res,'page'=>$page,'cs'=>$cs));

		// 数据输出
		// echo('<pre>');
			// var_dump($res);
			// print_r($res);
		// echo('</pre>');

		$this->display('');
	}

	// 升级管理-班级升级
	public function upgradeManageAction()
	{
		// 初始化
		$eno = null;
		$sid = arCfg('current_member.sid');
		$d1 = $data = arPost();
		if(isset($data['type'])):
			$type = $data['type'];
			unset($data['type']);
			$data['upgrade_time'] = strtotime($data['upgrade_time']);
			$data = array_filter($data);
		endif;

		// 班级升级
		if(isset($type)):
			if($type=='upgrade'):
				// 数据处理
				$data['sid'] = $sid;
				$data['created_time'] = time();
				unset($data['name']);

				$cidb = $data['u_cid_before'];
				$cidl = $data['u_cid_later'];
				$bid = $data['bid'];
				// 检测班级列表中是否有该学生
				$cb = EduClassListModel::model()->classListIsHaveBaby($cidb,$bid);
				$cl = EduClassListModel::model()->classListIsHaveBaby($cidl,$bid);
				if($cb&&!$cl):
					// 添加数据到升级表
					$eno = EduUpgrademodel::model()
						->getDb()
						->insert($data,true);
					// 修改升级前班级列表中的bids
					$eno1 = EduClassListModel::model()->delbid($cidb,$bid);
					// 修改升级后班级列表中的bids
					$eno2 = EduClassListModel::model()->addbid($cidl,$bid);
				endif;
			endif;
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
		$condition['bid'] = arGet('searchtext');
		$condition = array_filter($condition);

		// 分页计算
		$total = EduUpgradeModel::model()
			->getDb()
			->where($condition)
			->count();
		$page = new Page($total,10);

		// 获取数据
		$res = EduUpgradeModel::model()
			->getDb()
			->where($condition)
			->order('uid desc')
			->limit($page->limit())
			->queryAll();

		// 获取关联数据
		$res = EduUpgradeModel::model()->getDetailInfo($res);

		// 获取其他数据
		// 获取班级select信息
		$cls = EduClassroomModel::model()->getDb()
				->where(array('sid'=>$sid,'status'=>1))
				->order('class_type desc,cid asc')
				->queryAll();
		$cs = EduClassroomModel::model()->getClassNameSelect($cls);

		// 分配模板
		$this->assign(array('res'=>$res,'page'=>$page,'cs'=>$cs,));

		// 数据输出
		// echo("<pre>");
		// echo("</pre>");

		$this->display('');
	}
	// 班级毕业
	public function classGraduationAction()
	{
		// 初始化
		$eno = null;
		$sid = arCfg('current_member.sid');
		$data = arPost();
		if(isset($data['type'])):
			$type = $data['type'];
			unset($data['type']);
			$data['graduation_time'] = strtotime($data['graduation_time']);
			$data = array_filter($data);
		endif;

		// 操作
		if(isset($type)):
			if($type=='graduation'):
				$data['sid']          = $sid;
				$data['mid']          = arCfg('current_member.mid');
				$data['created_time'] = time();
				var_dump($data);
				// 插入数据
				$eno = EduGraduationModel::model()
					->getDb()
					->insert($data,true);
				// 修改班级状态为3（已毕业）
				$eno1 = EduClassListModel::model()
					->getDb()
					->where(array('cid'=>$data['graduation_class_cid']))
					->update(array('status'=>3));
				$eno2 = EduClassroomModel::model()
					->getDb()
					->where(array('cid'=>$data['graduation_class_cid']))
					->update(array('status'=>3));
			endif;
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
		$condition['bid'] = arGet('searchtext');
		$condition = array_filter($condition);

		// 分页计算
		$total = EduGraduationModel::model()
			->getDb()
			->where($condition)
			->count();
		$page = new Page($total,10);

		// 获取数据
		$res = EduGraduationModel::model()
			->getDb()
			->where($condition)
			->order('gid desc')
			->limit($page->limit())
			->queryAll();

		// 获取关联数据
		$res = EduGraduationModel::model()->getDetailInfo($res);

		// 获取其他数据
		// 获取班级select信息
		$cls = EduClassroomModel::model()->getDb()
				->where(array('sid'=>$sid,'status'=>1))
				->order('class_type desc,cid asc')
				->queryAll();
		$cs = EduClassroomModel::model()->getClassNameSelect($cls);

		// 分配模板
		$this->assign(array('res'=>$res,'page'=>$page,'cs'=>$cs,));

		// 数据输出
		// echo("<pre>");
		// print_r($res);
		// echo("<hr>POST:<br>");
		// print_r(arPost());
		// echo("<hr>处理:<br>");
		// print_r($data);
		// echo("</pre>");

		$this->display('');
	}

	public function testAction()
	{
		$this->display('');
	}

	// 通过班级名称获取班级中的幼儿
	public function getbabysByCidAction()
	{
		$cid = arPost('cid');
		// 获取班级幼儿列表;
		$class = EduClassListModel::model()
			->getDb()
			->where(array('cid'=>$cid))
			->queryRow();
		$baby = explode(',', $class['bids']);
		// 获取幼儿信息
		foreach ($baby as $k=>$v):
			$baby[$k] = BabyModel::model()
				->getDb()
				->select('bid,name')
				->where(array('bid'=>$v))
				->queryRow();
		endforeach;

		echo json_encode($baby);
	}

}
