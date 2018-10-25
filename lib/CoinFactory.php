<?php
require_once 'CoinCore.php';
class CoinFactory {
    private $obj = null;
    
    
    public function __construct($conf){
        $this->connect($conf);
    }
    
    public function connect($conf = ''){
        $this->obj = new CoinCore($conf['host'], $conf['port'], $conf['user'], $conf['pwd']);
    }
    
    /**
     * 直接操作bitcoind的对象
     * @return CoinCore
     */
    public function getObj() {
        return $this->obj;
    }
    
    /**
     * 返回全网基本信息
     * @return array | false
     * @author xym Created at 2014-4-17
     */
    public function getInfo(){
        $info = $this->obj->getinfo();
        return $info;
    }
    
    /**
     * wallet
     * 根据地址标签返回该标签下所有地址包的总余额，没指定则返回钱包总余额
     * @param $label		//地址标签		*表示任意
     * @param $minconf		//最小确认次数
     * @return float | false
     * @author xym Created at 2014-4-16
     */
    public function getBalance($label = '*', $minconf = -1){
        if (empty($label)) {//该钱包总额（至少1次确认）
            $balance = $this->obj->getbalance();
        }else if ($minconf == -1) {//返回该地址虚拟币的总额（至少1次确认）
            $balance = $this->obj->getbalance($label);
        }else {
            $balance = $this->obj->getbalance($label, $minconf);
        }
        return $balance;
    }
    
    /**
     * wallet
     * 根据标签，返回该地址的总额
     * @param $label
     * @param $minconf		//最小确认次数（6为已经非常安全）
     * @return float | false
     * @author xym Created at 2014-4-21
     */
    public function getReceivedByAccount($label, $minconf = 1) {
        $amount = $this->obj->getreceivedbyaccount($label, $minconf);
        return $amount;
    }
    
    /**
     * wallet
     * 列出标签对应的交易，键为标签，值为虚拟币数量
     * @param $minconf		//最小确认次数
     * @return array | false
     * @author xym Created at 2014-4-22
     */
    public function listAccounts($minconf = 1) {
        $data = $this->obj->listaccounts($minconf);
        return $data;
    }
    
    /**
     * wallet
     * 返回所有交易信息
     * @param $blockhash
     * @param $target_conf
     * @return array | false
     * @author xym Created at 2014-4-22
     */
    public function listSinceBlock($blockhash = '*', $target_conf = 0){
        if (empty($blockhash)) {
            $data = $this->obj->listsinceblock();
        }elseif (empty($target_conf)) {
            $data = $this->obj->listsinceblock($blockhash);
        }else {
            $data = $this->obj->listsinceblock($blockhash, $target_conf);
        }
        	
        return $data;
    }
    
    /**
     * wallet
     * 返回钱包的所有有虚拟币的地址和币管理的相关信息
     * @return array | false
     * @author xym Created at 2014-4-21
     */
    public function listAddressGroupings() {
        $groups = $this->obj->listaddressgroupings();
        return $groups;
    }
    
    /**
     * wallet
     * 列出暂不可用的输出列表
     * @return array | false
     * @author xym Created at 2014-4-21
     */
    public function listLockUnspent() {
        $list = $this->obj->listlockunspent();
        return $list;
    }
    
    /**
     * wallet
     * 可以列出所有账户的信息， 即(0, true)
     * 获取地址接收到虚拟币的信息
     * @param int $minconf				//最小确认次数
     * @param boolean $includeempty		//是否包括总额为0的地址
     * @return array | false
     * @author xym Created at 2014-4-17
     */
    public function listReceivedByAddress($minconf = 1, $includeempty = false) {
        $data = $this->obj->listreceivedbyaddress($minconf, $includeempty);
        return $data;
    }
    
    /**
     * wallet
     * 可以列出所有账户的信息， 即(0, true)
     * 根据标签，列出账户接收到虚拟币的信息
     * @param $minconf		//最小确认次数
     * @param $includeempty	//是否包括虚拟币为空的账户
     * @return array | false
     * @author xym Created at 2014-4-21
     */
    public function listReceivedByAccount($minconf = 1, $includeempty = false) {
        $info = $this->obj->listreceivedbyaccount($minconf, $includeempty);
        return $info;
    }
    
