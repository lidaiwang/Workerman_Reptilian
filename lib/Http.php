<?php
	class Http {

        private static $Http;

        //单例化
        public static function _getInstance() {
            if (self::$Http) {
                return self::$Http;
            }
            self::$Http = new self();
            return self::$Http;
        }
        
        protected function __construct(){
            
        }
      
	
		/**
		 * 模拟多线程调用接口（向本系统相应方法发送请求）
		 * @param array $data			//要发送的数据，键值类型的数据
		 * @param string $method		//处理方法，在common_control下对应的方法
		 * @return int
		 * @author xym Created at 2013-12-13
		 */
		public function multi_thread($data, $method){
			ignore_user_abort(true);//关闭浏览器也继续运行
			$host = $_SERVER['HTTP_HOST'];
			$port = $_SERVER['SERVER_PORT'];
			$fp = fsockopen($host, $port); //连接服务器
			//code为识别验证码
			$data['code'] = $method . "4ghoeu7n-h049uy-743hgjr-mkt0-32n3041-912834";
		
			$encoded = http_build_query($data);//转换数据格式为http传输格式
			//模拟http请求头信息
			$path = '/common/'. $method; // 请求路径
			$send_type = 'POST'; // 请求方法(POST方式)
			$post = "$send_type $path HTTP/1.1\n";
			$post .= "Host: $host\n";
			$post .= "Content-type: application/x-www-form-urlencoded\n";
			$post .= "Content-length: " . strlen ( $encoded ) . "\n";
			$post .= "Connection: close\n\n";
			$post .= "$encoded\n";
			//传送信息
			fputs($fp, $post);
			sleep(0.5);//休眠0.5s
			//关闭链接
			fclose($fp);
			return 0;
		}

		
		/**  
		 * 发送get请求
		 * @param
		 * @return
		 * @author daiwang Created at 2018年1月5日
		 */
		public function curlGet($url,$queryparas=array(),$timeout = 2,$header = array(),$proxy = array()){
		    if(!empty($queryparas)){
		        if(is_array($queryparas)){
		            $postData = http_build_query($queryparas);
		            $url .= strpos($url,'?')?'':'?';
		            $url .= $postData;
		        }else if(is_string($queryparas)){
		            $url .= strpos($url,'?')?'':'?';
		            $url .= $queryparas;
		        }
		    }
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
		    if(!empty($header) && is_array($header)){
		        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		    }
		    
		    if (!empty($proxy)){
		        curl_setopt($ch, CURLOPT_PROXYAUTH, 1);
		        curl_setopt($ch, CURLOPT_PROXY, $proxy['ip']);
		        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
		        curl_setopt($ch, CURLOPT_PROXYTYPE, 0);
		    }

		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		    $output = curl_exec($ch);
		    
		    $result['code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		    $result['content'] = $output;
		    curl_close($ch);
		    return $result;
		}
		
		/**
		 * 发送post请求
		 * @param
		 * @return
		 * @author daiwang Created at 2018年1月5日
		 */
		public function curlPost($url, $queryparas=array(), $postdata=array(), $header = array(), $timeout = 2, $proxy = array()){
		    if(!empty($queryparas)){
		        if(is_array($queryparas)){
		            $postData = http_build_query($queryparas);
		            $url .= strpos($url,'?')?'':'?';
		            $url .= $postData;
		        }else if(is_string($queryparas)){
		            $url .= strpos($url,'?')?'':'?';
		            $url .= $queryparas;
		        }
		    }
		    
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
		    if(!empty($header) && is_array($header)){
		        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		    }
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		    if (!empty($proxy)){
		        curl_setopt($ch, CURLOPT_PROXYAUTH, 1);
		        curl_setopt($ch, CURLOPT_PROXY, $proxy['ip']);
		        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
		        curl_setopt($ch, CURLOPT_PROXYTYPE, 0);
		    }
		    curl_setopt($ch, CURLOPT_POST, TRUE);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		    
		    $output = curl_exec($ch);
		    
		    $result['code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		    
		    $result['content'] = $output;
		    curl_close($ch);
		    return $result;
		}
		/**
		 * 发送Del请求
		 * @param
		 * @return
		 * @author daiwang Created at 2018年1月5日
		 */
		public function curlDel($url, $queryparas=array(), $postdata=array(), $header = array(), $timeout = 2,$proxy = array()){
		    if(!empty($queryparas)){
		        if(is_array($queryparas)){
		            $postData = http_build_query($queryparas);
		            $url .= strpos($url,'?')?'':'?';
		            $url .= $postData;
		        }else if(is_string($queryparas)){
		            $url .= strpos($url,'?')?'':'?';
		            $url .= $queryparas;
		        }
		    }
		    
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
		    if(!empty($header) && is_array($header)){
		        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		    }
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		    
		    if (!empty($proxy)){
		        curl_setopt($ch, CURLOPT_PROXYAUTH, 1);
		        curl_setopt($ch, CURLOPT_PROXY, $proxy['ip']);
		        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
		        curl_setopt($ch, CURLOPT_PROXYTYPE, 0);
		    }
		    
		    
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");   
            curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
		    
		    $output = curl_exec($ch);
		    
		    $result['code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		    $result['content'] = $output;
		    curl_close($ch);
		    return $result;
		}
		
		/**
		 * 设置JSON返回的头信息
		 */
		public function headerJson(){
		    header('Content-type: application/json; charset=utf-8');

		}
		

        /**
         * 返回成功的JSON信息  和 主体信息
         * @param string $key       //提示语主键
         * @author daiwang Created at 2018-1-5
         */
        public function custom_Json($data) {
            if (is_array($data)){
                echo json_encode($data);
            }else {
                echo $data;
            }
        }
        
        
        
        
	}