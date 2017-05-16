<?php
class MySqlSessionHandler{

	protected $DB_CONN;

	public function connect($host, $user, $secret, $schema){
		$this->DB_CONN = new mysqli($host, $user, $secret, $schema);
		return !$this->DB_CONN->connect_errno;
	}

	public function close(){
		return $this->DB_CONN->close();
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