    /**
     * wallet
     * 列出【最近的】交易信息
     * @param string $account	//地址标签
     * @param int $count		//列出数量
     * @param int $from			//0：表示从最新的交易起，取出 $count条记录
     * @return array | false
     * @author xym Created at 2014-4-21
     */
    public function listTransactions($account = '*', $count = 10, $from = 0) {
        if (empty($account)) $data = $this->obj->listtransactions();
        else $data = $this->obj->listtransactions($account, $count, $from);
        return $data;
    }
    
    
    /**
     * 验证地址的合法性
     * @param $address
     * @return array | false
     * //不是我们钱包的地址：array('isvalid' => boolean, 'address' => $address, 'ismine' => false)
     * //属于我们钱包的地址：array('isvalid' => boolean, 'address' => $address, 'ismine' => true, 'isscript' => boolean, 'pubkey' => string, 'iscompressed' => boolean, 'account' => string)
     * @author xym Created at 2014-4-17
     */
    public function validateAddress($address){
        $info = $this->obj->validateaddress($address);
        return $info;
    }
    
    /**
     * 获取内存池的所有交易ID
     * @return
     * @author xym Created at 2014-4-21
     */
    public function getRawMemPool() {
        $txidArr = $this->obj->getrawmempool();
        return $txidArr;
    }
    
    /**
     * (仅本地钱包涉及交易的交易ID有效)
     * 
     * 根据交易ID，获取交易信息
     * @param string $txid	//交易ID
     * @return array | false
     * @author xym Created at 2014-4-18
     */
    public function getTransaction($txid){
        $txinfo = $this->obj->gettransaction($txid);
        return $txinfo;
    }
    
    /**
     * 返回交易的原始数据(通过createrawtransaction等构造的交易数据 可以用于检查该交易是否已经存在于blockchain中)
     * @param $txid		//交易ID
     * @param $verbose	//verbose=0，仅返回hex数据，verbose=1则返回详细的格式化数据
     * @return array | string | false
     * @author xym Created at 2014-4-21
     */
    public function getRawTransaction($txid, $verbose = 0) {
        $data = $this->obj->getrawtransaction($txid, $verbose);
        return $data;
    }
    
    /**
     * wallet
     * 加密钱包(钱包自动停止，需手动重启)
     * 注意：需要自己记住 $encryptStr
     * @return string | false
     * @author xym Created at 2014-4-18
     */
    private function encryptWallet($encryptStr){
        $rs = $this->obj->encryptwallet($encryptStr);
        return $rs;
    }
    
    /**
     * wallet
     * 备份钱包到指定位置
     * @param $path		//备份文件路径
     * @return
     * @author xym Created at 2014-4-21
     */
    private function backupWallet($path){
        $rs = $this->obj->backupwallet($path);
        return $rs;
    }
    
    /**
     * wallet
     * 验证钱包密码，在n秒之后要重新验证
     * @param $encryptStr   //加密串
     * @param $seconds		//过期时间（秒）
     * @return NULL | false
     * @author xym Created at 2014-4-18
     */
    public function walletPassPhrase($encryptStr, $seconds) {
        $state = $this->obj->walletpassphrase($encryptStr, $seconds);
        return $state;
    }
    
    /**
     * wallet
     * 清除内存中的密码，锁定钱包
     * @return NULL | false
     * @author xym Created at 2014-4-18
     */
    public function walletLock(){
        $state = $this->obj->walletlock();
        return $state;
    }
    
    /**
     * wallet
     * 获取钱包指定地址的私钥
     * @param $address		//仅限本地钱包地址
     * @param $seconds		//密码有效时长（秒）
     * @return string | false
     * @author xym Created at 2014-4-18
     */
    public function dumpPrivkey($address, $seconds = 30){
        $this->walletPassPhrase($seconds);
        $privkey = $this->obj->dumpprivkey($address);
        return $privkey;
    }
    
