<?php
class Message{
	
	private $conf = array(
			'account'		=> '101100-WEB-HUAX-065531',
			'pwd'			=> 'ZDBNDFJG',
			'msgUrl'		=> 'http://www.stongnet.com/sdkhttp/sendsms.aspx',
			'balanceUrl'	=> 'http://www.stongnet.com/sdkhttp/getbalance.aspx',
			'reportUrl'		=> 'http://www.stongnet.com/sdkhttp/getmtreport.aspx',
			'pwdUrl'		=> 'http://www.stongnet.com/sdkhttp/uptpwd.aspx',
	);
	
	/**
	 * 发送验手机证码，返回发送成功的短信总数
	 * 状态码意义
	 * 
	 * @param string $phone 		//手机号码(多个手机号码则用英文逗号分隔)
	 * @param string $msg			//信息内容
	 * @return boolean				//是否发送成功
	 * @author xym Created at 2013-8-10
	 */
	public function sendMsg($phone, $msg){
		$strSourceAdd = "";	//子通道号，可为空（预留参数一般为空）
		$param = "reg=" . $this->conf['account'] . "&pwd=" . $this->conf['pwd'] . "&sourceadd=" . $strSourceAdd . "&phone=" . $phone . "&content=". $msg;
		$strResponse = $this->postSend($this->conf['msgUrl'], $param);
		
		$data = array();
		parse_str($strResponse, $data);
		if (isset($data['result']) && $data['result'] == 0) return 1;
		return 0;
	}
	
	/**
	 * 查询账户余额
	 * @return 
	 * @author xym Created at 2014-9-25
	 */
	public function getBalance(){
		$param = "reg=" . $this->conf['account'] . "&pwd=" . $this->conf['pwd'];
		$repos = $this->postSend($this->conf['balanceUrl'], $param);
		if (empty($repos)) return '';
		
		$data = array();
		parse_str($repos, $data);
		$data = array_merge($data, array('time' => date('Y-m-d')));
		return $data;
	}
	
	/**
	 * 发送报告
	 * @return array(
	 * 					'result' => int,
	 * 					'message' => '结果解析',
	 * 					'total' => 当天提交的总量,
	 * 					'waitnum' => 等待发送的数量,
	 * 					'sendingnum' => 正在发送的数量,
	 * 					'sucessnum' => 成功发送的数量,
	 * 					'failnum' => 发送失败的数量
	 * 		   )
	 * @author xym Created at 2014-9-25
	 */
	public function getReport(){
		$time = time();
		$param = "reg=" . $this->conf['account'] . "&pwd=" . $this->conf['pwd'];
		$repos = $this->postSend($this->conf['reportUrl'], $param);
		if (empty($repos)) return '';
		
		$repos .= '&rectime='. date('Y-m-d', $time) .'&inttime='. $time;
		return $repos;
	}
	
	/**
	 * 修改密码
	 * @param $newpwd	//新密码
	 * @return array('result' => int, 'message' => '结果解析', 'time' => 当前时间);
	 * @author xym Created at 2014-9-26
	 */
	public function changePwd($newpwd){
// 		$oldpwd = $this->conf['pwd'];
		$oldpwd = '';
		$param = "reg=" . $this->conf['account'] . "&pwd=" . $oldpwd ."&newpwd=". $newpwd;
		$repos = $this->postSend($this->conf['pwdUrl'], $param);
		if (empty($repos)) return '';
		
		$data = array();
		parse_str($repos, $data);
		$data = array_merge($data, array('time' => date('Y-m-d H:i:s')));
		return $data;
	}
	
	
	
	
	/**
	 * 发送请求
	 * @return string
	 * @author xym Created at 2014-9-25
	 */
	public function postSend($url, $param){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$data = curl_exec($ch);
		curl_close ( $ch );
		return $data;
	}
	
	public function getSend($url, $param){
		$ch = curl_init($url."?".$param);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$output = curl_exec($ch);
		
		return $output;
	}
}