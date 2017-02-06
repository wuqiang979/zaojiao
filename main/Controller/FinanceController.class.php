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

class FinanceController extends BaseController
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
            '收费登记' => arU('registerFee'),
            '收费管理' => arU('manageFees'),
            '到期提醒' => arU('expirationReminder'),
            '退费登记' => arU('refundRegister'),
            '退费管理' => arU('refundManage'),
            // '出勤费用结算' => arU('feeSettlement'),
            // '出勤结算管理' => arU('billingManage'),
            // '出勤结算记录' => arU('billingRecords')
        );
        $this->assign(array('headnavs' => $headnavs));
    }

	// 到期提醒
	public function expirationReminderAction()
	{
		// 搜索条件
        $condition = array(
        );
        $condition['sid'] = arCfg('current_member.sid');
        // 到期日期
        if (arRequest('charge1') && arRequest('charge2')) :
        	$start = urldecode(arRequest('charge1'));
        	$end = urldecode(arRequest('charge2'));
        	$condition[] = ' daydue between "'. $start .'" and "' . $end .'"';
        elseif(!arRequest('charge1') && arRequest('charge2')):
        	$condition['daydue'] = arRequest('charge2');
        elseif(arRequest('charge1') && !arRequest('charge2')):
        	$condition['daydue'] = arRequest('charge1');
        elseif(!arRequest('charge1') && !arRequest('charge2')):
        	# 默认到期日期为当天
        	// $condition['daydue'] = date('Y-m-d', time());
        	# 默认是当月
        	$condition['daydue like '] = '%' . date('Y-m', time()) . '%';
        endif;
        // // 单据号
        // if ($account_name = urldecode(arRequest('serialnumber'))) :
        // 	$condition['serialnumber'] = $account_name;
        // endif;
        // // 收费月份
        // if ($account_name = urldecode(arRequest('chargeYmd'))) :
        // 	$condition['chargeday like '] = '%' . $account_name . '%';
        // else:
        // 	$condition['chargeday like '] = '%' . date('Y-m', time()) . '%';
        // endif;
        // 幼儿姓名
        if ($account_name = urldecode(arRequest('babyname'))) :
        	$babyContidion = array(
        		'sid' => arCfg('current_member.sid'),
        		'name like ' => '%' . $account_name . '%'
        	);
        	$baby = BabyModel::model()
				->getDb()
				->select('bid, name')
				->where($babyContidion)
				->queryAll();
			$name = array();
			foreach ($baby as $key => $value) {
				$name[] = $value['bid'];
			}
			$nameids = implode(',', $name);
	        $condition[] =  'bid in (' . $nameids . ')';
        endif; 
        // 状态
        if ($account_name = arRequest('status')) :
            $condition['status'] = $account_name;
        endif; 

        // 分页计算
        $total = BabyChargeModel::model()->getDb()
            ->where($condition)
            ->count();
        $per = 10;
        $page = new Pageone($total, $per);

		$charge = BabyChargeModel::model()->getDb()
			->where($condition)
			->order('ccid desc')
            ->limit($page->limit())
			->queryAll();
		$pagelist = $page ->fpage();
		$charge = BabyChargeModel::model()->getDetailInfo($charge);
		// echo '<pre>';
		// var_dump($condition);
		// 收费模式
		$chargeway = BabyChargeModel::$CHARGEWAY_MAP;
		// 收费类型
		$chargetype = BabyChargeModel::$CHARGETYPE_MAP;
		// 学年信息
		$schoolYear = EduSchoolYearModel::model()
			->getDb()
			->where(array('sid' => arCfg('current_member.sid'), 'status' => EduSchoolYearModel::STATUS_APPROVED))
			->queryAll();
		$schoolYear = EduSchoolYearModel::model()->getDetailInfo($schoolYear);
		// 学生
		$choseBabyCondition = array(
			'sid' => arCfg('current_member.sid'),
		);
		$choseBaby = BabyModel::model()
			->getDb()
			->select('bid, name')
			->where($choseBabyCondition)
			->queryAll();

		$this->assign(array(
			'choseBaby' => $choseBaby,
			'charge' => $charge,
			'pagelist' => $pagelist,
			'schoolYear' => $schoolYear,
			'chargeway' => $chargeway,
			'chargetype' => $chargetype
		));
		$this->display('');
	}

	// 收费登记
	public function registerFeeAction()
	{
		if (arPost()) :
			$data = array_map('trim', arPost());
			// 查询银行卡
			if (isset($data['sign']) && $data['sign'] == 'choseBaby'):
				$backCard = BabyModel::model()
					->getDb()
					->where(array('bid' => $data['bid']))
					->queryRow();
				$backCards = array(
					$backCard['back_card1'],
					$backCard['back_card2']
				);
				echo  json_encode($backCards);
				exit;
			endif;

			// 收费登记 收费的计算
			$data['sid'] = arCfg('current_member.sid');
			$data['status'] = 1;
			$actualchargesCheck = $data['shouldcharges'] * $data['discount'];
			if($actualchargesCheck != $data['actualcharges']):
				$data['actualcharges'] = $actualchargesCheck;
			endif;
			// 代扣
			if(isset($data['withholdingsingle']) && $data['withholdingsingle'] == 1):
				# 代扣处理
			endif;
			// 定金
			if(isset($data['earnest']) && $data['earnest'] == '1'):
				# 定金处理
			endif;
			// 收费类型
			if ($data['chargetype'] == 1):
				unset($data['chargecard']);
			endif;
			// 收费模式
			if ($data['chargeway'] == 1):
				unset($data['studyyear']);
			endif;
			$data['operator'] = arCfg('current_member.mid');

			// 记录到数据库
			$insertRes = BabyChargeModel::model()
				->getDb()
				->insert($data, 1);
			if ($insertRes):
				$this->redirectSuccess('registerFee', '操作成功！', '2');
			else:
				$this->redirectError('registerFee', '操作失败！', '2');
			endif;


		endif;

		// 学年信息
		$schoolYear = EduSchoolYearModel::model()
			->getDb()
			->where(array('sid' => arCfg('current_member.sid'), 'status' => EduSchoolYearModel::STATUS_APPROVED))
			->queryAll();
		$schoolYear = EduSchoolYearModel::model()->getDetailInfo($schoolYear);
		// 学生
		$choseBabyCondition = array(
			'sid' => arCfg('current_member.sid'),
		);
		$choseBaby = BabyModel::model()
			->getDb()
			->select('bid, name')
			->where($choseBabyCondition)
			->queryAll();
		// 登录人员的 realname
		$realname = arCfg('current_member.realname');
		$this->assign(array(
			'choseBaby' => $choseBaby,
			'schoolYear' => $schoolYear,
			'realname' => $realname
		));
		$this->display('');
	}

    // 收费管理
	public function manageFeesAction()
	{
		if (arPost()) :
            $data = array_map('trim', arPost());
        	// 查询银行卡
			if (isset($data['sign']) && $data['sign'] == 'backCards'):
				$backCard = BabyModel::model()
					->getDb()
					->where(array('bid' => $data['bid']))
					->queryRow();
				$backCards = array(
					$backCard['back_card1'],
					$backCard['back_card2']
				);
				echo  json_encode($backCards);
				exit;
			endif;
        
            if ($rid = arRequest('ccid')) :
                // 写入数据库 edit
                $actualchargesCheck = $data['shouldcharges'] * $data['discount'];
				if($actualchargesCheck != $data['actualcharges']):
					$data['actualcharges'] = $actualchargesCheck;
				endif;
				// 代扣
				if(isset($data['withholdingsingle']) && $data['withholdingsingle'] == 1):
					# 代扣处理
				endif;
				// 定金
				if(isset($data['earnest']) && $data['earnest'] == 1):
					# 定金处理
				endif;
				// 收费模式
				if ($data['chargeway'] == 1):
					$data['studyyear'] = '';
				endif;
				// 收费类型
				if($data['chargetype'] == 1):
					$data['chargecard'] = '';
				endif;

                $insertResult = BabyChargeModel::model()
                    ->getDb()
                    ->where(array('ccid' => $rid))
                    ->update($data, 1);
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
        // 收费日期
        if (arRequest('charge1') && arRequest('charge2')) :
        	$start = urldecode(arRequest('charge1'));
        	$end = urldecode(arRequest('charge2'));
        	$condition[] = ' chargeday between "'. $start .'" and "' . $end .'"';
        elseif(!arRequest('charge1') && arRequest('charge2')):
        	$condition['chargeday'] = arRequest('charge2');
        elseif(arRequest('charge1') && !arRequest('charge2')):
        	$condition['chargeday'] = arRequest('charge1');
        // elseif(!arRequest('charge1') && !arRequest('charge2')):

        endif;
        // 单据号
        if ($account_name = urldecode(arRequest('serialnumber'))) :
        	$condition['serialnumber'] = $account_name;
        endif;
        // 收费月份
        if ($account_name = urldecode(arRequest('chargeYmd'))) :
        	$condition['chargeday like '] = '%' . $account_name . '%';
        else:
        	$condition['chargeday like '] = '%' . date('Y-m', time()) . '%';
        endif;
        // 幼儿姓名
        if ($account_name = urldecode(arRequest('babyname'))) :
        	$babyContidion = array(
        		'sid' => arCfg('current_member.sid'),
        		'name like ' => '%' . $account_name . '%'
        	);
        	$baby = BabyModel::model()
				->getDb()
				->select('bid, name')
				->where($babyContidion)
				->queryAll();
			$name = array();
			foreach ($baby as $key => $value) {
				$name[] = $value['bid'];
			}
			$nameids = implode(',', $name);
	        $condition[] =  'bid in (' . $nameids . ')';
        endif; 
        // 状态
        if ($account_name = arRequest('status')) :
            $condition['status'] = $account_name;
        endif; 

        // 分页计算
        $total = BabyChargeModel::model()->getDb()
            ->where($condition)
            ->count();
        $per = 10;
        $page = new Pageone($total, $per);

		$charge = BabyChargeModel::model()->getDb()
			->where($condition)
			->order('ccid desc')
            ->limit($page->limit())
			->queryAll();
		$pagelist = $page ->fpage();
		$charge = BabyChargeModel::model()->getDetailInfo($charge);
		
		// 学年信息
		$schoolYear = EduSchoolYearModel::model()
			->getDb()
			->where(array('sid' => arCfg('current_member.sid'), 'status' => EduSchoolYearModel::STATUS_APPROVED))
			->queryAll();
		$schoolYear = EduSchoolYearModel::model()->getDetailInfo($schoolYear);
		// 学生
		$choseBabyCondition = array(
			'sid' => arCfg('current_member.sid'),
		);
		$choseBaby = BabyModel::model()
			->getDb()
			->select('bid, name')
			->where($choseBabyCondition)
			->queryAll();

		$this->assign(array(
			'choseBaby' => $choseBaby,
			'charge' => $charge,
			'pagelist' => $pagelist,
			'schoolYear' => $schoolYear
		));
		$this->display();
	}

	// 审核
	public function shenheAction()
	{
		if ($nid = arRequest('ccid')) :
			// 收费管理之审核
            $shenheResult = BabyChargeModel::model()
                ->getDb()
                ->where(array('ccid' => $nid))
                ->update(array('status' => arRequest('status')));
        elseif($nid = arRequest('rcid')):
        	// 退费管理之审核
            $shenheResult = BabyReturnChargeModel::model()
                ->getDb()
                ->where(array('rcid' => $nid))
                ->update(array('status' => arRequest('status')));
        else :
            $this->redirectError(arRequest('url'), '参数错误');
        endif;

        if ($shenheResult) :
            $this->redirectSuccess(arRequest('url'));
        else :
            $this->redirectError(arRequest('url'));
        endif;
	}
	// 删除
	public function delAction()
	{
		if ($nid = arRequest('ccid')) :
			// 收费管理之删除
            $delResult = BabyChargeModel::model()
                ->getDb()
                ->where(array('ccid' => $nid))
                ->delete();
        elseif($nid = arRequest('rcid')) :
        	// 退费管理之删除
            $delResult = BabyReturnChargeModel::model()
                ->getDb()
                ->where(array('rcid' => $nid))
                ->delete();
        else :
                $this->showJsonError('参数错误');
        endif;

        if ($delResult) :
        	$this->showJsonSuccess();
        else :
            $this->showJsonError('删除失败');
        endif;
	}
    // 收费管理历史
	public function historyRecordAction()
	{
		$this->display('');
	}
    // 退费登记
	public function refundRegisterAction()
	{
		if (arPost()) :
			$data = array_map('trim', arPost());
			// 查询银行卡
			if (isset($data['sign']) && $data['sign'] == 'choseBaby'):
				$backCard = BabyModel::model()
					->getDb()
					->where(array('bid' => $data['bid']))
					->queryRow();
				$backCards = array(
					$backCard['back_card1'],
					$backCard['back_card2']
				);
				echo  json_encode($backCards);
				exit;
			endif;

			$data['operator'] = arCfg('current_member.mid');
			$data['sid'] = arCfg('current_member.sid');
			$data['status'] = 1;
			if($data['returntype'] == 1): // 现金退费
				unset($data['backCard']);
			endif;

			if($data['returnway'] == 1): // 按次退费
				unset($data['studyyear']);
			endif;

			// 记录到数据库
			$insertRes = BabyReturnChargeModel::model()
				->getDb()
				->insert($data, 1);
			if ($insertRes):
				$this->redirectSuccess();
			else:
				$this->redirectError();
			endif;

		endif;

		// 学年信息
		$schoolYear = EduSchoolYearModel::model()
			->getDb()
			->where(array('sid' => arCfg('current_member.sid'), 'status' => EduSchoolYearModel::STATUS_APPROVED))
			->queryAll();
		$schoolYear = EduSchoolYearModel::model()->getDetailInfo($schoolYear);
		// 学生
		$choseBabyCondition = array(
			'sid' => arCfg('current_member.sid'),
		);
		$choseBaby = BabyModel::model()
			->getDb()
			->select('bid, name')
			->where($choseBabyCondition)
			->queryAll();
		// 登录人员的 realname
		$realname = arCfg('current_member.realname');
		$this->assign(array(
			'choseBaby' => $choseBaby,
			'schoolYear' => $schoolYear,
			'realname' => $realname
		));
		$this->display('');
	}
    // 退费管理
	public function refundManageAction()
	{
		if (arPost()) :
            $data = array_map('trim', arPost());
        	// 查询银行卡
			if (isset($data['sign']) && $data['sign'] == 'backCards'):
				$backCard = BabyModel::model()
					->getDb()
					->where(array('bid' => $data['bid']))
					->queryRow();
				$backCards = array(
					$backCard['back_card1'],
					$backCard['back_card2']
				);
				echo  json_encode($backCards);
				exit;
			endif;
        	// 修改
            if ($rid = arRequest('rcid')) :
                // 写入数据库 edit
				// 收费模式
				if ($data['returnway'] == 1):
					$data['studyyear'] = '';
				endif;
				// 收费类型
				if($data['returntype'] == 1):
					$data['backcard'] = '';
				endif;

                $insertResult = BabyReturnChargeModel::model()
                    ->getDb()
                    ->where(array('rcid' => $rid))
                    ->update($data, 1);
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
        // 退费日期
        if (arRequest('charge1') && arRequest('charge2')) :
        	$start = urldecode(arRequest('charge1'));
        	$end = urldecode(arRequest('charge2'));
        	$condition[] = ' returnday between "'. $start .'" and "' . $end .'"';
        elseif(!arRequest('charge1') && arRequest('charge2')):
        	$condition['returnday'] = arRequest('charge2');
        elseif(arRequest('charge1') && !arRequest('charge2')):
        	$condition['returnday'] = arRequest('charge1');
        // elseif(!arRequest('charge1') && !arRequest('charge2')):

        endif;
        // 单据号
        if ($account_name = urldecode(arRequest('serialnumber'))) :
        	$condition['serialnumber'] = $account_name;
        endif;
        // 退费月份
        if ($account_name = urldecode(arRequest('returnYmd'))) :
        	$condition['returnday like '] = '%' . $account_name . '%';
        else:
        	$condition['returnday like '] = '%' . date('Y-m', time()) . '%';
        endif;
        // 幼儿姓名
        if ($account_name = urldecode(arRequest('babyname'))) :
        	$babyContidion = array(
        		'sid' => arCfg('current_member.sid'),
        		'name like ' => '%' . $account_name . '%'
        	);
        	$baby = BabyModel::model()
				->getDb()
				->select('bid, name')
				->where($babyContidion)
				->queryAll();
			$name = array();
			foreach ($baby as $key => $value) {
				$name[] = $value['bid'];
			}
			$nameids = implode(',', $name);
	        $condition[] =  'bid in (' . $nameids . ')';
        endif; 
        // 状态
        if ($account_name = arRequest('status')) :
            $condition['status'] = $account_name;
        endif; 

        // 分页计算
        $total = BabyReturnChargeModel::model()->getDb()
            ->where($condition)
            ->count();
        $per = 10;
        $page = new Pageone($total, $per);

		$charge = BabyReturnChargeModel::model()->getDb()
			->where($condition)
			->order('rcid desc')
            ->limit($page->limit())
			->queryAll();
		$pagelist = $page ->fpage();
		$charge = BabyReturnChargeModel::model()->getDetailInfo($charge);
		
		// 学年信息
		$schoolYear = EduSchoolYearModel::model()
			->getDb()
			->where(array('sid' => arCfg('current_member.sid'), 'status' => EduSchoolYearModel::STATUS_APPROVED))
			->queryAll();
		$schoolYear = EduSchoolYearModel::model()->getDetailInfo($schoolYear);
		// 学生
		$choseBabyCondition = array(
			'sid' => arCfg('current_member.sid'),
		);
		$choseBaby = BabyModel::model()
			->getDb()
			->select('bid, name')
			->where($choseBabyCondition)
			->queryAll();

		$this->assign(array(
			'choseBaby' => $choseBaby,
			'charge' => $charge,
			'pagelist' => $pagelist,
			'schoolYear' => $schoolYear
		));
		$this->display('');
	}
	//出勤结算管理
	public function billingManageAction()
	{
		$this->display('');
	}
	//出勤结算记录
	public function billingRecordsAction()
	{
		$this->display('');
	}
	//出勤费用结算
	public function feeSettlementAction()
	{
		$this->display('');
	}

}
