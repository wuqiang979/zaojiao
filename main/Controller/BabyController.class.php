<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author yc <ycassnr@gmail.com>
 */

/**
 * 幼儿管理类.
 */
class BabyController extends BaseController
{
	public function init()
    {
        parent::init();
        // 设置布局文件
        $this->setLayoutFile('content');
        $headnavs = array(
            '招生名单登记' => arU('babyEnrolmentAddList'),
            '招生名单管理' => arU('babyEnrolmentList'),
            '跟进记录管理' => arU('enrolmentFollowManagement'),
            '幼儿档案登记' => arU('babyAdd'),
            '幼儿档案管理' => arU('babyList'),
        );
        $this->assign(array('headnavs' => $headnavs));

    }

    // 招生名单添加
    public function babyEnrolmentAddListAction()
    {
        // 直接屏蔽NOTICE 慎用
        Ar::setConfig('DEBUG_SHOW_ERROR', false);

        if ($action = arRequest('action')) :
            switch ($action) {
                case 'show_onwindow':
                    $this->assign(array('nonav' => true));
                    break;
                case 'update_onwindow':
                    $this->assign(array('nonav' => true));
                    break;
                default:
                    break;
            }
        endif;
        if ($data = arPost()) :
            if ($eid = $data['eid']) :
                $update_condition = array(
                    'eid' => $eid,
                );
                $addResultId = BabyEnrolmentModel::model()
                    ->getDb()
                    ->where($update_condition)
                    ->update($data, true);
                BabyEnrolmentProfileModel::model()
                    ->getDb()
                    ->where($update_condition)
                    ->update($data, true);
                // 写入档案表
                for ($i = 0; $i < count($data['parent_name']); $i++) :
                    if ($data['parent_name'][$i]) :
                        $parent = array(
                            'name' => $data['parent_name'][$i],
                            'relationship' => $data['parent_relationship'][$i],
                            'phone' => $data['parent_phone'][$i],
                        );
                        if ($pid = $data['parent_pid'][$i]) :
                            // 写入家长表 批量插入batchInsert
                            BabyEnrolmentParentModel::model()
                                ->getDb()
                                ->where(array('pid' => $data['parent_pid'][$i]))
                                ->update($parent);
                        else :
                            $parent['eid'] = $eid;
                            // 写入家长表 单条插入insert
                            BabyEnrolmentParentModel::model()
                                ->getDb()
                                ->insert($parent);
                        endif;
                    endif;
                endfor;
                $this->redirectSuccess(array('', array('eid' => $eid)));
            else :
                // 初始化信息
                $data['amid'] = arCfg('current_member.mid');
                $data['sid'] = arCfg('current_member.sid');
                $data['addtime'] = time();
                $addResultId = BabyEnrolmentModel::model()->getDb()->insert($data, true);
                if ($addResultId) :
                    $data['eid'] = $addResultId;
                    // 写入档案表
                    BabyEnrolmentProfileModel::model()->getDb()->insert($data, true);
                    $parents = array();
                    for ($i = 0; $i < count($data['parent_name']); $i++) :
                        if ($data['parent_name'][$i]) :
                            $parents[] = array(
                                'name' => $data['parent_name'][$i],
                                'relationship' => $data['parent_relationship'][$i],
                                'phone' => $data['parent_phone'][$i],
                                'eid' => $addResultId,
                            );
                        endif;
                    endfor;
                    if (!empty($parents)) :
                        // 写入家长表 批量插入batchInsert
                        BabyEnrolmentParentModel::model()->getDb()->batchInsert($parents);
                    endif;

                    $this->redirectSuccess('', '添加成功');
                else :
                    $this->redirectError('', '添加失败');
                endif;
            endif;
        endif;

        if ($eid = arRequest('eid')) :
            $enrollment = BabyEnrolmentModel::model()
                ->getDb()
                ->where(array('eid' => $eid))
                ->queryRow();
            $enrollment = BabyEnrolmentModel::model()->getDetailInfo($enrollment);
            $this->assign(array('enrollment' => $enrollment));
        endif;
        
        // 学年信息
        $schoolYear = EduSchoolYearModel::model()
            ->getDb()
            ->where(array('sid' => arCfg('current_member.sid'), 'status' => EduSchoolYearModel::STATUS_APPROVED))
            ->queryAll();
        $schoolYear = EduSchoolYearModel::model()->getDetailInfo($schoolYear);

        // 教室信息
        $classRoom = EduClassroomModel::model()
            ->getDb()
            ->where(array('sid' => arCfg('current_member.sid'), 'status' => EduClassroomModel::STATUS_APPROVED))
            ->queryAll();
        $classRoom = EduClassroomModel::model()->getDetailInfo($classRoom);

        $this->assign(array('schoolYear' => $schoolYear, 'classRoom' => $classRoom));

        $this->display('');

    }

