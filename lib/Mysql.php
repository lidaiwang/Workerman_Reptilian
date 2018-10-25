<?php
	class Mysql {
		
		public static $Instance = null;
		
		private $tableName	= null;				//当前操作表
		private $db 		= null;				//PDO连接对象
		private $sql 		= null;				//SQL语句
		private $result 	= null;				//上一次查询结果
		private $state		= null; 			//上一次SQL执行状态
		private $lastId		= null;				//上一次插入记录返回的主键值
		
		public static function _getInstance($tableName = ""){
			if (self::$Instance === null){
				self::$Instance = new self();
			}
			self::$Instance->selectTable($tableName);
			return self::$Instance;
		}
		
		private function __construct(){
			$this->connnet();
		}
		
		/**
		 * 切换表
		 * @param string $table
		 * @return Mysql
		 * @author Ymj Create at 2013-12-14
		 */
		public function selectTable($tableName){
			if (!empty($tableName)) $this->tableName = $tableName;
			return self::$Instance;
		}
		
		/**
		 * 连接PDO
		 * @param
		 * @return Mysql
		 * @author Ymj Create at 2013-12-15
		 */
		public function connnet($conf = array()){
			empty($conf) && $conf = DBCONF['db1'];
			$dsn = 'mysql:host='. $conf['host'] .';port='. $conf['port'] .';dbname='. $conf['database'];
			try {
				$this->db = new PDO($dsn, $conf['user'], $conf['password'], array(PDO::ATTR_PERSISTENT => false));
				//返回字段名总是小写
				$this->db->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
				//设置编码
				$this->db->query("set names utf8");
			} catch (PDOException $e){
				die("数据库连接出错！<br />" . $e->getMessage());
			}
			return self::$Instance;
		}
		
		/**
		 * 关闭PDO连接
		 * @param
		 * @return Mysql
		 * @author Ymj Create at 2013-12-15
		 */
		public function close(){
			$this->db = null;
			self::$Instance = null;
			return self::$Instance;
		}
		
		/**
		 * 返回PDO对象
		 * @param
		 * @return PDO
		 * @author Ymj Create at 2013-12-15
		 */
		public function getPDO(){
			return $this->db;
		}
		
		/**
		 * 组装sql
		 * @param string $sql
		 * @param bool	 $append	//是否采用追加模式
		 * @return Mysql
		 * @author Ymj Create at 2013-12-15
		 */
		public function sql($sql, $append = FALSE){
			if ($append && $this->sql != null){
				$this->sql .= $sql;
			}
			else $this->sql = $sql;
			return self::$Instance;
		}
		
		/**
		 * 生成查询SQL头部
		 * @param array $field
		 * @return Mysql
		 * @author Ymj Create at 2013-12-15
		 */
		public function sqlHead($field = array(), $tableName = ''){
			if ($tableName == '') $tableName = $this->tableName;
			$sql = "SELECT ";
			if (empty($field))
				$sql .= "*";
			else if(is_array($field)){
				foreach ($field as $f => $as){
					if(is_numeric($f)){
						$sql .= "`$as`, ";
					}else{
						$sql .= "`$f` AS `$as`, ";
					}
				}
				$sql = substr($sql, 0, strlen($sql) - 2);
			}else if(is_string($field)){
				$sql .= $field;
			}
			$sql .= " FROM `" . $tableName . "` ";
			$this->sql = $sql;
			return self::$Instance;
		}
		
		/**
		 * 设置查询语句的where子语句
		 * @param array $where
		 * @return string 空字符串 || where子语句
		 * @author Li.hq Create at 2013-07-17
		 * 		   Modified by Ymj at 2013-12-15
		 */
		public function where($where = array()){
			if ($this->sql != null){
				$sql = '';
				if(!empty($where)){
					$sql .= ' WHERE';
					if(is_array($where)){
						foreach ($where as $f => $v){
							if(is_numeric($f)) continue;
							if(1 == preg_match('/^!=/', $v)){
								$sql .= ' `' . $f . '`!="' . substr($v, 2) . '" and';
							}else{
								$sql .= ' `' . $f . '`="' . $v . '" and';
							}
						}
						$sql = substr($sql, 0, strlen($sql) -3);
					}else if(is_string($where)){
						$sql .= ' ' . $where;
					}
				}
				$this->sql .= $sql;
			}
			return self::$Instance;
		}
		
		/**
		 * 查询多条
		 * @param $type
		 * @return Mysql 
		 * @author Ymj Create at 2013-12-15
		 */
		public function query($type = PDO::FETCH_ASSOC){
			$stmt = $this->db->query($this->sql);
			$this->result = $stmt->fetchAll($type);
			return self::$Instance;
		}
		
		/**
		 * 查询单条
		 * @param $type
		 * @return Mysql
		 * @author Ymj Create at 2013-12-15
		 */
		public function queryOne($type = PDO::FETCH_ASSOC){
			$stmt = $this->db->query($this->sql);
			$this->result = $stmt->fetch($type);
			return self::$Instance;
		}
		
		/**
		 * 执行UPDATE,DELETE,INSERT操作
		 * @param string $sql
		 * @return boolean || int
		 * @author Ymj Created At 2012-12-8
		 */
		public function exec($sql = ""){
			!empty($sql) && $this->sql = $sql;
			$this->state = !!$this->db->exec($this->sql);
			return self::$Instance;
		}
		
		/**
		 * 更新记录
		 * @param array $record
		 * @return boolean
		 * @author Ymj Created At 2012-12-8
		 */
		public function update($record){
			if (empty($record['id']) || !is_numeric($record['id'])) return FALSE;
			$sql = "UPDATE `" . $this->tableName . "` SET ";
			foreach ($record as $key => $val){
				if ($key != 'id')
					$sql .= "`" . $key . "` = '" . addslashes($val) . "',";
			}
			$sql = substr($sql, 0, -1);
			$sql .= " WHERE `id` = '" . $record['id'] . "'";
			$this->sql = $sql;
			return $this->exec();
		}
		
		/**
		 * 插入记录
		 * @param array $record
		 * @return boolean || int
		 * @author Ymj Created At 2012-12-8
		 */
		public function insert($record){
			$sql = "INSERT INTO `" . $this->tableName . "` SET ";
			foreach ($record as $key => $val){
				if ($key != 'id')
					$sql .= "`" . $key . "` = '" . addslashes($val) . "',";
			}
			$sql = substr($sql, 0, -1);
			$this->sql = $sql;
			$this->exec();
			if ($this->state)
				$this->lastId = $this->db->lastInsertId();
			return self::$Instance;
		}
		
		/**
		 * 返回全部状态
		 * @param
		 * @return array
		 * @author Ymj Create at 2013-12-15
		 */
		public function getAll(){
			return array(
				'sql' 		=> $this->sql,
				'result' 	=> $this->result,
				'state' 	=> $this->state,
				'lastId' 	=> $this->lastId
			);
		}
		
		/**
		 * 返回上一次执行的SQL
		 * @param
		 * @return string
		 * @author Ymj Create at 2013-12-15
		 */
		public function getSql(){
			return $this->sql;
		}
		
		
		/**
		 * 返回查询结果
		 * @param 
		 * @return array 
		 * @author Ymj Create at 2013-12-15
		 */
		public function getResult(){
			return $this->result;
		}
		
		/**
		 * 返回查询得到的总数
		 * @param
		 * @return 
		 * @author Ymj Create at 2014-1-15
		 */
		public function getCount(){
			if ($this->result == null) return 0;
			if (!isset($this->result['count'])) return 0;
			return (int)$this->result['count'];
		}
		
		/**
		 * 返回执行状态
		 * @param
		 * @return boolean
		 * @author Ymj Create at 2013-12-15
		 */
		public function getState(){
			return $this->state;
		}
		
		/**
		 * 返回最后插入记录主键值
		 * @return int
		 * @author Ymj Create at 2013-12-15
		 */
		public function getLastId(){
			return $this->lastId;
		}
		
		/**
		 * 清空上一次的执行状态及数据
		 * @param
		 * @return Mysql
		 * @author Ymj Create at 2013-12-15
		 */
		public function clean(){
			$this->sql = null;
			$this->result = null;
			$this->state = null;
			$this->lastId = null;
			return self::$Instance;
		}
	}