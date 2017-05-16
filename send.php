<?php
	if(empty($_POST['user_id'])||empty($_POST['message'])){
		header("Location: https://www.fancyjesse.com/projects/chatroom");
		exit();
	}
	require_once('/home/website/MySqlSessionHandler.php');
	$handler = new MySqlSessionHandler();
	$success = $handler->connect();
	if($success){
		$success = $handler->send_chatroom_message($_POST['user_id'], $_POST['message']);
	}
	echo $success;
?>
