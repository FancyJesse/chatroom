<?php
	if(empty($_POST['user_id'])||empty($_POST['message'])){
		echo false;
		exit();
	}
	require_once('MySqlSessionHandler.php');
	$handler = new MySqlSessionHandler();
	$success = $handler->connect();
	if($success){
		$success = $handler->send_chatroom_message($_POST['user_id'], $_POST['message']);
	}
	echo $success;
?>
