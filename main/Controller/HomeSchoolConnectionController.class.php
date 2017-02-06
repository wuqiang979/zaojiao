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
// 家校互联
class HomeSchoolConnectionController extends BaseController
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
            '班级相册管理' => arU('ClassPhotoMa'),
            '院长信箱管理' => arU('DeanMailboxMa')
        );
        $this->assign(array('headnavs' => $headnavs));
    }
	// 班级相册管理
	public function ClassPhotoMaAction()
	{
        if (arPost()) :
            $data = array_map('trim', arPost());
            if($data['type'] == "1"):
                // 幼儿照片
                unset($data['classid']);
                unset($data['name']);
            else:
                unset($data['bid']);
            endif;
            if ($rid = arRequest('pid')) :
                // 写入数据库 edit
                $insertResult = ClassPicModel::model()
                    ->getDb()
                    ->where(array('pid' => $rid))
                    ->update($data, 1);
            else :
                // 写入数据库 add
                $data['sid'] = arCfg('current_member.sid');


                // 记录到 s_edu_class_pic 表
                $insertResult = ClassPicModel::model()
                    ->getDb()
                    ->insert($data, 1);
            endif;
            if ($insertResult) :
                $this->redirectSuccess();
            else :
                $this->redirectError();
            endif;

        endif;
        // 班级
        $class = EduClassroomModel::model()->getDb()
            ->where(array('sid'=>arCfg('current_member.sid'),'status'=>1)) // status 条件不太理想
            ->order('class_type desc,cid asc')
            ->queryAll();

        // 幼儿
        $babies = BabyModel::model()->getDb()
            ->select('bid, name')
            ->queryAll();

        // 搜索条件
        $condition = array(
        );
        $condition['sid'] = arCfg('current_member.sid');
//        if ($searchtext = urldecode(arRequest('cid'))) :
//             $condition['sname like '] = '%' . $searchtext . '%';
//        endif;
//        if ($searchtext = urldecode(arRequest('type'))) :
//             $condition['type'] = $searchtext;
//        endif;
        if (arRequest('cid') && arRequest('type')):
            if(arRequest('type' == '1')):
                // 该班级所有学生的照片
                $classCon = array(
                    'sid' => arCfg('current_member.sid'),
                    'cid' => arRequest('cid')
                );
                $baby = EduClassListModel::model()
                    ->getDb()
                    ->where($classCon)
                    ->queryRow();
                $condition[] = 'bid in ('. $baby["bids"] .')';
            else:
                $condition['classid'] = arRequest('cid');
                $condition['type'] = arRequest('type');
            endif;
        endif;

        // 分页计算
        $total = ClassPicModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $per = 10;
        $page = new Pageone($total, $per);

        // 获取数据
        $list = ClassPicModel::model()
            ->getDb()
            ->where($condition)
            ->order('pid desc')
            ->limit($page->limit())
            ->queryAll();
        $list = ClassPicModel::model()->getDetailInfo($list);
        $pagelist = $page ->fpage();
        $this->assign(array(
            'class' => $class,
            'babies' => $babies,
            'list' => $list,
            'pagelist' => $pagelist
        ));
//        echo '<pre>';
//        var_dump($list);
		$this->display('ClassPhotoMa');
	}


    // 幼儿请假管理
    public function appLeaveListMaAction()
    {

        $this->display('appLeaveListMa');
    }

     // 院长信箱管理
    public function DeanMailboxMaAction()
    {
        if (arPost()) :
            if (arModule('Lib.Auth')->isYz()) :
                $this->redirectError(); // 园长不能添加和修改
            endif;
            $data = array_map('trim', arPost());
            $data['dayfrom'] = time();
            if ($rid = arRequest('ymid')) :
                // 写入数据库 edit
                $insertResult = YzmessageModel::model()
                    ->getDb()
                    ->where(array('ymid' => $rid))
                    ->update($data, 1);
            else :
                // 写入数据库 add
                $data['sid'] = arCfg('current_member.sid');
                $data['frommid'] = arCfg('current_member.mid');
                $data['status'] = 1;
                // 记录到 s_edu_class_pic 表
                $insertResult = YzmessageModel::model()
                    ->getDb()
                    ->insert($data, 1);
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
        if (arModule('Lib.Auth')->isYz()) :
            $isYz = 1;
            $condition['statusback'] = 1; // 未查看
            $condition['status'] = 3; // 发送成功
        else:
            $isYz = 0;
            $condition = array('status !=' => 3); // 发送未失败
            $condition['frommid'] = arCfg('current_member.mid');
        endif;

        if ($day = urldecode(arRequest('day'))) :
            $condition[] = ' dayfrom between ' . strtotime($day) . ' and '. strtotime($day.' 23:59:59');
        endif;
        if ($searchtext = urldecode(arRequest('type'))) :
            $condition['type'] = $searchtext;
        endif;
        if ($searchtext = urldecode(arRequest('status'))) :
            unset($condition['status !=']);
            $condition['status'] = $searchtext;
        endif;
        if ($searchtext = urldecode(arRequest('statusback'))) :
            $condition['statusback'] = $searchtext;
        endif;

        // 分页计算
        $total = YzmessageModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $per = 10;
        $page = new Pageone($total, $per);

        // 获取数据
        $list = YzmessageModel::model()
            ->getDb()
            ->where($condition)
            ->order('ymid desc')
            ->limit($page->limit())
            ->queryAll();
        $list = YzmessageModel::model()->getDetailInfo($list);
        $pagelist = $page ->fpage();
        $this->assign(array(
            'list' => $list,
            'pagelist' => $pagelist,
            'isYz' => $isYz
        ));
       // echo '<pre>';
       // var_dump($condition);
        $this->display('DeanMailboxMa');
    }

    // 删除
    public function delAction()
    {
        if ($nid = arRequest('pid')) :
            // 班级相册管理之删除
            $delResult = ClassPicModel::model()
                ->getDb()
                ->where(array('pid' => $nid))
                ->delete();
       elseif($nid = arRequest('ymid')) :
           // 院长信箱管理之删除
           $delResult = YzmessageModel::model()
               ->getDb()
               ->where(array('ymid' => $nid))
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

    // 相片的添加/删除
    public function addpicAction()
    {
        if(arPost()):
            $data = arPost();
            if(isset($data['sign']) && $data['sign'] == 'more'):
                // 上传图片的ajax
                $type = ClassPicModel::model()
                    ->getDb()
                    ->where(array('pid' => $data['pid']))
                    ->queryRow();
                $res = 0;
                if($type['type'] !== '1'):
                    $res = 1;
                endif;
                echo json_encode($res);
                exit;
            elseif(isset($data['sign']) && $data['sign'] == 'del'):
                // 删除
                if(isset($data['del'])):
                    $picDel = $data['del'];
                    $picOld = ClassPicModel::model()
                        ->getDb()
                        ->where(array('pid' => $data['pid']))
                        ->queryRow();
                    $picOld = explode(',', $picOld['pic']);
                    foreach ($picOld as $key=> &$pic){
                        if(in_array($pic, $picDel)):
                            unset($picOld[$key]);
                        endif;
                    }
                    $updateData = array(
                        'pic' => implode(',', $picOld)
                    );
                    $insertResult = ClassPicModel::model()
                        ->getDb()
                        ->where(array('pid' => $data['pid']))
                        ->update($updateData);
                else:
                    $insertResult = '';
                endif;

            else:
                // 上传
                $pic = array();
                if($_FILES):
                    # 有上传图片
                    foreach ($_FILES as $key => $file){
                        if($file['name']):
                            $pic[] = arComp('ext.upload')->upload($key, arComp('url.route')->pathToDir(AR_SERVER_PATH.'Upload'));
                            $data['recordtime'] = time();
                        endif;
                    }
                    // var_dump(arComp('ext.upload') -> errorMsg());exit;
                    $data['pic'] = implode(',', $pic);
                    $insertResult = ClassPicModel::model()
                        ->getDb()
                        ->where(array('pid' => $data['pid']))
                        ->update($data, 1);
                else:
                    $insertResult = '';
                endif;

            endif;

            if($insertResult):
                $this->redirectSuccess(array('', array('pid' => $data['pid'])));
            else:
                $this->redirectError(array('', array('pid' => $data['pid'])));
            endif;

        endif;

        $pic = ClassPicModel::model()
            ->getDb()
            ->where(array('pid' => arRequest('pid')))
            ->queryRow();
        if($pic['pic']):
            $pics = explode(',', $pic['pic']);
        else:
            $pics = array();
        endif;

        $this->assign(array('nonav' => true, 'pics' => $pics ));
        $this->display();
    }

    // 信息的发送/查看
    public function sendAction()
    {
        
        if ($ymid = arRequest('ymid')) :
            if(arRequest('sign') == 'send'):
                // 信息的发送
                if (arModule('Lib.Auth')->isYz()) :
                    $this->redirectError(arRequest('url'), '没有权限');
                else:
                    $data = array( 
                        'status' => 3,
                        'statusback' => 1,
                        'dayto' => time()

                    );
                    $delResult = YzmessageModel::model()
                        ->getDb()
                        ->where(array('ymid' => $ymid))
                        ->update($data, 1);
                endif;
            elseif(arRequest('sign') == 'chakan'):
                // 信息的查看
                if (!arModule('Lib.Auth')->isYz()) :
                    $this->redirectError(arRequest('url'), '没有权限');
                else:
                    $data = array(
                        'statusback' => 2
                    );
                    $delResult = YzmessageModel::model()
                        ->getDb()
                        ->where(array('ymid' => arPost('ymid')))
                        ->update($data, 1);
                endif;
            endif;
        else :
            $this->redirectError(arRequest('url'), '参数错误');
        endif;

        if ($delResult) :
            $this->redirectSuccess(arRequest('url'));
        else :
            $this->redirectError(arRequest('url'));
        endif;

    }

}

