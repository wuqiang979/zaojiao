<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Controller of webapp.
 */
class BaseController extends ArController
{
    /**
     * just the example of get contents.
     *
     * @return void
     */

    // 初始化方法
    public function init()
    {
        // 调用layer msg cart插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'layer, echarts',
                    'this' => $this
                )
            )
        );
        // var_dump(arComp('list.session')->get('member.mid'));
        // exit;
        // 登录判断
        if (!arModule('Lib.Auth')->isLoginIn()) :
            $this->redirectError('Account/index', '请先登录');
        else :
            // 用户信息记录
            arModule('Lib.Member')->onUserOperationInItialization();
        endif;

        // var_dump(arCfg('current_member'));
        // exit;

    }

}
