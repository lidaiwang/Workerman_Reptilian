<?php
	class Json{
		/**
		 * 将数组转成JSON字符串（可兼容中文）
		 * @access public
		 * @param array $array
		 * @param boolean $zh （是否兼容中文）
		 * @param boolean $zh_key （是否处理数组中的中文key）
		 * @return string
		 * @author Ymj                2012-5-6 下午1:03:03
		 */
		public function json_from_array($array,$zh=true,$zh_key=true){
			if($zh == false){
				return json_encode($array);
			}
			$this->_json_zh($array,$zh_key);
			return urldecode(json_encode($array));
		}
		
		private function _json_zh(&$array,$zh_key=true){
			foreach ($array as $key => $value){
				if(is_array($value)){
					$this->_json_zh($array[$key],$zh_key);
				}
				else{
					if(!is_bool($value) && !is_numeric($value))
						$array[$key] = urlencode(preg_replace('/(\\\\|")/', '\\\$1', str_replace(array("\r", "\n", "\r\n", "\n\r"), "<br />", $value)));
				}
				if($zh_key && is_string($key)){
					$new_key = urlencode($key);
					if ($new_key != $key) {
						$array[$new_key] = $array[$key];
						unset($array[$key]);
					}
				}
			}
		}
	}