    /**
     * wallet
     * 导入私钥
     * @param $address	//私钥地址
     * @param $label	//标签
     * @param $rescan	//重新扫描
     * @param $seconds	//密码有时长（秒）
     * @return
     * @author xym Created at 2014-4-21
     */
    public function importPrivkey($address, $label = '', $rescan = TRUE, $seconds = 30) {
        $this->walletPassPhrase($seconds);
        $rs = $this->obj->importprivkey($address, $label = '', $rescan);
        return $rs;
    }
    
    /**
     * wallet
     * 获取指定比特币地址的标签
     * @param $address		//比特币地址
     * @return string | false
     * @author xym Created at 2014-4-21
     */
    public function getAccount($address){
        $tag = $this->obj->getaccount($address);
        return $tag;
    }
    
    /**
     * wallet
     * 创建并返回新地址
     * @param $label
     * @return string | false
     * @author xym Created at 2014-4-21
     */
    public function getNewAddress($label = '') {
        $address = $this->obj->getnewaddress($label);
        return $address;
    }
    
    /**
     * wallet
     * 获取指定标签的比特币地址，可能有多个（若标签不存在，则创建并返回新地址）
     * @param $label
     * @return string | false
     * @author xym Created at 2014-4-21
     */
    public function getAccountAddress($label){
        $address = $this->obj->getaccountaddress($label);
        return $address;
    }
    
    /**
     * wallet
     * 列出标签的所有地址（标签不存在，则返回空数组）
     * @param $label
     * @return array | false
     * @author xym Created at 2014-4-21
     */
    public function getAddressesByAccount($label){
        $addressArr = $this->obj->getaddressesbyaccount($label);
        return $addressArr;
    }
    
    /**
     * block
     * 获取块区总数
     * @return int | false
     * @author xym Created at 2014-4-21
     */
    public function getBlockCount() {
        $count = $this->obj->getblockcount();
        return $count;
    }
    
    /**
     * 返回块区链中指定索引的hash值，0表示创世块
     * @param $index
     * @return string | false
     * @author xym Created at 2014-4-21
     */
    public function getBlockHash($index) {
        $hash = $this->obj->getblockhash($index);
        return $hash;
    }
    
    /**
     * 获取最长块区链里最好块区的hash值
     * @return string | false
     * @author xym Created at 2014-4-21
     */
    public function getBestBlockHash() {
        $hash = $this->obj->getbestblockhash();
        return $hash;
    }
    
    /**
     * 获取指定区块的信息
     * @param $hash
     * @return array | false
     *
     * Array
     *   (
     *       [hash] => 56215f017e53a949ab09157cda45a3601247e4e998c57ad3752189bd3cbaaadc
     *       [confirmations] => 2               //确认次数
     *       [strippedsize] => 1539             //
     *       [size] => 1539                     //大小（Bytes）
     *       [weight] => 1140,                  //
     *       [height] => 552887                 //高度
     *       [version] => 2                     //版本
     *       [versionHex] => "00000001"         //版本，16进制
     *       [merkleroot] => d262e12d7af03a0a4f2051c4fa36993bc265f820bdc13135f0f0a7244fff7f53
     *       [tx] => Array                      //区块包含的交易
     *           (
     *               [0] => 8565ba322e6734c6b975c7f489c0c16c0f3a2736bd5ff9ae9754207a4f4f5beb
     *               [1] => f278dc964024050a5180669d6d7bb60ec551fe04ec9b52d4b2d2554bc751984f
     *               .......................................................................
     *           )
     *       [time] => 1398062778               //创建时间
     *       [nonce] => 885450124
     *       [bits] => 1b0cb1d0                 //Bits
     *       [difficulty] => 5162.4393715       //难度
     *       [previousblockhash] => c59fc01fa16ecf61158178a957dd5c6e57697743b940a4dd5b85f7b931b46cdb
     *       [nextblockhash] => 179808430c7b55e5bdf8cd0a054216cd101b87ae829a7838f8c5cd56dfa7d23b
     *   )
     *
     * @author xym Created at 2014-4-21
     */
    public function getBlock($hash) {
        $info = $this->obj->getblock($hash);
        return $info;
    }
    
