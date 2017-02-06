<?php

namespace Lib\Module;
// namespace Lib\Ext;
class PhoneCodeModule
{
	/**
	 * 产生随机数
	 * @param $length 产生随机数长度
	 * @param $type 返回字符串类型
	 * @param $hash  是否由前缀，默认为空. 如:$hash = 'zz-'  结果zz-823klis
	 * @return 随机字符串 $type = 0：数字+字母
	 *$type = 1：数字
	 *$type = 2：字符
	 */
	public function random($length, $type = 0, $hash = '') 
	{
		if ($type == 0) {
			$chars = '0123456789abcdefghijklmnopqrstuvwxyz';
		} else if ($type == 1) {
			$chars = '0123456789';
		} else if ($type == 2) {
			$chars = 'abcdefghijklmnopqrstuvwxyz';
		}
		$max = strlen ( $chars ) - 1;
		mt_srand ( ( double ) microtime () * 1000000 );
		for($i = 0; $i < $length; $i ++) {
			$hash .= $chars [mt_rand ( 0, $max )];
		}
		return $hash;
	}

	public function sendPhoneCode($mobllestr, $phone, $tpl_id = 14305, $key = 'af8daa1642f6c3a6475eabea7b1281bf')
	{
		//手机发短信接口
		$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL

		$smsConf = array(
		    'key'   => $key, //您申请的APPKEY
		    'mobile'    => $phone, //接受短信的用户手机号码
		    'tpl_id'    => $tpl_id, //您申请的短信模板ID，根据实际情况修改
		    'tpl_value' =>'#code#='.$mobllestr.'&#company#=聚合数据' //您设置的模板变量，根据实际情况修改
		    );
		$content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信

		if($content){
			$result = json_decode($content,true);
			$error_code = $result['error_code'];
			if($error_code == 0){
		        //状态为0，说明短信发送成功
				// echo "短信发送成功,短信ID：".$result['result']['sid'];
				$flage = 1;//成功
			}else{
		        //状态非0，说明失败
				$msg = $result['reason'];
				// echo "短信发送失败(".$error_code.")：".$msg;
				$flage = 0;//false
			}
		}else{
		    //返回内容异常，以下可根据业务逻辑自行修改
			// echo "请求发送短信失败";
			$flage = 0;//false
		}
		return $flage;

	}


	/**
	* 请求接口返回内容
	* @param  string $url [请求的URL地址]
	* @param  string $params [请求的参数]
	* @param  int $ipost [是否采用POST形式]
	* @return  string
	*/
	function juhecurl($url,$params=false,$ispost=0){
		$httpInfo = array();
		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
		curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
		curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
		if( $ispost )
		{
			curl_setopt( $ch , CURLOPT_POST , true );
			curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
			curl_setopt( $ch , CURLOPT_URL , $url );
		}
		else
		{
			if($params){
				curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
			}else{
				curl_setopt( $ch , CURLOPT_URL , $url);
			}
		}
		$response = curl_exec( $ch );
		if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
			return false;
		}
		$httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
		$httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
		curl_close( $ch );
		return $response;
	}



}