    // 招生名单
    public function babyEnrolmentListAction()
    {
        // 搜索条件
        $condition = array(
        );
        if ($searchtext = urldecode(arRequest('searchtext'))) :
            $condition['name like '] = '%' . $searchtext . '%';
        endif;

        // 分页计算
        $total = BabyEnrolmentModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 20);

        // 获取数据
        $enrollments = BabyEnrolmentModel::model()
            ->getDb()
            ->where($condition)
            ->order('eid desc')
            ->limit($page->limit())
            ->queryAll();

        // 获取关联表详细信息
        $enrolments = BabyEnrolmentModel::model()->getDetailInfo($enrollments);

        // 分配模板
        $this->assign(array('enrolments' => $enrolments, 'page' => $page));

        $this->display('');

    }

    // 删除招生信息
    public function enrolmentDelAction()
    {
        if ($eid = arRequest('eid')) :
            $delResult = BabyEnrolmentModel::model()
                ->getDb()
                ->where(array('eid' => $eid))
                ->delete();

            BabyEnrolmentProfileModel::model()
                ->getDb()
                ->where(array('eid' => $eid))
                ->delete();

            BabyEnrolmentParentModel::model()
                ->getDb()
                ->where(array('eid' => $eid))
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

    // 招生跟进信息管理
    public function enrolmentFollowManagementAction()
    {
        if ($data = arPost()) :
            if ($fid = arRequest('fid')) :
                // 写入数据库
                $insertResult = BabyEnrolmentFollowModel::model()
                    ->getDb()
                    ->where(array('fid' => $fid))
                    ->update($data, 1);
            else :
                $data['addtime'] = time();
                // 写入数据库
                $insertResult = BabyEnrolmentFollowModel::model()->getDb()->insert($data, 1);
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

        if ($searchtext = urldecode(arRequest('searchtext'))) :
             $condition['info like '] = '%' . $searchtext . '%';
        endif;

        if ($fid = arRequest('fid')) :
            $condition['fid'] = $fid;
        endif;

        // 分页计算
        $total = BabyEnrolmentFollowModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 10);

        // 获取数据
        $follows = BabyEnrolmentFollowModel::model()
            ->getDb()
            ->where($condition)
            ->order('fid desc')
            ->limit($page->limit())
            ->queryAll();

        // 获取关联表详细信息
        $follows = BabyEnrolmentFollowModel::model()->getDetailInfo($follows);

        // 分配模板
        $this->assign(array('follows' => $follows, 'page' => $page));

        $this->display('');

    }

    // 添加跟踪信息
    public function addFollowAction()
    {
        if ($eid = arRequest('followeid')) :
            $hasFollow = BabyEnrolmentFollowModel::model()->getDb()->where(array('eid' => $eid))->queryRow();
            if ($hasFollow) :
                $this->redirectError(array('enrolmentFollowManagement', array('fid' => $hasFollow['fid'])), '已添加跟踪信息');
            else :
                $follow = array(
                    'eid' => $eid,
                    'addtime' => time(),
                    'mid' => arCfg('current_member.mid'),
                );
                $follow = array_merge(arPost(), $follow);
                $fid = BabyEnrolmentFollowModel::model()->getDb()->insert($follow, true);
                $this->redirectSuccess(array('enrolmentFollowManagement', array('fid' => $fid)));
            endif;
        endif;

    }

	// 幼儿档案添加
	public function babyAddAction()
	{
		// 直接屏蔽NOTICE 慎用
		Ar::setConfig('DEBUG_SHOW_ERROR', false);

		if ($action = arRequest('action')) :
			switch ($action) {
				case 'show_onwindow':
					$this->assign(array('nonav' => true));
					break;
				case 'update_onwindow':
					$this->assign(array('nonav' => true));
					break;
				default:
					break;
			}
		endif;
		if ($data = arPost()) :
			if ($bid = $data['bid']) :
				$update_condition = array(
					'bid' => $bid,
				);
				$addResultId = BabyModel::model()
					->getDb()
					->where($update_condition)
					->update($data, true);
				BabyProfileModel::model()
					->getDb()
					->where($update_condition)
					->update($data, true);
				// 写入档案表
				for ($i = 0; $i < count($data['parent_name']); $i++) :
					if ($data['parent_name'][$i]) :
						$parent = array(
							'name' => $data['parent_name'][$i],
							'relationship' => $data['parent_relationship'][$i],
							'phone' => $data['parent_phone'][$i],
						);
						if ($pid = $data['parent_pid'][$i]) :
							// 写入家长表 批量插入batchInsert
							BabyParentModel::model()
								->getDb()
								->where(array('pid' => $data['parent_pid'][$i]))
								->update($parent);
						else :
							$parent['bid'] = $bid;
							// 写入家长表 单条插入insert
							BabyParentModel::model()
								->getDb()
								->insert($parent);
						endif;
					endif;
				endfor;
				$this->redirectSuccess(array('', array('bid' => $bid)));
			else :
				// 初始化信息
				$data['amid'] = arCfg('current_member.mid');
				$data['sid'] = arCfg('current_member.sid');
				$data['addtime'] = time();
				$addResultId = BabyModel::model()->getDb()->insert($data, true);
				if ($addResultId) :
					$data['bid'] = $addResultId;
					// 写入档案表
					BabyProfileModel::model()->getDb()->insert($data, true);
					$parents = array();
					for ($i = 0; $i < count($data['parent_name']); $i++) :
						if ($data['parent_name'][$i]) :
							$parents[] = array(
								'name' => $data['parent_name'][$i],
								'relationship' => $data['parent_relationship'][$i],
								'phone' => $data['parent_phone'][$i],
								'bid' => $addResultId,
							);
						endif;
					endfor;
					if (!empty($parents)) :
						// 写入家长表 批量插入batchInsert
						BabyParentModel::model()->getDb()->batchInsert($parents);
					endif;

					$this->redirectSuccess('', '添加成功');
				else :
					$this->redirectError('', '添加失败');
				endif;
			endif;
		endif;

		if ($bid = arRequest('bid')) :
			$enrollment = BabyModel::model()
				->getDb()
				->where(array('bid' => $bid))
				->queryRow();
			$enrollment = BabyModel::model()->getDetailInfo($enrollment);
			$this->assign(array('enrollment' => $enrollment));
		endif;

		// 学年信息
		$schoolYear = EduSchoolYearModel::model()
			->getDb()
			->where(array('sid' => arCfg('current_member.sid'), 'status' => EduSchoolYearModel::STATUS_APPROVED))
			->queryAll();
		$schoolYear = EduSchoolYearModel::model()->getDetailInfo($schoolYear);

		// 教室信息
		$classRoom = EduClassroomModel::model()
			->getDb()
			->where(array('sid' => arCfg('current_member.sid'), 'status' => EduClassroomModel::STATUS_APPROVED))
			->queryAll();
		$classRoom = EduClassroomModel::model()->getDetailInfo($classRoom);

		$this->assign(array('schoolYear' => $schoolYear, 'classRoom' => $classRoom));

		$this->display('');

	}

	// 招生名单
	public function babyListAction()
	{
        // 搜索条件
        $condition = array(
        );
        if ($searchtext = urldecode(arRequest('searchtext'))) :
            $condition['name like '] = '%' . $searchtext . '%';
        endif;

        // 分页计算
        $total = BabyModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total, 20);

        // 获取数据
        $enrollments = BabyModel::model()
            ->getDb()
            ->where($condition)
            ->order('bid desc')
            ->limit($page->limit())
            ->queryAll();

        // 获取关联表详细信息
        $enrolments = BabyModel::model()->getDetailInfo($enrollments);

        // 分配模板
        $this->assign(array('enrolments' => $enrolments, 'page' => $page));

        $this->display('');

	}

    // 删除招生信息
    public function babyDelAction()
    {
        if ($bid = arRequest('bid')) :
            $delResult = BabyModel::model()
                ->getDb()
                ->where(array('bid' => $bid))
                ->delete();

            BabyProfileModel::model()
                ->getDb()
                ->where(array('bid' => $bid))
                ->delete();

            BabyParentModel::model()
                ->getDb()
                ->where(array('bid' => $bid))
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

	// 幼儿成长评价
	public function yeczpjAction()
	{
		$this->display('yeczpj');
	}

	// 幼儿档案登记
	public function yedadjAction()
	{
			$this->display('yedadj');
	}

	// 幼儿资料存储
	public function zlccAction()
	{
		$data = arPost();
		$array = array();
		$array2 = array();
		$array3 = array();
		if($data):
			// 基本信息
			$array['name'] = $data['name'];
			$array['nickname'] = $data['nickname'];
			$array['birthday'] = $data['birthday'];
			$array['sex'] = $data['sex'];
			$array['phone'] = $data['phone'];
			$array['income'] = $data['income'];
			$array['bankcard'] = $data['bankcard'];
			$array['idcard'] = $data['idcard'];
			$array['card1'] = $data['card1'];
			$array['card2'] = $data['card2'];
			$array['householdpro'] = $data['householdpro'];
			$array['householdicity'] = $data['householdicity'];
			$array['nowprovinces'] = $data['nowprovinces'];
			$array['nowcity'] = $data['nowcity'];
			$array['nowarea'] = $data['nowarea'];
			$array['address'] = $data['address'];
			$array['level'] = $data['level'];
			$insert = JbzlccModel::model()->getDb()->insert($array,true);
			if($insert):
				$condition['name like'] = '%'.Urldecode($data['name']).'%';
				$baby = JbzlccModel::model()->getDb()->where($condition)->queryRow();
			else:
				exit('基本信息储存出错');
			endif;
			// 家长信息
			$array2['pname'] = $data['pname'];
			// $array2['if'] = $data['if'];
			$array2['tel'] = $data['tel'];
			$array2['education'] = $data['education'];
			$array2['working'] = $data['working'];
			$array2['bid'] = $baby['bid'];
			$insert2 = JzxxccModel::model()->getDb()->insert($array2,true);
			if(!$insert2):
				exit('家长信息储存出错');
			endif;
			// 高级资料
			$array3['source'] = $data['source'];
			$array3['type'] = $data['type'];
			$array3['ssemester'] = $data['ssemester'];
			$array3['sdata'] = $data['sdata'];
			$array3['esemester'] = $data['esemester'];
			$array3['class'] = $data['class'];
			$array3['ifsbus'] = $data['ifsbus'];
			$array3['health'] = $data['health'];
			$array3['allergy'] = $data['allergy'];
			$array3['interests'] = $data['interests'];
			$array3['note'] = $data['note'];
			$array3['bid'] = $baby['bid'];
			$insert3 = GjzlccModel::model()->getDb()->insert($array3,true);
			if(!$insert3):
				exit('高级资料储存出错');
			endif;
			echo '保存成功';
			$this->redirectSuccess('Baby/yedadj', '储存成功');
		endif;
	}

	// 测试
	public function testAction()
	{
		$upload = '../main/Module/upload.func.php';
		echo $upload;
	}

	// 开放日管理
	public function kfrglAction()
	{
		$this->display('kfrgl');
	}
	// 活动签到
	public function hdqdAction()
	{
		$this->display('');
	}
	// 招生名单添加
	public function babyImportListAction()
	{
		$this->display('');
	}
	// 招生名单管理
	public function enrollmentManageAction()
	{
        $list = BabyzsmdModel::model()->getDb()->queryAll();
        $this->assign(array('list' => $list));
		$this->display('');
	}
	// 跟进记录管理
	public function followRecordAction()
	{
		$this->display('');
	}

	// 照片上传
	public function zpscAction()
	{
		$fileInfo = $_FILES['imgOne'];
		// 判断错误号
		if($fileInfo['error']>0){
			switch($fileInfo['error']){
				case 1:
					$mes = '上传文件超过了PHP配置中upload_max_filesize';
					break;
				case 2:
					$mes = '超过了表单MAX_FILE_SIZE限制的大小';
					break;
				case 3:
					$mes = '部分文件被上传';
					break;
				case 4:
					$mes = '没有选择上传文件';
					break;
				case 6:
					$mes = '没有找到临时目录';
					break;
				case 7: // 文件写入失败
				case 8: // 上传文件被PHP扩展文件中断
					$mes = '系统错误';
					break;
			}
			exit($mes);
		}
		// 检测上传文件类型
		// 获取文件后缀名
		$ext = pathinfo($fileInfo['name'],PATHINFO_EXTENSION);
		// $ext = strtolower(end(explode('.',$fileInfo['name'])));
		// 匹配的文件类型
		$allowExt = array('jpeg','jpg','png','gif');
		// 判断传入的$allowExt是否为数组
		if(!is_array($allowExt)){
			exit('传入的后缀名应为数组');
		}
		if(!in_array($ext,$allowExt)){
			exit('非法文件类型');
		}

		// 检测上传文件大小是否符合规范
		$maxSize=2097152;//允许的最大值 2M 单位为字节
		if($fileInfo['size']>$maxSize){
			exit('上传文件过大');
		}

		// 检测是否为真实的图片类型
		$flag=true;
		if($flag){
			// getimagesize(); 得到指定的图片信息,如果是图片返回数组,如果不是图片,返回false
			if(!getimagesize($fileInfo['tmp_name'])){
				exit('不是真实的图片类型');
			}
		}

		// 检测文件是否是通过HTP POST方式上传上来
		if(!is_uploaded_file($fileInfo['tmp_name'])){
			exit('文件不是通过HTTP POST方式上传上来的');
		}

		// 移动文件到指定储存目录
		$uploadPath = 'Public/uploads/zp';
		// 判断移动的文件名是否存在
		if(!file_exists($uploadPath)){
			mkdir($uploadPath,0777,true);// 创建文件 0777=>可读,可写,可执行
			chmod($uploadPath,0777); // 改变权限
		}
		// 创建唯一的文件名
		$uniName=md5(uniqid(microtime(true),true)).'.'.$ext;
		$destination=$uploadPath.'/'.$uniName;
		if(!@move_uploaded_file($fileInfo['tmp_name'],$destination)){
			exit('文件移动失败');
		}

		echo '文件上传成功';
		/*return array(
			'newName' => $destination,
			'size' => $fileInfo['size'],
			'type' => $fileInfo['type'],
		);*/
		$img = '/zaojiao/'.$destination;
		echo $img;
	}

	// 幼儿性别分布
	public function babySexAction()
	{
		$this->display('@listAnalysis/babySex');
	}
	// 户籍数据统计
	public function censusRegisterAction()
	{
		$this->display('@listAnalysis/censusRegister');
	}
	// 现住址数据统计
	public function nowAddressAction()
	{
		$this->display('@listAnalysis/nowAddress');
	}
	// 幼儿年龄分布
	public function babyAgeAction()
	{
		$this->display('@listAnalysis/babyAge');
	}
	// 家庭收入情况
	public function familyIncomeAction()
	{
		$this->display('@listAnalysis/familyIncome');
	}
	// 幼儿档案登记
	public function babyFileRegisterAction()
	{
		$this->display('@babyFileRegister/babyFileRegister');
	}

	// 档案管理
	public function babyFileManageAction()
	{
		$baby = JbzlccModel::model()->getDb()->queryAll();
		$this->assign(array('baby' => $baby));
		$this->display('');
	}
	// 班级档案
	public function classFileAction()
	{
		$this->display('');
	}

}