    /**
     * wallet
     * 将指定标签的指定数量虚拟币转到钱包内的另一个标签内
     * @param $fromaccount		//发送账户标签，默认账户用""
     * @param $toaccount		//接收账户标签，默认账户用""
     * @param $amount			//发送总量
     * @param $minconf			//最小确认次数
     * @param $comment			//附加信息，说明该笔交易用来做什么【不属于交易的内容，仅在自己的钱包内做记录】
     * @return boolean			//true：转移成功，false：转移失败
     * @author xym Created at 2014-4-21
     */
    public function move($fromaccount, $toaccount, $amount, $minconf = 1, $comment = '') {
        if (!empty($comment)) {
            $rs = $this->obj->move($fromaccount, $toaccount, $amount, $minconf);
        }else {
            $rs = $this->obj->move($fromaccount, $toaccount, $amount, $minconf, $comment);
        }
        return $rs;
    }
    
    /**
     * wallet
     * 从指定标签发送虚拟币到指定地址（确保有足够的币，要使用minconf做限制）
     * @param $fromaccount		//发送账户标签，默认账户用""
     * @param $address			//接收虚拟币的地址
     * @param $amount			//发送数量（包括交易手续费）
     * @param $minconf			//发送账户的余额要达到最小确认次数才可以被使用
     * @param $comment			//附加信息，说明该笔交易用来做什么【不属于交易的内容，仅在自己的钱包内做记录】
     * @param $comment_to		//附加信息，说明该笔交易发送给谁或组织【不属于交易的内容，仅在自己的钱包内做记录】
     * @return string | false	//发送成功，则返回交易ID
     * @author xym Created at 2014-4-21
     */
    public function sendFrom($fromaccount, $toaddress, $amount, $minconf = 6, $comment = '', $comment_to = '', $seconds = 30) {
        $this->walletPassPhrase($seconds);
        if (empty($comment) && empty($comment_to)) {
            $txid = $this->obj->sendfrom($fromaccount, $toaddress, $amount, $minconf);
        }elseif (empty($comment_to)) {
            $txid = $this->obj->sendfrom($fromaccount, $toaddress, $amount, $minconf, $comment);
        }else {
            $txid = $this->obj->sendmany($fromaccount, $toaddress, $minconf, $comment, $comment_to);
        }
        return $txid;
    }
    
    /**
     * wallet
     * 发送虚拟币给多个地址
     * @param string $fromaccount		//发送账户标签，默认账户用""
     * @param array $addressArr			//接收虚拟币的地址	格式： array('addr' => 'amount', 'addr' => 'amount', ...);
     * @param int $minconf				//发送账户的余额要达到最小确认次数才可以被使用
     * @param string $comment			//附加信息，说明该笔交易用来做什么【不属于交易的内容，仅在自己的钱包内做记录】
     * @return string | false			//发送成功，则返回交易ID (无论接收地址有多少，一次发送只产生一个交易ID)
     * @author xym Created at 2014-4-21
     */
    public function sendMany($fromaccount, $addressArr, $minconf = 6, $comment = '', $seconds = 30) {
        if (!is_array($addressArr)) {
            $this->obj->error = '接收地址格式错误！';
            return false;
        }
        $addressStr = '';
        foreach ($addressArr as $one) {
            $addressStr .= $one['addr'] .':'. $one['amount'] .',';
        }
        $addressStr = substr($addressStr, 0, -1);
        	
        $this->walletPassPhrase($seconds);
        if (empty($comment)) {
            $txid = $this->obj->sendmany($fromaccount, $addressStr, $minconf);
        }else {
            $txid = $this->obj->sendmany($fromaccount, $addressStr, $minconf, $comment);
        }
        return $txid;
    }
    
