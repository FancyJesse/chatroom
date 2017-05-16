<?php
	if(!isset($_POST['last_message_time'])){
		echo false;
		exit();
	}
	require_once('MySqlSessionHandler.php');
	$last_message_time = $_POST['last_message_time'];
	$handler = new MySqlSessionHandler();
	$handler->connect();
	if($last_message_time==0){
		$res =  $handler->get_chatroom_history();
	}
	else{
		$res = $handler->get_chatroom_update($last_message_time);
	}
	$arr = array();
	while($row = $res->fetch_array(MYSQL_ASSOC)) {
		$arr[] = $row;
	}
	echo json_encode($arr);
?>
