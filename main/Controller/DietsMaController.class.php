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
// 饮食管理
class DietsMaController extends BaseController
{
    public function init()
    {
        parent::init();
        // 设置布局文件
        $this->setLayoutFile('content');

        $headnavs = array(
            '幼儿食谱管理' => arU('DietsMa/infantRecipe'),
            '教师食谱管理' => arU('DietsMa/teacherRecipe'),
            '菜式管理' => arU('DietsMa/CuisineMa'),
            // '食材管理' => arU('DietsMa/IngredientsMa'),
            '食材用量' => arU('DietsMa/IngredientsAmountsMa'),
            '食材库存' => arU('DietsMa/IngredientsInventory'),
        );
        $this->assign(array('headnavs' => $headnavs));
    }


    // 幼儿食谱
    public function infantRecipeAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $data = arPost();
        $data['sid'] = $sid;
        $eno = null;
        if(isset($data['type'])):
            $type = $data['type'];
            unset($data['type']);
        endif;

        // 操作
        if(isset($type)):
            // 添加
            if($type=='add'):
                $data['created_time'] = time();
                $data['mid'] = arCfg('current_member.mid');
                unset($data['brid']);
                $eno = EatInfantRecipeModel::model()
                    ->getDb()
                    ->insert($data,true);
            // 修改
            elseif($type =='modify'):
                $brid = $data['brid'];
                unset($data['brid']);
                $eno = EatInfantRecipeModel::model()
                    ->getDb()
                    ->where(array('brid'=>$brid))
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
            'sid'   => $sid,
        );
        if(arGet()):
            $condition['week'] = arGet('week'); 
            $condition['yid'] = arGet('yid'); 
            $condition = array_filter($condition);
        endif;

        // 获取数据
        $res = EatInfantRecipeModel::model()
            ->getDb()
            ->where($condition)
            ->order('brid desc')
            ->queryRow();

        // 获取关联数据
        // 获取学年
        $res1 = $res = EatInfantRecipeModel::model()->getSchoolYearSelectByYid($res);

        // 获取菜式
        $res = EatInfantRecipeModel::model()->getCuisine($res);

        // 获取其他数据
        // 获取学年select
        $schoolyear = EduSchoolYearModel::model()->getSchoolYearSelect($sid);

        // 获取菜式select
        $cuisine = EatCuisineModel::model()
            ->getDb()
            ->select('cid,cuisine_name')
            ->where(array('sid'=>$sid))
            ->order('cid desc')
            ->queryAll();

        // 分配模板
        $this->assign(array('res'=>$res,'schoolyear'=>$schoolyear,'cuisine'=>$cuisine,));

        // 数据输出
        // echo('<pre>');
        // print_r($res);
        // var_dump($res1);
        // print_r(arPost());
        // print_r($data);
        // echo('</pre>');
        
        $this->display('infantRecipe');
    }

    // 删除幼儿食谱
    public function infantRecipeDelAction()
    {
        if ($brid = arRequest('brid')) :
            $delResult = EatInfantRecipeModel::model()
                ->getDb()
                ->where(array('brid' => $brid))
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

    // 教师食谱
    public function teacherRecipeAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $data = arPost();
        $data['sid'] = $sid;
        $eno = null;
        if(isset($data['type'])):
            $type = $data['type'];
            unset($data['type']);
        endif;

        // 操作
        if(isset($type)):
            // 添加
            if($type=='add'):
                $data['created_time'] = time();
                $data['mid'] = arCfg('current_member.mid');
                unset($data['trid']);
                $eno = EatTeacherRecipeModel::model()
                    ->getDb()
                    ->insert($data,true);
            // 修改
            elseif($type =='modify'):
                $trid = $data['trid'];
                unset($data['trid']);
                $eno = EatTeacherRecipeModel::model()
                    ->getDb()
                    ->where(array('trid'=>$trid))
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
            'sid'   => $sid,
        );
        if(arGet()):
            $condition['week'] = arGet('week'); 
            $condition['yid'] = arGet('yid'); 
            $condition = array_filter($condition);
        endif;

        // 获取数据
        $res = EatTeacherRecipeModel::model()
            ->getDb()
            ->where($condition)
            ->order('trid desc')
            ->queryRow();

        // 获取关联数据

        // 获取学年
        $res1 = $res = EatTeacherRecipeModel::model()->getSchoolYearSelectByYid($res);

        // 获取菜式
        $res = EatTeacherRecipeModel::model()->getCuisine($res);

        // 获取其他数据
        // 获取学年select
        $schoolyear = EduSchoolYearModel::model()->getSchoolYearSelect($sid);
        // 获取菜式select
        $cuisine = EatCuisineModel::model()
            ->getDb()
            ->select('cid,cuisine_name')
            ->where(array('sid'=>$sid))
            ->order('cid desc')
            ->queryAll();

        // 分配模板
        $this->assign(array('res'=>$res,'schoolyear'=>$schoolyear,'cuisine'=>$cuisine,));

        // 数据输出
        // echo('<pre>');
        // print_r($res);
        // var_dump($res1);
        // print_r(arPost());
        // print_r($data);
        // echo('</pre>');

        $this->display('teacherRecipe');
    }

    // 删除教师食谱
    public function teacherRecipeDelAction()
    {
        if ($trid = arRequest('trid')) :
            $delResult = EatTeacherRecipeModel::model()
                ->getDb()
                ->where(array('trid' => $trid))
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

    // 菜式管理
    public function cuisineMaAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $data = arPost();
        $eno = null;


        // 添加修改
        if(isset($data['type'])):
            //添加
            if($data['type']=='add'):
                $data['created_time'] = time();
                $data['sid'] = $sid;
                unset($data['cid']);
                // 插入数据
                $eno = EatCuisineModel::model()
                    ->getDb()
                    ->insert($data,true);
            elseif($data['type']=='modify'):
                // 修改数据
                $eno = EatCuisineModel::model()
                    ->getDb()
                    ->where(array('cid'=>$data['cid']))
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
        if ($searchtext = urldecode(arRequest('searchtext'))) :
            $condition['cuisine_name like '] = '%' . $searchtext . '%';
        endif;

        // 分页计算
        $total = EatCuisineModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total,10);

        // 获取数据
        $res = EatCuisineModel::model()
            ->getDb()
            ->where($condition)
            ->order('cid desc')
            ->limit($page->limit())
            ->queryAll();
        // 获取关联数据

        // 获取其他数据

        // 分配模板
        $this->assign(array('res'=>$res,'page'=>$page,));

        // 数据输出
        // echo('<pre>');
        // print_r(arPost());
        // echo('<hr>');
        // print_r($data);
        // echo('<hr>');
        // echo('</pre>');

        $this->display('cuisineMa');
    }

    // 删除菜式
    public function cuisineDelAction()
    {
        if ($cid = arRequest('cid')) :
            $delResult = EatCuisineModel::model()
                ->getDb()
                ->where(array('cid' => $cid))
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

    // 食材管理
    public function ingredientsMaAction()
    {
        $this->display('ingredientsMa');
    }

    // 食材用量管理
    public function ingredientsAmountsMaAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');

        // 操作

        // 搜索条件
        $condition = array(
            'sid'   =>$sid,
        );
        if(arGet('searchtext')):
            $sdate = strtotime(arGet('searchtext'));
            $y = date('Y',$sdate);
            $m = date('m',$sdate);
            $d = date('d',$sdate);
            $star = mktime(0,0,0,$m,$d,$y);
            $end = mktime(24,0,0,$m,$d,$y);
            $condition[] = ' use_time between '. $star .' and ' . $end ;
        endif;
        $condition = array_filter($condition);

        // 分页计算
        $total = EatFoodUseModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total,10);

        // 获取数据
        $res = EatFoodUseModel::model()
            ->getDb()
            ->where($condition)
            ->order('fuid desc')
            ->queryAll();

        // 获取关联数据
        $res = EatFoodUseModel::model()->getDetailInfo($res);

        // 分配模板
        $this->assign(array('res'=>$res,'page'=>$page,));

        // 数据输出
        // echo('<pre>');
            // var_dump($res);
        // var_dump($condition);
        // echo('</pre>');

        $this->display('ingredientsAmountsMa');
    }

    // 食材库存
    public function ingredientsInventoryAction()
    {
        // 初始化
        $sid = arCfg('current_member.sid');
        $data = arPost();
        $eno = null;
        $data['sid'] = $sid;
        if(isset($data['type'])):
            $type = $data['type'];
            unset($data['type']);
        endif;

        // 操作
        if(isset($type)):
            // 添加食材库存
            if($type=='add'):
                $data['created_time'] = time();
                unset($data['fiid']);
                $eno = EatFoodInventoryModel::model()
                    ->getDb()
                    ->insert($data,true);
            // 修改食材库存
            elseif($type=='modify'):
                $eno = EatFoodInventoryModel::model()
                    ->getDb()
                    ->where(array('fiid'=>$data['fiid']))
                    ->update($data,true);
            // 使用食材
            elseif($type=='use'):
                // 食材剩余量
                $all = EatFoodInventoryModel::model()
                    ->getDb()
                    ->select('food_inventory')
                    ->where(array('fiid'=>$data['fiid']))
                    ->queryRow();
                $surplus = $all['food_inventory']-$data['food_inventory'];
                // 修改食材库存量
                $eno = EatFoodInventoryModel::model()
                    ->getDb()
                    ->where(array('fiid'=>$data['fiid']))
                    ->update(array('food_inventory'=>$surplus));
                // 插入数据到食材用量表
                $arr =array(
                    'sid' =>$sid,
                    'use_time' =>time(),
                    'mid'   =>arCfg('current_member.mid'),
                    'fiid'  =>$data['fiid'],
                    'use'   =>$data['food_inventory'],
                    'note'  =>$data['use_note']
                );
                $eno1 = EatFoodUseModel::model()
                    ->getDb()
                    ->insert($arr);
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
            'sid'   => $sid,
            'food_inventory >'  =>0,
        );
        if(arGet()):
            $searchtext = arGet('searchtext');
            $condition['food_name like'] = '%' .$searchtext.'%';
        endif;

        // 分页计算
        $total = EatFoodInventoryModel::model()
            ->getDb()
            ->where($condition)
            ->count();
        $page = new Page($total,10);

        // 获取数据
        $res = EatFoodInventoryModel::model()
            ->getDb()
            ->where($condition)
            ->order('fiid desc')
            ->limit($page->limit())
            ->queryAll();
            
        // 获取关联数据
        // 获取其他数据
        // 分配模板
        $this->assign(array('res'=>$res,'page'=>$page,));

        // 数据输出
        // echo('<pre>');
        // print_r($data);
        // echo('</pre>');

        $this->display('ingredientsInventory');
    }
    // 删除食材库存
    public function foodInventoryDelAction()
    {
        if ($fiid = arRequest('fiid')) :
            $delResult = EatFoodInventoryModel::model()
                ->getDb()
                ->where(array('fiid' => $fiid))
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


    // 食谱列表
    public function recipeListAction()
    {
        $this->display('recipeList');
    }

}

