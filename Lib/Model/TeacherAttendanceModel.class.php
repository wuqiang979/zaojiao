<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ck <yunshaohunzhu@sina.com>
 */

/**
 * user 数据库模型.
 */
class TeacherAttendanceModel extends ArModel
{
    // 表名
    public $tableName = 'u_company_school_member_attendance';

    // 初始化model
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 打卡类型
    static $ATTENDANCETYPE_MAP = array(
         1 => '上班打卡',
         2 => '下班打卡',
    );

    // 打卡修改正常
    static $KAOQINEDIT_MAP = array(
         1 => '改为正常',
         2 => '不修改',
    );

    // 获取bundle详细信息 万能方法
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;

            // 获取成员姓名
            $bundle['mname'] = U_company_school_member_recordModel::model()
                ->getDb()
                ->select('realname')
                ->where(array('mid' => $bundle['mid']))
                ->queryRow();

            return $bundle;
        endif;

        return $bundles;

    }

    // 考勤记录
    public function kaoqin($data)
    {
        // 打卡
        $dataL = array();
        $data['sid'] = arCfg('current_member.sid');
        $data['mid'] = arCfg('current_member.mid');
        $data['day'] = date('Y-m-d', time());
        if ($data['type'] == 1) :
            $data['on'] = time();
            unset($data['overtime']);
            $stander = strtotime($data['day'] . '09:00:59');
            if($data['on'] > $stander):
                $data['remarkon'] = '迟到' . ceil(($data['on'] - $stander)/60) . '分钟';
                $data['onreason'] = $data['reason'];
                $dataL = array('leavelong' => ceil(($data['on'] - $stander)/60) . '分钟');
            else:
                $data['remarkon'] = '正常';
            endif;
        else:
            $data['off'] = time();
            $stander = strtotime($data['day'] . '18:00:00');
            if($data['off'] < $stander):
                $data['remarkoff'] = '早退' . ceil(( $stander - $data['off'])/60) . '分钟';
                $data['offreason'] = $data['reason'];
                unset($data['overtime']);
                $dataL['leavelong'] = ceil(( $stander - $data['off'])/60) . '分钟';
            else:
                $data['remarkoff'] = '正常';
                if($data['overtime']):
                    // 加班
                    $data['overtime'] = ($stander - $data['off'])/60 . '分钟';
                    # 换算成小时，>30 分钟则为一小时， < 30 分钟的不计算
                    # $data['overtime'] = round(($stander - $data['off'])/3600 ) . '小时';
                else:
                    // 没有加班
                    unset($data['overtime']);
                endif;
            endif;
        endif;
        if($data['reason']):
            // 有原因时，记录到 请假条中
            $dataL['sid'] = $data['sid'];
            $dataL['mid'] = $data['mid'];
            $dataL['leavetime'] = $data['day'];
            $dataL['leavereason'] = $data['reason'];
            $dataL['recordtime'] = time();
            TeacherLeaveModel::model()->getDb()->insert($dataL, 1);
        endif;

        $flageCondition = array(
            'sid' => arCfg('current_member.sid'),
            'mid' => arCfg('current_member.mid'),
            'day' => $data['day'],
        );
        $flage = TeacherAttendanceModel::model()
            ->getDb()
            ->where($flageCondition)
            ->queryAll();
        if(count($flage)):
            // 上班卡未打
            if(!$flage[0]['on']):
                if( !empty($data['on'])):
                    $insertResult = TeacherAttendanceModel::model()
                    ->getDb()
                    ->where(array('aid' => $flage[0]['aid']))
                    ->update($data, 1);
                    $str = 1;
                else:
                    $str = 2; // 下班卡已打！
                endif;
                
            endif;
            // 下班卡未打
            if(!$flage[0]['off']):
                if( !empty($data['off'])):
                    $insertResult = TeacherAttendanceModel::model()
                    ->getDb()
                    ->where(array('aid' => $flage[0]['aid']))
                    ->update($data, 1);
                    $str = 1;
                else:
                    $str = 3; // '上班卡已打！'
                endif;
                
            endif;
            // 考勤已记录
            if($flage[0]['off'] && $flage[0]['on']):
                $str = 4; // '今天考勤已记录！' 
            endif;

        else:
            // 写入数据库 add
            $insertResult = TeacherAttendanceModel::model()
                ->getDb()
                ->insert($data, 1);
            $str = 1;
        endif;
        return $str;
    }

    /**
    *计算一段时间的星期几
    * $start 开始时间戳， $end 结束时间戳
    */ 
    public function whatDay($start, $end)
    {
        $start = strtotime(date('Y-m-d', $start));
        $end = strtotime(date('Y-m-d', $end));
        $whatDay = array();
        for ($i=$start; $i < $end; $i = $i+3600*24) { 
            $whatDay[] = date('D', $i);
        }
        return $whatDay;
    }

    /**
    *计算一段时间的号数
    * $start 开始时间戳， $end 结束时间戳
    */ 
    public function howDays($start, $end)
    {
        $start = strtotime(date('Y-m-d', $start));
        $end = strtotime(date('Y-m-d', $end));
        $whatDay = array();
        for ($i=$start; $i < $end; $i = $i+3600*24) { 
            $whatDay[] = date('d', $i);
        }
        return $whatDay;
    }

    

}