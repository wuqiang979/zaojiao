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
                    'plugin' => 'layer',
                    'this' => $this
                )
            )
        );

        // 登录判断
        if (!arModule('Lib.Auth')->isLoginIn()) :
            $this->redirectError('/main/Account/index', '请先登录');
        else :
            // 用户信息记录
            arModule('Lib.Member')->onUserOperationInItialization();
        endif;

    }

}