    /**
     * wallet
     * 从钱包发送虚拟币到指定地址
     * @param $address			//接收虚拟币的地址
     * @param $amount			//发送数量（包括交易手续费）
     * @param $comment			//附加信息，说明该笔交易用来做什么【不属于交易的内容，仅在自己的钱包内做记录】
     * @param $comment_to		//附加信息，说明该笔交易发送给谁或组织【不属于交易的内容，仅在自己的钱包内做记录】
     * @return string | false	//发送成功，则返回交易ID
     * @author xym Created at 2014-4-21
     */
    public function sendToAddress($address, $amount, $comment = '', $comment_to = '', $seconds = 30) {
        $this->walletPassPhrase($seconds);
        if (empty($comment) && empty($comment_to)) {
            $txid = $this->obj->sendtoaddress($address, $amount);
        }elseif (empty($comment_to)) {
            $txid = $this->obj->sendtoaddress($address, $amount, $comment);
        }else {
            $txid = $this->obj->sendtoaddress($address, $amount, $comment, $comment_to);
        }
        return $txid;
    }
    
    /**
     * wallet
     * 设置手续费
     * @param $txfee	//8位小数
     * @return
     * @author xym Created at 2014-4-25
     */
    public function setTxfee($txfee) {
        $state = $this->obj->settxfee($txfee);
        return $state;
    }
    
    /**
     * wallet
     * 列出各地址还没有花费的虚拟币信息
     * @param int $minconf		//最小确认次数
     * @param int $maxconf		//最大确认次数
     * @param array $addressArr	//指定地址
     * @return array | false
     * @author xym Created at 2014-4-21
     */
    public function listUnspent($minconf = 1, $maxconf = 9999999, $addressArr = array()){
        if (empty($addressArr)) {
            $data = $this->obj->listunspent($minconf, $maxconf);
        }else {
            $data = $this->obj->listunspent($minconf, $maxconf, $addressArr);
        }
        return $data;
    }
    
    /**
     * wallet
     * 创造交易信息（注意：此信息未广播出去的）
     * @param array $unspentArr		//钱包接收到的交易，listunspent $minconf $maxconf $address 可以查看到
     * 格式：
     * array(
     *		array(
     *			"txid"=>"aed23bb3ec7e93d69450d7e5ea49d52fcfbef9d380108f2be8fe14ef705fcea5",
     *			"vout"=>2
     *		),
     *		array(
     *			"txid"=>"b28c740c66726ab2f0397be29f2d25f091b8ab353b98b9ebf9e6ccfd080cdf49",
     *			"vout"=>3
     *		),
     *		...............................................
     * )
     *
     * @param array $addressArr		//接收地址 => 数量
     * 格式：
     * array(
     *		"1GTDT3hYk4x4wzaa9k38pRsHy9SPJ7qPzT"=>0.006,
     *		"1ApD64wpNUM6GBeSmKYhsyaNwFot3FMC5y"=>0.004,
     * )
     *
     * @return string | false	//16进制字符串
     * @author xym Created at 2014-4-25
     */
    public function createRawTransaction($unspentArr, $addressArr){
        $data = $this->obj->createrawtransaction($unspentArr, $addressArr);
        return $data;
    }
    
    /**
     * wallet
     * 结构化的交易数据
     * @param string $hexString		//createrawtransaction得到，或者getrawtransaction得到的
     * @return array | false
     * @author xym Created at 2014-4-25
     */
    public function decodeRawTransaction($hexString) {
        $data = $this->obj->decoderawtransaction($hexString);
        return $data;
    }
    
    /**
     * wallet
     * 交易签名
     * @param string $hexString		//createrawtransaction 生成的16进制字符串(创建的交易信息)
     * @return string $hex | false
     * @author xym Created at 2014-4-25
     */
    public function signRawTransaction($hexString, $seconds = 30, $prevtxs = NULL, $privatekeys = NULL, $sighashtype = NULL) {
        $this->walletPassPhrase($seconds);
        $data = $this->obj->signrawtransaction($hexString);
        return $data;
    }
    
    /**
     * wallet
     * 发送到虚拟币网络
     * @param string $hexString			//createrawtransaction 生成的16进制字符串(创建的交易信息)
     * @param boolean $allowhighfees	//是否允许高额手续费
     * @return string | false			//返回16进制表示的交易hash
     * @author xym Created at 2014-4-25
     */
    public function sendRawTransaction($hexString, $allowhighfees = false) {
        $data = $this->obj->sendrawtransaction($hexString, $allowhighfees);
        return $data;
    }
    
