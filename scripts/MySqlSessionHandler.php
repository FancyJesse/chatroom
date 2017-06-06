<?php
class MySqlSessionHandler{

	protected $DB_CONN;
	private $host = '';
	private $user = '';
	private $secret = '';
	private $schema = '';

	public function connect(){
		$this->DB_CONN = new mysqli($this->host, $this->user, $this->secret, $this->schema);
		return !$this->DB_CONN->connect_errno;
	}

	public function close(){
		return $this->DB_CONN->close();
	}

	public function init_tables(){
		$user_query = "CREATE TABLE IF NOT EXISTS `user` (
			`user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`username` varchar(15) NOT NULL,
			`secret` binary(60) NOT NULL,
			`date_created` datetime NOT NULL,
			`last_login` datetime NOT NULL,
			PRIMARY KEY (`user_id`),
			UNIQUE KEY `username_UNIQUE` (`username`)
			)";
		$chatroom_query = "CREATE TABLE IF NOT EXISTS `chatroom` (
			`message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` int(11) NOT NULL,
			`message` varchar(255) NOT NULL,
			`time` datetime NOT NULL,
			PRIMARY KEY (`message_id`)
			)";
		$this->DB_CONN->query($user_query);
		$this->DB_CONN->query($chatroom_query);
	}

	public function user_info($username){
		$query = 'SELECT * From user WHERE username=?';
		$stmt = $this->DB_CONN->prepare($query);
		$stmt->bind_param('s', $username);
		$stmt->execute();
		return $stmt->get_result();
	}

	public function user_register($username, $secret){
		$user_info = $this->user_info($username)->fetch_array(MYSQL_ASSOC);
		if(!$user_info){
			$query =
				'INSERT INTO user (username, secret, date_created, last_login)
				VALUES (?, ?, NOW(), NOW())';
			$stmt = $this->DB_CONN->prepare($query);
			$stmt->bind_param('ss', $username, password_hash($secret, PASSWORD_BCRYPT));
			$stmt->execute();
			$user_info = $this->user_info($username)->fetch_array(MYSQL_ASSOC);
			unset($user_info['secret']);
			return $user_info;
		}
		return false;
	}

	public function user_login($username, $secret){
		$user_info = $this->user_info($username)->fetch_array(MYSQL_ASSOC);
		if(password_verify($secret, $user_info['secret'])){
			$query = 'UPDATE user SET last_login=NOW() WHERE username=?';
			$stmt = $this->DB_CONN->prepare($query);
			$stmt->bind_param('s', $username);
			$stmt->execute();
			unset($user_info['secret']);
			return $user_info;
		}
		return false;
	}

	public function get_chatroom_history(){
		$query =
			'SELECT message_id, chatroom.user_id, username, message, time
        	        FROM chatroom
                	LEFT JOIN user ON user.user_id=chatroom.user_id
                	ORDER BY time ASC LIMIT 50';
		return $this->DB_CONN->query($query);
	}

	public function get_chatroom_update($last_message_time){
		$query =
			'SELECT message_id, chatroom.user_id, username, message, time
        	        FROM chatroom
        	        LEFT JOIN user ON user.user_id=chatroom.user_id
			WHERE time > ?
                	ORDER BY time';
			$stmt = $this->DB_CONN->prepare($query);
			$stmt->bind_param('s', $last_message_time);
			$stmt->execute();
		return $stmt->get_result();
	}

	public function send_chatroom_message($user_id, $message){
		$message = trim($message);
		if($message == ''){
			return false;
		}
		$query = 'INSERT INTO chatroom (user_id, message, time) VALUES (?, ?, NOW())';
		$stmt = $this->DB_CONN->prepare($query);
		$success = $stmt->bind_param('is', $user_id, $message);
		if($success){
			$success = $stmt->execute();
		}
		return $success;
	}

}
?>
