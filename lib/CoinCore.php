<?php
	class CoinCore {
	    // Configuration options
	    private $username;
	    private $password;
	    private $proto;
	    private $host;
	    private $port;
	    private $url;
	    private $CACertificate;
	
	    // Information and debugging
	    public $status;
	    public $error;
	    public $raw_response;
	    public $response;
	
	    private $id = 0;
	
	    /**
	     * @param string $coin	//ltc、btc...
	     * @param string $url
	     */
	    function __construct($host, $port, $user, $pwd, $proto = 'http', $certificate = null, $url = null) {
	    	
	    	// Set some defaults
	    	$this->proto         = $proto;
	    	$this->CACertificate = $certificate;
	    	
	    	$this->host          = $host;
	    	$this->port          = $port;
	    	$this->url           = $url;
	    	
	        $this->username      = $user;
	        $this->password      = $pwd;
	    }
	
	    /**
	     * @param string|null $certificate
	     */
	    public function setSSL($certificate = null) {
	        $this->proto         = 'https'; // force HTTPS
	        $this->CACertificate = $certificate;
	    }
	
	    public function __call($method, $params = []) {
	        $this->status       = null;
	        $this->error        = null;
	        $this->raw_response = null;
	        $this->response     = null;
	
	        // If no parameters are passed, this will be an empty array
	        $params = array_values($params);
	
	        // The ID should be unique for each call
	        $this->id++;
	
	        // Build the request, it's ok that params might have any empty array
	        $request = json_encode(array(
	            'method' => $method,
	            'params' => $params,
	            'id'     => $this->id
	        ));
	        
	        $options = array(
	                CURLOPT_RETURNTRANSFER => true,
	                CURLOPT_FOLLOWLOCATION => true,
	                CURLOPT_MAXREDIRS      => 10,
	                CURLOPT_HTTPHEADER     => array('Content-type: application/json'),
	                CURLOPT_POST           => true,
	                CURLOPT_POSTFIELDS     => $request
	        );
	
	        // Build the cURL session
	        $curl    = curl_init("{$this->proto}://{$this->username}:{$this->password}@{$this->host}:{$this->port}/{$this->url}");
	        
	        //设置证书
	        if ($this->proto == 'https') {
	            if ($this->CACertificate != null) {
	                $options[CURLOPT_CAINFO] = $this->CACertificate;
	                $options[CURLOPT_CAPATH] = DIRNAME($this->CACertificate);
	            }else {
	                $options[CURLOPT_SSL_VERIFYPEER] = false;
	            }
	        }
	
	        curl_setopt_array($curl, $options);
	
	        // Execute the request and decode to an array
	        $this->raw_response = curl_exec($curl);
	        $this->response     = json_decode($this->raw_response, true);
	
	        // If the status is not 200, something is wrong
	        $this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
	        // If there was no error, this will be an empty string
	        $curl_error = curl_error($curl);
	
	        curl_close($curl);
	
	        //请求出错
	        if (!empty($curl_error)) {
	            $this->error = $curl_error;
	        }elseif ($this->response['error']) {
	            // bitcoind返回错误信息
	            $this->error = $this->response['error']['message'];
	        }elseif ($this->status != 200) {
	            // bitcoind响应码错误
	            switch ($this->status) {
	                case 400:
	                    $this->error = 'HTTP_BAD_REQUEST';
	                    break;
	                case 401:
	                    $this->error = 'HTTP_UNAUTHORIZED';
	                    break;
	                case 403:
	                    $this->error = 'HTTP_FORBIDDEN';
	                    break;
	                case 404:
	                    $this->error = 'HTTP_NOT_FOUND';
	                    break;
	            }
	        }
	        
	        if ($this->error) {
	            return [
	                    'error' => $this->error,
	            ];
	        }
	        
	        return [
	                'data' => $this->response['result']
	        ];
	    }
	}