    /**
     * wallet
     * 设置地址标签
     * @param $label
     * @return
     * @author xym Created at 2014-4-21
     */
    public function setAccount($address, $label) {
        $rs = $this->obj->setaccount($address, $label);
        return $rs;
    }
    
    /**
     * wallet
     * 提币（各地址总额不包括矿工费）
     * @param array $addressInfo		//提现地址		array('提币地址1' => 总额1, '提币地址2' => 总额2, ......);
     * @param $coin						//ltc 或者   btc
     * @return $hex						//返回交易的hash值的十六进制串
     * @author xym Created at 2014-4-29
     */
    public function withDrawal($addressInfo, $coin){
        $tmp = $this->listUnspent();
        if ($tmp['success'] === false) return $tmp;
        	
        $total = 0;
        foreach ($addressInfo as $oneAmount) {
            $total += $oneAmount;
        }
        	
        //获取在线钱包总余额
        $salt = 0.1;
        $balance = $this->getBalance('*', 1);
        if ($total + $salt > $balance) {
            return array('success' => false, 'error' => '钱包余额不足，请处理！', 'state_code' => 200);
        }
        	
        $unspentAmount = 0;
        $unspentArr = array();
        $unspent = $tmp['data'];
        foreach ($unspent as $item) {
            $unspentAmount += $item['amount'];
            $unspentArr[] = array('txid' => $item['txid'], 'vout' => $item['vout']);
            if ($unspentAmount > $total + $salt) {
                break;
            }
        }
        	
        //添加余额回收地址
        $tmpAddr = $this->getNewAddress();
        $sysAddress = $tmpAddr['data'];
        $addressInfo = array_merge($addressInfo, array($sysAddress => $unspentAmount - $total - $salt));
        	
        $hexTmp = $this->createRawTransaction($unspentArr, $addressInfo);
        	
        $size = strlen($hexTmp['data']) / 2;
        $ksize = $size / 1000;	//1kb -> 0.001ltc手续费， 0.0001btc矿工费
        if ($coin == 'ltc') {
            if ($ksize < 1) {
                $txfee = 0.001;
            }else $txfee = $size / 1000 * 0.001;
        }else if ($coin == 'btc') {
            if ($ksize < 1) {
                $txfee = 0.0001;
            }else $txfee = $size / 1000 * 0.0001;
        }else return array('success' => false, 'error' => '虚拟币类型有误！', 'state_code' => 200);
        	
        $addressInfo = array_pop($addressInfo);
        $addressInfo = array_merge($addressInfo, array($sysAddress => $unspentAmount - $total - $txfee));
        $finalHex = $this->createRawTransaction($unspentArr, $addressInfo);
        if ($finalHex === false) return array('success' => false, 'error' => '处理出错！', 'state_code' => 404);
        $signData = $this->signRawTransaction($finalHex);
        if ($signData === false) return array('success' => false, 'error' => '处理出错！', 'state_code' => 404);
        $txhashHex = $this->sendRawTransaction($signData);
        if ($txhashHex === false) return array('success' => false, 'error' => '处理出错！', 'state_code' => 404);
        else return array('success' => true, 'error' => '发送成功！', 'state_code' => 200);
    }
    
    /**
     * 填充地址池
     * @param $newsize		//初始化地址池的地址个数，若无配置默认是100
     * @return
     * @author xym Created at 2014-4-30
     */
    public function keyPoolRefill($newsize, $seconds = 30) {
        $this->walletPassPhrase($seconds);
        $rs = $this->obj->keypoolrefill($newsize);
        return $rs;
    }
    
    /**
     * 检查执行结果是否正确
     * @param $data			//执行失败时，为false
     * @return array
     * @author xym Created at 2014-4-17
     */
    private function checkState($data){
        $rs = array('success' => false, 'error' => $this->obj->error, 'state_code' => $this->obj->status);
        if ($data !== false) {
            $rs = array('success' => true, 'error' => $this->obj->error, 'state_code' => $this->obj->status, 'data' => $data);
        }
        return $rs;
    }
}