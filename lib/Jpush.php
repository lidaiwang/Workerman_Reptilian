<?php
	class Jpush {
		const PUSHURL = "https://api.jpush.cn/v3/push";
		
		private $apikey;
		private $secretkey;
		

		public function __construct($apikey, $secretkey){
			$this->apikey = $apikey;
			$this->secretkey = $secretkey;
		}
		
		/**
		 * 推送给指定的用户
		 * 
		 * @param array $pidArr 		//用户推送ID数组
		 * @param string $msgstr		//推送消息字符串
		 * @return 
		 * @author xym Created at 2016-1-19
		 */
		public function sendTarget($pidArr, $msgstr) {
			$json = new Json();
			$pushData = array(
					'platform' => array('android', 'ios'),
					'audience' => array(
							'registration_id' => $pidArr
					),
					'notification' => array(
							'alert' => $msgstr,
// 							'android' => array(
// 								'alert' => $msgstr,
// 							),
							'ios' => array(
								'badge' => 1,
								'sound' => 'default',
								'extras' => array(
									'vip' => 1,
								)
							)
					),
					"options"	=> array(
							"apns_production" => true
					)
			);
			
			return $this->_requestPost(Jpush::PUSHURL, $json->json_from_array($pushData));
		}
		
		private function _requestPost($url, $postData){
			$header = array(
					'Content-Type: application/json',
					'Authorization: Basic ' . base64_encode($this->apikey . ":" . $this->secretkey)
			);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_USERAGENT, 'JPush-API-PHP-Client');
			//建立连接最长耗时
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			//请求最长耗时
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
			if (curl_errno($ch))
				return array(
						"state" => FALSE,
						"error" => ""
				);
			$data = json_decode($data, TRUE);
			if (isset($data['msg_id']))
				return array(
						"state" => TRUE,
						"msg_id" => $data['msg_id']
				);
			curl_close ($ch);
			//默认返回
			return array(
						"state" => FALSE,
						"error" => empty($data['errmsg']) ? "" : $data['errmsg']
				);
		}
	}